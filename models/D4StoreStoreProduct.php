<?php

namespace d4yii2\d4store\models;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3product\dictionaries\D3productUnitDictionary;
use d3system\dictionaries\SysModelsDictionary;
use d4yii2\d4store\models\base\D4StoreStoreProduct as BaseD4StoreStoreProduct;
use Throwable;
use Yii;
use yii\base\UserException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\helpers\Json;
use yii\web\HttpException;

/**
 * This is the model class for table "d4store_store_product".
 *
 * @property-read D4StoreAction|null $storeActionActiveOne
 * @property-read string|null $productCode
 * @property-read null|string $unitLabel
 */
class D4StoreStoreProduct extends BaseD4StoreStoreProduct
{

    /**
     * @param string $modelClassName
     * @param int $modelId
     * @return static
     * @throws D3ActiveRecordException
     */
    public static function findByActionRef(string $modelClassName, int $modelId): ?self
    {
        return self::find()
            ->innerJoin(
                'd4store_action',
                'd4store_action.store_product_id = d4store_store_product.id'
            )
            ->innerJoin(
                'd4store_action_ref',
                'd4store_action.id = d4store_action_ref.action_id'
            )
            ->where([
                'd4store_action_ref.model_record_id' => $modelId,
                'd4store_action_ref.model_id' => SysModelsDictionary::getIdByClassName($modelClassName)
            ])
            ->one();
    }

    /**
     * @throws D3ActiveRecordException
     */
    public static function findByAction(string $modelClassName, int $modelId)
    {
        return self::find()
            ->innerJoin(
                'd4store_action',
                'd4store_action.store_product_id = d4store_store_product.id'
            )
            ->where([
                'd4store_action.ref_model_record_id' => $modelId,
                'd4store_action.ref_model_id' => SysModelsDictionary::getIdByClassName($modelClassName)
            ])
            ->one();
    }

    /**
     * @param int $rkInvoiceProductId
     * @param string $className
     * @throws D3ActiveRecordException
     * @throws UserException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public static function deleteByRkInvoiceProductId(int $rkInvoiceProductId, string $className): void
    {
        $rkInvoiceProductSysModelId = SysModelsDictionary::getIdByClassName($className);
        $actionRef = D4StoreActionRef::findOne([
                'model_record_id' => $rkInvoiceProductId,
                'model_id' => $rkInvoiceProductSysModelId
            ]);

        /** no found action ref */
        if (!$actionRef) {
            return;
        }
        $action = $actionRef->action;
        $storeProduct = $action->storeProduct;
        $action->delete();
        $storeProduct->delete();
    }

    public function delete()
    {
        $this->canDelete();
        foreach ($this->d4StorePackProductHistories as $d4StorePackProductHistory)  {
            $d4StorePackProductHistory->delete();
        }
        foreach ($this->d4StorePackProducts as $d4StorePackProduct) {
            $pack = $d4StorePackProduct->pack;
            $d4StorePackProduct->realDelete();
            if ($pack) {
                $pack->delete();
            }
        }
        return parent::delete();
    }

    /**
     * @throws UserException
     */
    public function canDelete(): void
    {
        if ($this->reserved_qnt > 0) {
            $message = [
                'message' => Yii::t('d4store',
                    'Cannot delete "{productName}" from store with reserved quantity.',
                    [
                        'productName' => $this->product->name,
                    ]
                ),
                'ExternalLink' => [
                    'text' => Yii::t('d4store','Store Product Card'),
                    'url' => [
                        '/d4storei/store-product/view',
                        'id' => $this->id,
                    ]
                ]
            ];
            throw new UserException(Json::encode($message));
        }

        if ($this->qnt !==  $this->remain_qnt) {
            $message = [
                'message' => Yii::t('d4store',
                    'Cannot delete already used product "{productName}" from store.',
                    [
                        'productName' => $this->product->name,
                    ]
                ),
                'ExternalLink' => [
                    'text' => Yii::t('d4store','Store Product Card'),
                    'url' => [
                        '/d4storei/store-product/view',
                        'id' => $this->id,
                    ]
                ]
            ];
            throw new UserException(Json::encode($message));
        }
    }

    /**
     * @throws D3ActiveRecordException
     */
    public function getModelIdFromRef(string $modelClassName): ?int
    {
        return $this
            ->getD4StoreActions()
            ->select(['d4store_action_ref.model_record_id'])
            ->innerJoin(
                'd4store_action_ref',
                'd4store_action.id = d4store_action_ref.action_id'
            )
            ->where([
                'd4store_action_ref.model_id' => SysModelsDictionary::getIdByClassName($modelClassName)
            ])
            ->scalar();
    }

    /**
     * @throws D3ActiveRecordException
     */
    public function getModelIdFromBaseRef(string $modelClassName): ?int
    {
        return $this
            ->getD4StoreActions()
            ->select(['d4store_action.ref_model_record_id'])
            ->where([
                'd4store_action.ref_model_id' => SysModelsDictionary::getIdByClassName($modelClassName)
            ])
            ->scalar();
    }


    /**
     * @param int $id
     * @return self|null
     * @throws HttpException
     */
    public static function findForController(int $id): ?D4StoreStoreProduct
    {
        $model = self::findOne($id);
        if (!$model) {
            throw new HttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * @return D4StoreAction|null
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function getStoreActionActiveOne(): ?D4StoreAction
    {
        return $this
            ->getD4StoreActions()
            ->andWhere([
                'type' => D4StoreAction::STORE_ACTION_TYPES,
                'is_active' => D4StoreAction::IS_ACTIVE_YES
            ])
            ->one();
    }

    /**
     * @throws \yii\base\Exception
     */
    public function convertRemainQntToUnit(int $toUnitId): ?float
    {
        return $this->product->unitConvertFromTo($this->remain_qnt, $this->product->unit_id, $toUnitId);
    }

    /**
     * @throws Exception
     */
    public function getUnitLabel(): ?string
    {
        return D3productUnitDictionary::getLabel(Yii::$app->SysCmp->getActiveCompanyId(), $this->product->unit_id);
    }

    /**
     * @param int $refTypeId
     * @param int $recordId
     * @return void
     * @throws D3ActiveRecordException|Exception
     */
    public function addRef(int $refTypeId, int $recordId): void
    {
        $ref = new D4StoreProductRef();
        $ref->store_product_id = $this->id;
        $ref->model_record_id = $recordId;
        $ref->ref_type_id = $refTypeId;
        if (!$ref->save()) {
            throw new D3ActiveRecordException($ref);
        }
    }

    /**
     * @throws D3ActiveRecordException
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$this->code) {
            $this->code = Yii::$app->storeProductRecorder->getCodeOrCreate($this->id);
            $this->save();
        }
    }

    /**
     * @depracated velāk jāizmet ārā, jo code ģenere afterSave())
     * @throws D3ActiveRecordException
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function getProductCode(): ?string
    {
        if ($this->code) {
            return $this->code;
        }
        if ($this->isNewRecord) {
            return null;
        }
        $this->code = Yii::$app->storeProductRecorder->getCodeOrCreate($this->id);
        if ($model = self::findOne($this->id)) {
            $model->code = $this->code;
            $model->save();
        }
        return $this->code;
    }

    /**
     * find store product ref model record id for action
     * @throws D3ActiveRecordException
     */
    public function findRefModelRecordId( string $actionType, string $modelClassName): ?int
    {
        return
            D4StoreActionRef::find()
                ->select('d4store_action_ref.model_record_id')
            ->innerJoin(
                'd4store_action',
                'd4store_action.id = d4store_action_ref.action_id'
            )
            ->where([
                'd4store_action.store_product_id' => $this->id,
                'd4store_action.type' => $actionType,
                'd4store_action_ref.model_id' => SysModelsDictionary::getIdByClassName($modelClassName)
            ])
            ->scalar();
    }
}

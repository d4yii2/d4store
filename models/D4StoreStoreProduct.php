<?php

namespace d4yii2\d4store\models;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3product\dictionaries\D3productUnitDictionary;
use d3system\dictionaries\SysModelsDictionary;
use d4yii2\d4store\models\base\D4StoreStoreProduct as BaseD4StoreStoreProduct;
use Yii;
use yii\web\HttpException;

/**
 * This is the model class for table "d4store_store_product".
 */
class D4StoreStoreProduct extends BaseD4StoreStoreProduct
{

    /**
     * @param string $modelClassName
     * @param int $modelId
     * @return static
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public static function findByActionRef(string $modelClassName, int $modelId): self
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
     * @throws \d3system\exceptions\D3ActiveRecordException
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
     * @throws \d3system\exceptions\D3ActiveRecordException
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
     * @throws \yii\web\HttpException
     * @return self|null
     */
    public static function findForController(int $id)
    {
        $model = self::findOne($id);
        if (!$model) {
            throw new HttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * @return \d4yii2\d4store\models\D4StoreAction|null
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

    public function getUnitLabel(): ?string
    {
        return D3productUnitDictionary::getLabel(Yii::$app->SysCmp->getActiveCompanyId(), $this->product->unit_id);
    }

    /**
     * @param int $refTypeId
     * @param int $recordId
     * @return void
     * @throws \d3system\exceptions\D3ActiveRecordException
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
}

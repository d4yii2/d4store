<?php

namespace d4yii2\d4store\models;

use d3system\dictionaries\SysModelsDictionary;
use \d4yii2\d4store\models\base\D4StoreStoreProduct as BaseD4StoreStoreProduct;
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
     * @return array|\d4yii2\d4store\models\D4StoreAction|\yii\db\ActiveRecord|null
     */
    public function getStoreActionActiveOne()
    {
        return $this
            ->getD4StoreActions()
            ->andWhere([
                'type' => [
                    D4StoreAction::TYPE_IN,
                    D4StoreAction::TYPE_MOVE,
                    D4StoreAction::TYPE_FROM_PROCESS,
                ],
                'is_active' => D4StoreAction::IS_ACTIVE_YES
            ])
            ->one();
    }
}

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
     * @throws \yii\web\HttpException
     */
    public static function findForController(int $id): self
    {
        $model = self::findOne($id);
        if (!$model) {
            throw new HttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }
}

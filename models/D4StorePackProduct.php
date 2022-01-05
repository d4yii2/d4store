<?php

namespace d4yii2\d4store\models;

use d4yii2\d4store\models\base\D4StorePackProduct as BaseD4StorePackProduct;
use Yii;

/**
 * This is the model class for table "d4store_pack_product".
 */
class D4StorePackProduct extends BaseD4StorePackProduct
{
    public function delete()
    {
        $history = new D4StorePackProductHistory();
        $history->pack_product_id = $this->id;
        $history->store_product_id = $this->store_product_id;
        $history->pack_id = $this->pack_id;
        $history->setActionRemoved();
        $history->time = date('Y-m-d H:i:s');
        if (Yii::$app->has('user') && ($userId = Yii::$app->user-$this->id)) {
            $history->user = $userId;
        }

        return parent::delete();
    }
}

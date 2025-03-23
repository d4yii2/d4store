<?php

namespace d4yii2\d4store\models;

use d4yii2\d4store\models\base\D4StoreLists as BaseD4StoreLists;
use d4yii2\d4store\models\forms\ProductList;

/**
 * This is the model class for table "d4store_lists".
 */
class D4StoreLists extends BaseD4StoreLists
{

    /**
     * @return ProductList[]
     */
    public function loadList($cache): array
    {

        return ProductList::loadList($cache, $this->createListCacheKey());
    }

    public function createListCacheKey(): string
    {
        return 'D4StoreLists-' . $this->id;
    }
}


<?php

namespace d4yii2\d4store\dictionaries;

use d4yii2\d4store\models\D4StoreStore;
use Yii;
use yii\helpers\ArrayHelper;

class D4StoreStoreDictionary
{

    private const CACHE_KEY_LIST = 'D4StoreStoreDictionaryList';

    public static function getList(int $companyId): array
    {
        return Yii::$app->cache->getOrSet(
            self::createCacheKey($companyId),
            static function () use ($companyId) {
                return ArrayHelper::map(
                    D4StoreStore::find()
                        ->select([
                            'id' => '`d4store_store`.`id`',
                            'name' => '`d4store_store`.`name`',
                            //'name' => 'CONCAT(code,\' \',name)'
                        ])
                        ->where(['d4store_store.company_id' => $companyId])
                        ->orderBy([
                            '`d4store_store`.`name`' => SORT_ASC,
                        ])
                        ->asArray()
                        ->all(),
                    'id',
                    'name'
                );
            },
            60 * 60
        );
    }

    private static function createCacheKey($companyId): string
    {
        return self::CACHE_KEY_LIST . '-' . $companyId;
    }

    /**
     * get label
     * @param int $companyId
     * @param int $id
     * @return string|null
     */
    public static function getLabel(int $companyId, int $id): ?string
    {
        return self::getList($companyId)[$id] ?? null;
    }

    public static function clearCache(): void
    {
        foreach (D4StoreStore::find()
                     ->distinct()
                     ->select('company_id')
                     ->column() as $companyId) {
            Yii::$app->cache->delete(self::createCacheKey($companyId));
        }
    }
}

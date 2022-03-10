<?php

namespace d4yii2\d4store\dictionaries;

use d4yii2\d4store\models\D4StoreStack;
use d4yii2\d4store\models\D4StoreStore;
use Yii;
use yii\helpers\ArrayHelper;

class D4StoreStackDictionary
{

    private const CACHE_KEY_LIST = 'D4StoreStackDictionaryListA';

    public static function getList(int $companyId): array
    {
        return Yii::$app->cache->getOrSet(
            self::createCacheKey($companyId),
            static function () use ($companyId) {
                return ArrayHelper::map(
                    D4StoreStack::find()
                        ->select([
                            'id' => '`d4store_stack`.`id`',
                            'name' => 'CONCAT(d4store_store.name, \' - \',  `d4store_stack`.`name`)',
                            //'name' => 'CONCAT(code,\' \',name)'
                        ])
                        ->innerJoin(
                            'd4store_store',
                            'd4store_store.id = d4store_stack.store_id'
                        )
                        ->where(['d4store_store.company_id' => $companyId])
                        ->orderBy([
                            '`d4store_store`.`name`' => SORT_ASC,
                            '`d4store_stack`.`name`' => SORT_ASC,
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

    public static function getFullList(): array
    {
        return Yii::$app->cache->getOrSet(
            self::createCacheKey('full'),
            static function () {
                return ArrayHelper::map(
                    D4StoreStack::find()
                        ->select([
                            'id' => '`d4store_stack`.`id`',
                            'name' => 'CONCAT(d4store_store.name, \' \',  `d4store_stack`.`name`)',
                            //'name' => 'CONCAT(code,\' \',name)'
                        ])
                        ->innerJoin(
                            'd4store_store',
                            'd4store_store.id = d4store_stack.store_id'
                        )
                        ->asArray()
                        ->all(),
                    'id',
                    'name'
                );
            },
            60 * 60
        );
    }


    /**
     * get label
     * @param int $companyId
     * @param int $id
     * @return string|null
     * @deprecated use getLabel2
     */
    public static function getLabel(int $companyId, int $id): ?string
    {
        return self::getList($companyId)[$id] ?? null;
    }

    public static function getLabel2(int $id): ?string
    {
        return self::getFullList()[$id] ?? null;
    }

    private static function createCacheKey($companyId): string
    {
        return self::CACHE_KEY_LIST . '-' . $companyId;
    }

    public static function clearCache(): void
    {
        Yii::$app->cache->delete(self::createCacheKey('full'));
        foreach (D4StoreStore::find()
                     ->distinct()
                     ->select('company_id')
                     ->column() as $companyId) {
            Yii::$app->cache->delete(self::createCacheKey($companyId));
        }
    }
}

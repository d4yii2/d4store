<?php

namespace d4yii2\d4store\dictionaries;

use d3yii2\d3product\models\D3productGroup;
use d3yii2\d3product\models\D3productProduct;
use d3yii2\d3product\models\D3productProductGroup;
use d3yii2\d3product\models\D3productProductType;
use d4yii2\d4store\models\D4StoreAction;
use d4yii2\d4store\models\D4StoreStoreProduct;
use yii\helpers\StringHelper;

class D4StoreProductDictionary
{
    public static function searchByGroupTypeName(int $sysCompanyId, string $q = ''): array
    {

        if (!$q = trim($q)) {
            return [];
        }

        [$group, $type, $productCode] = array_pad(explode('/', $q, 3), 3, '');

        if (!$type && !$productCode && $group !== '*') {
            $groupNames = D3productGroup::find()
                ->select([
                    'name'
                ])
                ->where([
                    'sys_company_id' => $sysCompanyId
                ])
                ->andWhere(['LIKE', 'name', $group])
                ->orderBy(['name' => SORT_ASC])
                ->limit(20)
                ->column();
            $returnList = [];
            foreach ($groupNames as $key => $name) {
                $returnList[] = [
                    'id' => $key,
                    'text' => $name
                ];
            }
            return $returnList;
        }
        $storeProductTable = D4StoreStoreProduct::tableName();
        $productTable = D3productProduct::tableName();
        $storeActionTable = D4StoreAction::tableName();
        $query = D3productGroup::find()
            ->select([
                'id' => $storeProductTable . '.id',
                'groupName' => D3productGroup::tableName() . '.name',
                'productType' => D3productProductType::tableName() . '.name',
                'productName' => 'CONCAT( 
                    ' . $productTable . '.name,
                    \'|\',
                    ' . $storeActionTable . '.stack_id,
                    \'|\',
                    ' . $storeProductTable . '.remain_qnt - ' . $storeProductTable . '.reserved_qnt
                )'
            ])
            ->distinct()
            ->innerJoin(
                D3productProductGroup::tableName(),
                D3productProductGroup::tableName() . '.group_id = ' . D3productGroup::tableName() . '.id'
            )
            ->innerJoin(
                $productTable,
                D3productProductGroup::tableName() . '.product_id = ' . $productTable . '.id'
            )
            ->innerJoin(
                D3productProductType::tableName(),
                D3productProductType::tableName() . '.id = ' . $productTable . '.product_type_id'
            )
            ->innerJoin(
                $storeProductTable,
                $storeProductTable . '.product_id = ' . $productTable . '.id'
            )
            ->innerJoin(
                $storeActionTable,
                $storeActionTable . '.store_product_id = ' . $storeProductTable . '.id
                '
            )
            ->andWhere($storeProductTable . '.remain_qnt - ' . $storeProductTable . '.reserved_qnt > 0')
            ->andWhere([
                $storeActionTable . '.type' => D4StoreAction::STORE_ACTION_TYPES,
                $storeActionTable . '.is_active' => D4StoreAction::IS_ACTIVE_YES
            ])
            ->orderBy([
                'groupName' => SORT_ASC,
                'productType' => SORT_ASC,
            ])
            ->limit(20);


        if ($group && $group !== '*') {
            $query->andWhere(['LIKE', D3productGroup::tableName() . '.name', $group]);
        }
        if ($type && $type !== '*') {
            $query->andWhere(['LIKE', D3productProductType::tableName() . '.name', $type]);
        }
        if ($productCode && $productCode !== '*') {
            $productSearch = implode('%', StringHelper::explode($productCode, ' '));
            $query
                //->addSelect(['productName' => $productTable . '.name'])
                ->andWhere($productTable . '.name LIKE \'%' . $productSearch . '%\'')
                ->addOrderBy(['productName' => SORT_ASC]);
        }
        $returnList = [];
        foreach ($query->asArray()->all() as $res) {
            $displayName = [
                $res['groupName'],
                $res['productType']
            ];

            if (isset($res['productName'])) {
                [$productName, $stackId, $qnt] = explode('|', $res['productName']);
                $displayName[] = $productName
                    . ' ' . (D4StoreStackDictionary::getFullList()[$stackId] ?? '??')
                    . ' ' . $qnt;
            }
            $returnList[] = [
                'id' => $res['id'],
                'text' => implode('/', $displayName)
            ];
        }
        return $returnList;
    }
}
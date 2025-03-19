<?php

namespace d4yii2\d4store\models\forms;

use d4yii2\d4store\models\D4StoreStoreProduct;
use Yii;
use yii\base\Model;
use yii\caching\FileCache;

/**
 * Class ProductList
 *
 * Represents a list of products with functionality for validation, loading, and caching.
 * use in forms for scanning codes and entering quantity
 */
class ProductList extends Model
{

    /**
     * FORM FIELDS
     */
    public ?string $productCode = null;
    public ?string $productQnt = null;

    /**
     *  variables
     */
    public ?string $cacheKey = null;
    public ?object $storeProduct = null;

    public function rules(): array
    {
        return [
            [['productCode', 'productQnt'], 'required'],
            [['productCode'], 'string'],
            [['productQnt'], 'number'],
            [['productCode'], 'validateProductCode'],
        ];
    }

    public function validateProductCode($attribute): void
    {
        if (!$this->storeProduct) {
            $this->addError($attribute, Yii::t('d4store', 'Product not found'));
        }
        if (get_class($this->storeProduct) !== D4StoreStoreProduct::class) {
            $this->storeProduct = null;
            $this->addError($attribute, Yii::t('d4store', 'Product not found'));
        }
    }
    public function attributeLabels(): array
    {
        return [
            'productCode' => Yii::t('d4store', 'Code'),
            'productQnt' => Yii::t('d4store', 'Quantity'),
        ];
    }

    public function load($data, $formName = null): bool
    {
        if (!parent::load($data, $formName)) {
            return false;
        }
        if ($this->productCode) {
            $this->storeProduct = Yii::$app->codeReader->findModel($this->productCode);
        }
        return true;
    }

    public function addToList(FileCache  $cache): bool
    {
        if (!$this->validate()) {
            return false;
        }
        $list = self::loadList($cache, $this->cacheKey);
        $list[] = $this;
        $cache->set($this->cacheKey, $list);
        return true;
    }

    public static function loadList(FileCache  $cache, $cacheKey): array
    {
        if (!$list = $cache->get($cacheKey)) {
            $list = [];
        }
        return $list;
    }

    public static function clearList(FileCache  $cache,$cacheKey): void
    {
        $cache->delete($cacheKey);
    }

    public static function deleteFromList(FileCache  $cache, string $cacheKey, string $productCode): void
    {
        $list = self::loadList($cache, $cacheKey);
        foreach ($list as $key => $item) {
            if ($item->productCode === $productCode) {
                unset($list[$key]);
            }
        }
        $cache->set($cacheKey, $list);
    }
}

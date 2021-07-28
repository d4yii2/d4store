<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace d4yii2\d4store\models\base;

use Yii;


/**
 * This is the base-model class for table "d4store_store_product".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $qnt
 * @property string $remain_qnt
 * @property string $reserved_qnt
 *
 * @property \d4yii2\d4store\models\D4storeAction[] $d4storeActions
 * @property \d4yii2\d4store\models\D3productProduct $product
 * @property string $aliasModel
 */
abstract class D4StoreStoreProduct extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'd4store_store_product';
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
        ];
        return $behaviors;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'required' => [['product_id'], 'required'],
            'smallint Unsigned' => [['product_id'],'integer' ,'min' => 0 ,'max' => 65535],
            'integer Unsigned' => [['id'],'integer' ,'min' => 0 ,'max' => 4294967295],
            [['qnt', 'remain_qnt', 'reserved_qnt'], 'number'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => \d4yii2\d4store\models\D3productProduct::className(), 'targetAttribute' => ['product_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('d4store', 'ID'),
            'product_id' => Yii::t('d4store', 'Product ID'),
            'qnt' => Yii::t('d4store', 'Qnt'),
            'remain_qnt' => Yii::t('d4store', 'Remain Qnt'),
            'reserved_qnt' => Yii::t('d4store', 'Reserved Qnt'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getD4storeActions()
    {
        return $this->hasMany(\d4yii2\d4store\models\D4storeAction::className(), ['store_product_id' => 'id'])->inverseOf('storeProduct');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(\d4yii2\d4store\models\D3productProduct::className(), ['id' => 'product_id'])->inverseOf('d4storeStoreProducts');
    }


    
    /**
     * @inheritdoc
     * @return \d4yii2\d4store\models\D4StoreStoreProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \d4yii2\d4store\models\D4StoreStoreProductQuery(get_called_class());
    }

}

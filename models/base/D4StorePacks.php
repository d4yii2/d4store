<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace d4yii2\d4store\models\base;

use Yii;

/**
 * This is the base-model class for table "d4store_packs".
 *
 * @property integer $id
 * @property string $code
 * @property string $notes
 * @property boolean $is_active
 *
 * @property \d4yii2\d4store\models\D4StorePackProductHistory[] $d4StorePackProductHistories
 * @property \d4yii2\d4store\models\D4StorePackProduct[] $d4StorePackProducts
 * @property string $aliasModel
 */
abstract class D4StorePacks extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'd4store_packs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'required' => [['is_active'], 'required'],
            'tinyint Unsigned' => [['is_active'],'integer' ,'min' => 0 ,'max' => 255],
            'integer Unsigned' => [['id'],'integer' ,'min' => 0 ,'max' => 4294967295],
            'boolean' => [['is_active'],'boolean'],
            [['notes'], 'string'],
            [['code'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('d4store', 'ID'),
            'code' => Yii::t('d4store', 'Code'),
            'notes' => Yii::t('d4store', 'Notes'),
            'is_active' => Yii::t('d4store', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getD4storePackProductHistories()
    {
        return $this->hasMany(\d4yii2\d4store\models\D4storePackProductHistory::className(), ['pack_id' => 'id'])->inverseOf('pack');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getD4StorePackProducts()
    {
        return $this->hasMany(\d4yii2\d4store\models\D4storePackProduct::className(), ['pack_id' => 'id'])->inverseOf('pack');
    }


    
    /**
     * @inheritdoc
     * @return \d4yii2\d4store\models\D4StorePacksQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \d4yii2\d4store\models\D4StorePacksQuery(get_called_class());
    }

}

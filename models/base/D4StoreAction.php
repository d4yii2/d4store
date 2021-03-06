<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace d4yii2\d4store\models\base;

use Yii;

use d3system\behaviors\D3DateTimeBehavior;

/**
 * This is the base-model class for table "d4store_action".
 *
 * @property integer $id
 * @property integer $store_product_id
 * @property string $type
 * @property integer $stack_id
 * @property string $is_active
 * @property string $time
 * @property float $qnt
 * @property integer $ref_model_id
 * @property integer $ref_model_record_id
 *
 * @property \d4yii2\d4store\models\D4StoreActionRef[] $d4StoreActionRefs
 * @property \d4yii2\d4store\models\SysModels $refModel
 * @property \d4yii2\d4store\models\D4StoreStack $stack
 * @property \d4yii2\d4store\models\D4StoreStoreProduct $storeProduct
 * @property string $aliasModel
 */
abstract class D4StoreAction extends \yii\db\ActiveRecord
{



    /**
    * ENUM field values
    */
    public const TYPE_IN = 'In';
    public const TYPE_OUT = 'Out';
    public const TYPE_TO_PROCESS = 'To Process';
    public const TYPE_FROM_PROCESS = 'From Process';
    public const TYPE_MOVE = 'Move';
    public const TYPE_RESERVATION = 'Reservation';
    public const IS_ACTIVE_YES = 'Yes';
    public const IS_ACTIVE_NOT = 'Not';
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'd4store_action';
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
        ];
        $behaviors = array_merge(
            $behaviors,
            D3DateTimeBehavior::getConfig(['time'])
        );
        return $behaviors;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'required' => [['store_product_id', 'type', 'qnt'], 'required'],
            'enum-type' => ['type', 'in', 'range' => [
                    self::TYPE_IN,
                    self::TYPE_OUT,
                    self::TYPE_TO_PROCESS,
                    self::TYPE_FROM_PROCESS,
                    self::TYPE_MOVE,
                    self::TYPE_RESERVATION,
                ]
            ],
            'enum-is_active' => ['is_active', 'in', 'range' => [
                    self::IS_ACTIVE_YES,
                    self::IS_ACTIVE_NOT,
                ]
            ],
            'tinyint Unsigned' => [['ref_model_id'],'integer' ,'min' => 0 ,'max' => 255],
            'smallint Unsigned' => [['stack_id'],'integer' ,'min' => 0 ,'max' => 65535],
            'integer Unsigned' => [['id','store_product_id','ref_model_record_id'],'integer' ,'min' => 0 ,'max' => 4294967295],
            [['type', 'is_active'], 'string'],
            [['time'], 'safe'],
            [['qnt'], 'number'],
            [['ref_model_id'], 'exist', 'skipOnError' => true, 'targetClass' => \d4yii2\d4store\models\SysModels::className(), 'targetAttribute' => ['ref_model_id' => 'id']],
            [['stack_id'], 'exist', 'skipOnError' => true, 'targetClass' => \d4yii2\d4store\models\D4StoreStack::className(), 'targetAttribute' => ['stack_id' => 'id']],
            [['store_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => \d4yii2\d4store\models\D4StoreStoreProduct::className(), 'targetAttribute' => ['store_product_id' => 'id']],
            'D3DateTimeBehavior' => [['time_local'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('d4store', 'ID'),
            'store_product_id' => Yii::t('d4store', 'Store Product ID'),
            'type' => Yii::t('d4store', 'Type'),
            'stack_id' => Yii::t('d4store', 'Stack ID'),
            'is_active' => Yii::t('d4store', 'Is Active'),
            'time' => Yii::t('d4store', 'Time'),
            'qnt' => Yii::t('d4store', 'Qnt'),
            'ref_model_id' => Yii::t('d4store', 'Ref Model ID'),
            'ref_model_record_id' => Yii::t('d4store', 'Ref Model Record ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getD4StoreActionRefs()
    {
        return $this->hasMany(\d4yii2\d4store\models\D4StoreActionRef::className(), ['action_id' => 'id'])->inverseOf('action');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefModel()
    {
        return $this->hasOne(\d4yii2\d4store\models\SysModels::className(), ['id' => 'ref_model_id'])->inverseOf('d4StoreActions');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStack()
    {
        return $this->hasOne(\d4yii2\d4store\models\D4StoreStack::className(), ['id' => 'stack_id'])->inverseOf('d4StoreActions');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoreProduct()
    {
        return $this->hasOne(\d4yii2\d4store\models\D4StoreStoreProduct::className(), ['id' => 'store_product_id'])->inverseOf('d4StoreActions');
    }


    
    /**
     * @inheritdoc
     * @return \d4yii2\d4store\models\D4StoreActionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \d4yii2\d4store\models\D4StoreActionQuery(get_called_class());
    }


    /**
     * get column type enum value label
     * @param string $value
     * @return string
     */
    public static function getTypeValueLabel($value): string
    {
        if(!$value){
            return '';
        }
        $labels = self::optsType();
        return $labels[$value] ?? $value;
    }

    /**
     * column type ENUM value labels
     * @return string[]
     */
    public static function optsType(): array
    {
        return [
            self::TYPE_IN => Yii::t('d4store', 'In'),
            self::TYPE_OUT => Yii::t('d4store', 'Out'),
            self::TYPE_TO_PROCESS => Yii::t('d4store', 'To Process'),
            self::TYPE_FROM_PROCESS => Yii::t('d4store', 'From Process'),
            self::TYPE_MOVE => Yii::t('d4store', 'Move'),
            self::TYPE_RESERVATION => Yii::t('d4store', 'Reservation'),
        ];
    }

    /**
     * get column is_active enum value label
     * @param string $value
     * @return string
     */
    public static function getIsActiveValueLabel($value): string
    {
        if(!$value){
            return '';
        }
        $labels = self::optsIsActive();
        return $labels[$value] ?? $value;
    }

    /**
     * column is_active ENUM value labels
     * @return string[]
     */
    public static function optsIsActive(): array
    {
        return [
            self::IS_ACTIVE_YES => Yii::t('d4store', 'Yes'),
            self::IS_ACTIVE_NOT => Yii::t('d4store', 'Not'),
        ];
    }
    /**
    * ENUM field values
    */
    /**
     * @return bool
     */
    public function isTypeIn(): bool
    {
        return $this->type === self::TYPE_IN;
    }

     /**
     * @return void
     */
    public function setTypeIn(): void
    {
        $this->type = self::TYPE_IN;
    }
    /**
     * @return bool
     */
    public function isTypeOut(): bool
    {
        return $this->type === self::TYPE_OUT;
    }

     /**
     * @return void
     */
    public function setTypeOut(): void
    {
        $this->type = self::TYPE_OUT;
    }
    /**
     * @return bool
     */
    public function isTypeToProcess(): bool
    {
        return $this->type === self::TYPE_TO_PROCESS;
    }

     /**
     * @return void
     */
    public function setTypeToProcess(): void
    {
        $this->type = self::TYPE_TO_PROCESS;
    }
    /**
     * @return bool
     */
    public function isTypeFromProcess(): bool
    {
        return $this->type === self::TYPE_FROM_PROCESS;
    }

     /**
     * @return void
     */
    public function setTypeFromProcess(): void
    {
        $this->type = self::TYPE_FROM_PROCESS;
    }
    /**
     * @return bool
     */
    public function isTypeMove(): bool
    {
        return $this->type === self::TYPE_MOVE;
    }

     /**
     * @return void
     */
    public function setTypeMove(): void
    {
        $this->type = self::TYPE_MOVE;
    }
    /**
     * @return bool
     */
    public function isTypeReservation(): bool
    {
        return $this->type === self::TYPE_RESERVATION;
    }

     /**
     * @return void
     */
    public function setTypeReservation(): void
    {
        $this->type = self::TYPE_RESERVATION;
    }
    /**
     * @return bool
     */
    public function isIsActiveYes(): bool
    {
        return $this->is_active === self::IS_ACTIVE_YES;
    }

     /**
     * @return void
     */
    public function setIsActiveYes(): void
    {
        $this->is_active = self::IS_ACTIVE_YES;
    }
    /**
     * @return bool
     */
    public function isIsActiveNot(): bool
    {
        return $this->is_active === self::IS_ACTIVE_NOT;
    }

     /**
     * @return void
     */
    public function setIsActiveNot(): void
    {
        $this->is_active = self::IS_ACTIVE_NOT;
    }
}

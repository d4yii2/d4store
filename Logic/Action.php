<?php


namespace d4yii2\d4store\Logic;


use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d4yii2\d4store\models\D4StoreAction;
use d4yii2\d4store\models\D4StoreActionRef;
use d4yii2\d4store\models\D4StoreStack;
use d4yii2\d4store\models\D4StoreStoreProduct;
use DateTime;
use yii\db\Exception;

class Action
{

    /** @var \d4yii2\d4store\models\D4StoreStoreProduct */
    private $_storeProduct;

    /** @var \DateTime */
    private $_time;

    /** @var D4StoreAction */
    public $_action;

    /**
     * @param int $productId
     * @param float $qnt
     * @param \DateTime|null $time
     * @return \d4yii2\d4store\Logic\Action
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public static function createProduct(
        int      $productId,
        float    $qnt,
        DateTime $time = null
    ): self {
        $product = new D4StoreStoreProduct();
        $product->product_id = $productId;
        $product->qnt = $qnt;
        $product->remain_qnt = $qnt;
        $product->reserved_qnt = 0;
        $product->setStatusActive();
        $product->setTypeRegular();
        if (!$product->save()) {
            throw new D3ActiveRecordException($product);
        }

        return new self($product, $time);
    }


    /**
     * @param int $productId
     * @param float $qnt
     * @param \DateTime|null $time
     * @return static
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public static function createProductInOut(
        int      $productId,
        float    $qnt,
        DateTime $time = null
    ): self {
        $product = new D4StoreStoreProduct();
        $product->product_id = $productId;
        $product->qnt = $qnt;
        $product->remain_qnt = $qnt;
        $product->reserved_qnt = 0;
        $product->setStatusPlan();
        $product->setTypeInOut();
        if (!$product->save()) {
            throw new D3ActiveRecordException($product);
        }

        return new self($product, $time);
    }

    /**
     * Action constructor.
     * @param \d4yii2\d4store\models\D4StoreStoreProduct|null $storeProduct
     * @param \DateTime|null $time
     */
    public function __construct(D4StoreStoreProduct $storeProduct = null, DateTime $time = null)
    {
        $this->_storeProduct = $storeProduct;
        $this->_time = $time ?? new DateTime();
    }

    /**
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public function in(D4StoreStack $stack, $model): D4StoreAction
    {
        $this->_action = $this->newAction();
        $this->_action->setIsActiveYes();
        $this->_action->qnt = $this->_storeProduct->qnt;
        $this->_action->setTypeIn();
        $this->_action->stack_id = $stack->id;

        $this->_action->ref_model_id = SysModelsDictionary::getIdByClassName(get_class($model));
        $this->_action->ref_model_record_id = $model->id;
        if (!$this->_action->save()) {
            throw new D3ActiveRecordException($this->_action);
        }
        return $this->_action;
    }

    /**
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public function fromProcess(D4StoreStack $stack, $model): D4StoreAction
    {
        $this->_action = $this->newAction();
        $this->_action->setIsActiveYes();
        $this->_action->qnt = $this->_storeProduct->qnt;
        $this->_action->setTypeFromProcess();
        $this->_action->stack_id = $stack->id;

        $this->_action->ref_model_id = SysModelsDictionary::getIdByClassName(get_class($model));
        $this->_action->ref_model_record_id = $model->id;
        if (!$this->_action->save()) {
            throw new D3ActiveRecordException($this->_action);
        }
        return $this->_action;
    }

    /**
     * @throws \d3system\exceptions\D3ActiveRecordException
     * @throws \yii\db\Exception
     */
    public function reservation(float $qnt, $model): D4StoreAction
    {
        $this->_storeProduct->reserved_qnt += $qnt;
        if ($this->_storeProduct->reserved_qnt > $this->_storeProduct->remain_qnt) {
            throw new Exception('Try reserve more remain quantity');
        }
        if (!$this->_storeProduct->save()) {
            throw new D3ActiveRecordException($this->_storeProduct);
        }

        $this->_action = $this->newAction();
        $this->_action->setIsActiveYes();
        $this->_action->qnt = $qnt;
        $this->_action->setTypeReservation();
        $this->_action->ref_model_id = SysModelsDictionary::getIdByClassName(get_class($model));
        $this->_action->ref_model_record_id = $model->id;
        if (!$this->_action->save()) {
            throw new D3ActiveRecordException($this->_action);
        }
        return $this->_action;
    }

    /**
     * @throws \d3system\exceptions\D3ActiveRecordException
     * @throws \yii\db\Exception|\Throwable
     */
    public function cancelReservation(D4StoreAction $action)
    {
        $this->_storeProduct = $action->storeProduct;
        $this->_storeProduct->reserved_qnt -= $action->qnt;
        $this->_storeProduct->save();
        foreach ($action->d4StoreActionRefs as $ref) {
            $ref->delete();
        }
        $action->delete();
    }

    /**
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public function move(D4StoreStack $stack, $model = null): D4StoreAction
    {
        $this->setMoveActionsIsNotActive();

        $this->_action = $this->newAction();
        $this->_action->setIsActiveYes();
        $this->_action->qnt = $this->_storeProduct->remain_qnt;
        $this->_action->setTypeMove();
        $this->_action->stack_id = $stack->id;
        if($model) {
            $this->_action->ref_model_id = SysModelsDictionary::getIdByClassName(get_class($model));
            $this->_action->ref_model_record_id = $model->id;
        }
        if (!$this->_action->save()) {
            throw new D3ActiveRecordException($this->_action);
        }
        return $this->_action;
    }

    /**
     * @param float $qnt
     * @param $model
     * @return \d4yii2\d4store\models\D4StoreAction
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public function out(float $qnt, $model): D4StoreAction
    {
        $this->setMoveActionsIsNotActive();

        $this->newAction();
        $this->_action->setIsActiveYes();
        $this->_action->qnt = $qnt;
        $this->_action->setTypeOut();
        $this->_action->ref_model_id = SysModelsDictionary::getIdByClassName(get_class($model));
        $this->_action->ref_model_record_id = $model->id;
        if (!$this->_action->save()) {
            throw new D3ActiveRecordException($this->_action);
        }
        return $this->_action;
    }

    private function newAction(): D4StoreAction
    {
        $action = new D4StoreAction();
        $action->store_product_id = $this->_storeProduct->id;
        $action->time = $this->_time->format('Y-m-d H:i:s');
        return $action;
    }

    /**
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public function addRef($model): D4StoreActionRef
    {
        $ref = new D4StoreActionRef();
        $ref->action_id = $this->_action->id;
        $ref->model_id = SysModelsDictionary::getIdByClassName(get_class($model));
        $ref->model_record_id = $model->id;
        if (!$ref->save()) {
            throw new D3ActiveRecordException($ref);
        }
        return $ref;
    }

    private function setMoveActionsIsNotActive(): void
    {
        foreach ($this->_storeProduct->d4StoreActions as $action) {
            if ($action->isIsActiveNot()) {
                continue;
            }
            if (!in_array(
                $action->type,
                [
                    D4StoreAction::TYPE_IN,
                    D4StoreAction::TYPE_MOVE,
                    D4StoreAction::TYPE_TO_PROCESS,
                    D4StoreAction::TYPE_FROM_PROCESS
                ],
                true
            )) {
                continue;
            }
            $action->setIsActiveNot();
            if (!$action->save()) {
                throw new D3ActiveRecordException($action);
            }
        }
    }
}

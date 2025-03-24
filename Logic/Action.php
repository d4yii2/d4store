<?php


namespace d4yii2\d4store\Logic;


use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d4yii2\d4store\models\D3productProduct;
use d4yii2\d4store\models\D4StoreAction;
use d4yii2\d4store\models\D4StoreActionRef;
use d4yii2\d4store\models\D4StoreStack;
use d4yii2\d4store\models\D4StoreStoreProduct;
use DateTime;
use Throwable;
use yii\db\Exception;
use yii\helpers\VarDumper;

class Action
{

    private ?D4StoreStoreProduct $_storeProduct;

    private ?DateTime $_time;

    public ?D4StoreAction $_action = null;

    /**
     * pievieno store product un pārēķina daudzumu uz bāzes mērvienību
     * @param int $productId
     * @param float $qnt
     * @param int|null $unitId
     * @param DateTime|null $time
     * @return Action
     * @throws D3ActiveRecordException
     * @throws Exception|\yii\base\Exception
     */
    public static function createProduct(
        int      $productId,
        float    $qnt,
        int $unitId = null,
        DateTime $time = null
    ): self {

        /** converte uz D3productProduct mērvienību */
        if ($unitId) {
            if (!$d3Product = D3productProduct::findOne($productId)) {
                throw new Exception('Can not find D3productProduct. id=' . $productId);
            }
            if ($d3Product->unit_id !== $unitId) {
                if (!in_array($d3Product->unit_id, $d3Product->getToUnitIds($unitId), false)) {
                    throw new Exception('Not defined converting for  D3productProduct.id=' . $productId . ' from UnitId=' . $unitId);
                }
                if (!$convertedQnt = $d3Product->unitConvertFromTo($qnt, $unitId, $d3Product->unit_id)) {
                    throw new Exception('Can not convert quantity for  D3productProduct. id=' . $productId . ' UnitId=' . $unitId);
                }
                $qnt = $convertedQnt;
            }
        }

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
     * @param DateTime|null $time
     * @return static
     * @throws D3ActiveRecordException|Exception
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

    public static function loadAction(D4StoreAction $action, DateTime $time = null): self
    {
        $self = new self();
        $self->_action = $action;
        $self->_storeProduct = $action->storeProduct;
        $self->_time = $time ?? new DateTime();
        return $self;
    }

    /**
     * Action constructor.
     * @param D4StoreStoreProduct|null $storeProduct
     * @param DateTime|null $time
     */
    public function __construct(D4StoreStoreProduct $storeProduct = null, DateTime $time = null)
    {
        $this->_storeProduct = $storeProduct;
        $this->_time = $time ?? new DateTime();
    }

    /**
     * @return D4StoreStoreProduct
     */
    public function getStoreProduct(): ?D4StoreStoreProduct
    {
        return $this->_storeProduct;
    }

    /**
     * @throws D3ActiveRecordException|Exception
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
     * @throws D3ActiveRecordException|Exception
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
     * registry to precessing products
     *
     * @throws D3ActiveRecordException
     * @throws \yii\base\Exception
     * @throws Throwable
     */
    public function toProcess($model, float $qnt): D4StoreAction
    {
        //$this->setMoveActionsIsNotActive();
        /** rezerveshanu smazina */
        if ($reservationAction = D4StoreAction::find()->getReservationsByModel($model)->one()) {
            self::processReservation($reservationAction, $qnt);
        }

        /** samazina noliktavas atlikumu */
        $this->_storeProduct->remain_qnt -= round($qnt, 3);
        if ($this->_storeProduct->remain_qnt < 0) {
            throw new \yii\base\Exception(
                'Try ToProcess more as remain.' . PHP_EOL
                . ' RefModel: ' . VarDumper::dumpAsString($model->attributes) . PHP_EOL
                . ' qnt" ' . VarDumper::dumpAsString($qnt) . PHP_EOL
                . ' product: ' . VarDumper::dumpAsString($this->_storeProduct->attributes)
            );
        }

        /** ja produkts viss izmantots, is_atcive = N0 */
        if ($this->_storeProduct->remain_qnt === 0.) {
            $this->setMoveActionsIsNotActive();
            $this->_storeProduct->setStatusClosed();
        }

//        /**
//         * atceļ rezvāciju - nekorekti
//         */
//        if ($reservationAction = D4StoreAction::find()->getReservationsByModel($model)->one()) {
//            self::cancelReservation($reservationAction);
//        }
        $this->_action = $this->newAction();
        $this->_action->setIsActiveNot();
        $this->_action->qnt = $qnt;
        $this->_action->setTypeToProcess();

        $this->_action->ref_model_id = SysModelsDictionary::getIdByClassName(get_class($model));
        $this->_action->ref_model_record_id = $model->id;
        if (!$this->_action->save()) {
            throw new D3ActiveRecordException($this->_action);
        }
        if (!$this->_storeProduct->save()) {
            throw new D3ActiveRecordException($this->_storeProduct);
        }
        return $this->_action;
    }

    /**
     * @throws D3ActiveRecordException
     * @throws Exception
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
     * @throws D3ActiveRecordException
     * @throws Throwable
     */
    public static function cancelReservation(D4StoreAction $action): void
    {
        $storeProduct = $action->storeProduct;
        $storeProduct->reserved_qnt -= $action->qnt;
        if (!$storeProduct->save()) {
            throw new D3ActiveRecordException($storeProduct);
        }
        $action->setIsActiveNot();
        if (!$action->save()) {
            throw new D3ActiveRecordException($action);
        }
    }

    /**
     * samazina rezervēto daudzumu
     * ja vairs nav rezervēts, action uztaisa active Not
     * @throws D3ActiveRecordException
     * @throws Exception
     */
    public static function processReservation(D4StoreAction $action, float $qnt): void
    {
        $action->qnt -= $qnt;
        $storeProduct = $action->storeProduct;
        $storeProduct->reserved_qnt -= $qnt;
        if (!$storeProduct->save()) {
            throw new D3ActiveRecordException($storeProduct);
        }

        if (!$action->qnt) {
            $action->setIsActiveNot();
        }
        if (!$action->save()) {
            throw new D3ActiveRecordException($action);
        }
    }

    /**
     * move with reference model
     * @throws D3ActiveRecordException|Exception
     */
    public function move(D4StoreStack $stack, $model = null): D4StoreAction
    {
        $this->setMoveActionsIsNotActive();

        $this->_action = $this->newAction();
        $this->_action->setIsActiveYes();
        $this->_action->qnt = $this->_storeProduct->remain_qnt;
        $this->_action->setTypeMove();
        $this->_action->stack_id = $stack->id;
        if ($model) {
            $this->_action->ref_model_id = SysModelsDictionary::getIdByClassName(get_class($model));
            $this->_action->ref_model_record_id = $model->id;
        }
        if (!$this->_action->save()) {
            throw new D3ActiveRecordException($this->_action);
        }
        return $this->_action;
    }

    /**
     * write off with reference model
     * @param float $qnt
     * @param $model
     * @return D4StoreAction
     * @throws D3ActiveRecordException|Exception
     */
    public function out(float $qnt, $model): D4StoreAction
    {
        return $this->outSys(
            $qnt,
            SysModelsDictionary::getIdByClassName(get_class($model)),
            $model->id
        );
    }

    /**
     * write off with model ref id and model record id
     * @param float $qnt
     * @param int $sysModelId
     * @param int $modelRecordId
     * @return D4StoreAction
     * @throws D3ActiveRecordException
     * @throws Exception
     */
    public function outSys(float $qnt, int $sysModelId, int $modelRecordId): D4StoreAction
    {
        $this->setMoveActionsIsNotActive();

        $this->_action = $this->newAction();
        $this->_action->setIsActiveYes();
        $this->_action->qnt = $qnt;
        $this->_action->setTypeOut();
        $this->_action->ref_model_id = $sysModelId;
        $this->_action->ref_model_record_id = $modelRecordId;
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
     * add to action model ref by model
     * @throws D3ActiveRecordException|Exception
     */
    public function addRef($model): D4StoreActionRef
    {
        return $this->addRefSys(
            SysModelsDictionary::getIdByClassName(get_class($model)),
            $model->id
        );
    }

    /**
     * add to action model ref by ref_if and model record id
     * @param int $sysModelId
     * @param int $modelRecordId
     * @return D4StoreActionRef
     * @throws D3ActiveRecordException
     * @throws Exception
     */
    public function addRefSys(int $sysModelId, int $modelRecordId): D4StoreActionRef
    {
        $ref = new D4StoreActionRef();
        $ref->action_id = $this->_action->id;
        $ref->model_id = $sysModelId;
        $ref->model_record_id = $modelRecordId;
        if (!$ref->save()) {
            throw new D3ActiveRecordException($ref);
        }
        return $ref;
    }

    /**
     * @throws D3ActiveRecordException|Exception
     */
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

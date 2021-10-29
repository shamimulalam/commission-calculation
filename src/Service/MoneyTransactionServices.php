<?php

namespace CommissionTask\Service;

use CommissionTask\Models\TransactionModel;
use CommissionTask\Repositories\TransactionInterface;
use CommissionTask\Traits\CommissionCalculation;

class MoneyTransactionServices
{
    use CommissionCalculation;
    /**
     * @var TransactionInterface
     */
    protected $transactionRepository;

    /**
     * @var array
     */
    protected $setting;

    /**
     * TransactionController constructor.
     *
     * @param TransactionInterface $repository
     * @param array $config
     */
    public function __construct(TransactionInterface $repository, array $setting)
    {
        $this->transactionRepository = $repository;
        $this->setting                = $setting;
    }

    public function index($filename)
    {
        $this->transactionRepository->setDataFromFile($filename);
        $getTransactionData = $this->transactionRepository->getAllData();
        $this->checkCommission($getTransactionData);

    }
    private function checkCommission(array $transactions){
        if (is_array($transactions) && count($transactions)>0){
            foreach ($transactions as $transaction) {
                if ($transaction->getTransactionType() == TransactionModel::Deposit) {
                    $commission = $this->depositCommission($transaction,$this->setting);
                } else {
                    $commission = $this->withdrawCommission($transaction,$this->setting);
                }
                $this->printCommission($commission);
            }
        }else{
            print_r("Date Not Found\n");
        }
    }

}
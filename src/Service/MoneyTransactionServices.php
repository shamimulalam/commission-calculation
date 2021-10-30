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
    private function checkCommission($transactions){
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
    private function depositCommission(TransactionModel $transaction, $setting)
    {
        $commission = $transaction->getTransactionAmount() * $setting['depositCommissionPercent'];
        $convertedLimit = $this->convertCurrency($transaction, $setting);
        if ($commission > $convertedLimit) {
            return $convertedLimit;
        } else {
            return $commission;
        }
    }
    private function withdrawCommission(TransactionModel $transaction, $setting)
    {
        if ($transaction->getUserType() == 'private') {
            $date = new \DateTime($transaction->getDate());
            $week = $date->format('W');
            $userTransactions = $this->transactionRepository->getByParam('userId', $transaction->getUserId());
            $transactionsPerWeek = 0;
            $transactionsPerWeekAmount = 0;

            /** @var TransactionModel $userTransaction */
            foreach ($userTransactions as $userTransaction) {
                $currentDate = new \DateTime($userTransaction->getDate());
                if (
                    $week == $currentDate->format('W') &&
                    $userTransaction->getTransactionType() == TransactionModel::WITHDRAW ) {
                    if ($userTransaction->getId() == $transaction->getId() ) {
                        break;
                    }
                    $transactionsPerWeek++;
                    $transactionsPerWeekAmount += $this->convertCurrency($userTransaction, $setting);
                }
            }
            /** private user's discount for cashout calculation */
            if ($transactionsPerWeek >= $setting['withdrawCommissionCommonFreeTransactionsLimit']) {
                $commission = $transaction->getTransactionAmount() * $setting['withdrawCommissionPercentCommon'];
                return $commission;
            }else {

                if ($transactionsPerWeekAmount >=  $setting['withdrawCommissionCommonDiscount'] ) {
                    $commission = $transaction->getTransactionAmount() * $setting['withdrawCommissionPercentCommon'];
                    return $commission;
                } else {
                    $amount = max($this->convertCurrency($transaction, $setting) + $transactionsPerWeekAmount - $setting['withdrawCommissionCommonDiscount'], 0);
                    $commission = $amount * $setting['withdrawCommissionPercentCommon'];
                    return $this->convertCurrency($transaction, $setting, $commission);
                }

            }
        } else {
            $commission = $transaction->getTransactionAmount() * $setting['withdrawBusinessCommissionPercent'];
            $convertedLimit = $this->convertCurrency($transaction, $setting, $commission);
            if ($commission < $convertedLimit) {
                return $convertedLimit;
            } else {
                return $commission;
            }
        }
    }


}
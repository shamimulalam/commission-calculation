<?php

namespace CommissionTask\Traits;

use CommissionTask\Models\TransactionModel;

trait CommissionCalculation
{
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
                if ($week == $currentDate->format('W') && $userTransaction->getTransactionType() == TransactionModel::WITHDRAW) {
                    if ($userTransaction->getId() == $transaction->getId()) {
                        break;
                    }
                    $transactionsPerWeek++;
                    $transactionsPerWeekAmount += $this->convertCurrency($userTransaction, $setting);
                }
            }
            //  var_dump($transactionsPerWeekAmount);
            //exit();
            /** private user's discount for cashout calculation */
            if ($transactionsPerWeek >= $setting['withdrawCommissionCommonFreeTransactionsLimit']) {
                var_dump(1);
                exit();
                $commission = $transaction->getTransactionAmount() * $setting['withdrawCommissionPercentCommon'];
                return $commission;
            } else {

                if ($transactionsPerWeekAmount > $setting['withdrawCommissionCommonDiscount']) {
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
            $convertedLimit = $this->convertCurrency($transaction, $setting);
            if ($commission < $convertedLimit) {
                return $convertedLimit;
            } else {
                return $commission;
            }
        }
    }

    private function convertCurrency(TransactionModel $transaction, $setting, $amount = -1)
    {
        if ($amount < 0) {
            $converted = $transaction->getTransactionAmount() / $setting['currencyConversion'][$transaction->getCurrency()];
        } else {
            $converted = $amount * $setting['currencyConversion'][$transaction->getCurrency()];
        }
        $fig = pow(10, $setting['commissionPrecision']);
        $converted = ceil($converted * $fig) / $fig;
        return $converted;
    }

    private function printCommission($commission)
    {
        fwrite(STDOUT, sprintf("%0.2f\n", $commission));
    }
}
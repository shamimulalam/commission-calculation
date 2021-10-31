<?php

namespace CommissionTask\Traits;

use CommissionTask\Models\TransactionModel;
use CommissionTask\Repositories\TransactionInterface;
use CommissionTask\Repositories\TransactionRepository;
use phpDocumentor\Reflection\Types\This;

trait CommissionCalculation
{

    private function convertCurrency(TransactionModel $transaction, $setting, $amount = -1)
    {
        if ($amount < 0) {
            $converted = $transaction->getTransactionAmount() / $setting['currencyConversion'][$transaction->getCurrency()];
        } else {
            $converted = $amount * $setting['currencyConversion'][$transaction->getCurrency()];
        }
        $fig = pow(10, $setting['commissionPrecision']);
        $converted = ceil($converted * $fig) / $fig;
        return  floor($converted);
    }

    private function printCommission($commission)
    {
        fwrite(STDOUT, sprintf("%0.2f\n", $commission));
    }
    private function gracefulRound($val, $min = 2, $max = 4) {
        $result = round($val, $min);
        if ($result == 0 && $min < $max) {
            return $this->gracefulRound($val, ++$min, $max);
        } else {
            return $result;
        }
    }
}
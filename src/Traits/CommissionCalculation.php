<?php

namespace CommissionTask\Traits;

use CommissionTask\Models\TransactionModel;

trait CommissionCalculation
{

    private function convertCurrency(TransactionModel $transaction, $setting, $amount = -1)
    {
        if ($amount < 0) {
            $converted = $transaction->getTransactionAmount(
                ) / $setting['currencyConversion'][$transaction->getCurrency()]['rate'];
        } else {
            $converted = $amount * $setting['currencyConversion'][$transaction->getCurrency()]['rate'];
        }
        return $converted;
    }

    private function printCommission($commissionData)
    {
        $roundUp = $this->roundUp($commissionData['amount'], $commissionData['setting'], $commissionData['precision']);
        fwrite(STDOUT, print_r($roundUp . "\n", true));
    }

    public function roundUp($amount, $setting, $precision)
    {
        $amount = bcmul($amount, (string)pow(10, $precision), $setting['commissionPrecision']);
        $parts = explode('.', $amount);
        if (count($parts) == 2 && intval($parts[1]) > 0) {
            $parts[0] = bcadd($parts[0], '1', $setting['commissionPrecision']);
        }
        return bcdiv($parts[0], (string)pow(10, $precision), $precision);
    }
}
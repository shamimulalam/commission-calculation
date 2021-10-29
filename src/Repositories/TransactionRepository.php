<?php

namespace CommissionTask\Repositories;

use CommissionTask\Models\TransactionModel;

class TransactionRepository implements TransactionInterface
{
    protected $transactions = [];

    public function setDataFromFile($filename)
    {
        if (file_exists($filename)) {
            $contents = file_get_contents($filename);
            $contents = str_replace("\r\n", "\n", $contents);
            $contents = explode("\n", $contents);

            foreach ($contents as $content) {
                if ($content != "") {
                    $content = explode(',', $content);
                    $transaction = new TransactionModel();
                    $transaction->setDate($content[0]);
                    $transaction->setUserId($content[1]);
                    $transaction->setUserType($content[2]);
                    $transaction->setTransactionType($content[3]);
                    $transaction->setTransactionAmount($content[4]);
                    $transaction->setCurrency($content[5]);
                    $this->transactions[] = $transaction;
                }
            }
        }
    }

    public function getAllData()
    {
        return $this->transactions;
    }

    public function addData(TransactionModel $transaction)
    {
        $this->transactions[] = $transaction;
    }
    public function getByParam($param, $value)
    {
        $userTransactions = [];
        foreach ($this->transactions as $transaction) {
            $method = 'get' . ucfirst($param);

            if (method_exists($transaction, $method)) {
                if ($transaction->$method() == $value) {
                    $userTransactions[] = $transaction;
                }
            }
        }
        return $userTransactions;
    }
}
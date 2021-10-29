<?php

namespace CommissionTask\Repositories;

use CommissionTask\Models\TransactionModel;

interface TransactionInterface
{
    public function setDataFromFile($fileName);

    public function getAllData();

    public function addData(TransactionModel $transactionModel);

    public function getByParam($param, $value);

}
<?php

namespace CommissionTask\Tests\Repositories;

use CommissionTask\Models\TransactionModel;
use CommissionTask\Repositories\TransactionRepository;
use PHPUnit\Framework\TestCase;

class TransactionRepositoryTest extends TestCase
{
    public function testSetGetAllData()
    {
        $transaction = new TransactionModel();
        $transaction->setDate("2021-01-01");
        $transaction->setUserId("4");
        $transaction->setUserType("privet");
        $transaction->setTransactionType("deposit");
        $transaction->setTransactionAmount("500.00");
        $transaction->setCurrency("JPY");

        $repo = new TransactionRepository();
        $repo->addData($transaction);

        $this->assertEquals([$transaction], $repo->getAllData());

    }


}
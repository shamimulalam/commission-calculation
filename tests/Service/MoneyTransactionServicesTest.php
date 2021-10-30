<?php

namespace CommissionTask\Test\Service;

use CommissionTask\Models\TransactionModel;
use CommissionTask\Repositories\TransactionRepository;
use CommissionTask\Service\MoneyTransactionServices;
use CommissionTask\Traits\CommissionCalculation;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MoneyTransactionServicesTest extends TestCase
{
    use CommissionCalculation;

    private $setting;
    private $services;
    private $transactionModel;

    /**
     * TransactionControllerTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setting = include('./setting.php');
        $this->services = new MoneyTransactionServices(new TransactionRepository(), $this->setting);
        $this->transactionModel = new TransactionModel();

    }

    protected static function getMethod($class, $name)
    {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    public function testDepositInCommissionEUR()
    {
        $method = self::getMethod('CommissionTask\Service\MoneyTransactionServices', 'depositCommission');
        $this->transactionModel->setDate("2016-01-10");
        $this->transactionModel->setUserId("2");
        $this->transactionModel->setUserType("business");
        $this->transactionModel->setTransactionType("deposit");
        $this->transactionModel->setTransactionAmount("10000.00");
        $this->transactionModel->setCurrency("EUR");
        $result = $method->invokeArgs($this->services, [$this->transactionModel]);
        $this->assertEquals(3.00, $result);
    }

    public function testDepositInCommissionUSD()
    {
        $method = self::getMethod('CommissionTask\Service\MoneyTransactionServices', 'depositCommission');
        $this->transactionModel->setDate("2016-01-05");
        $this->transactionModel->setUserId("1");
        $this->transactionModel->setUserType("private");
        $this->transactionModel->setTransactionType("deposit");
        $this->transactionModel->setTransactionAmount("100.00");
        $this->transactionModel->setCurrency("USD");
        $result = $method->invokeArgs($this->services, [$this->transactionModel]);
        $this->assertEquals(0.03, $result);
    }

    public function testCashInCommissionJPY()
    {
        $method = self::getMethod('CommissionTask\Service\MoneyTransactionServices', 'depositCommission');
        $this->transactionModel->setDate("2016-01-05");
        $this->transactionModel->setUserId("1");
        $this->transactionModel->setUserType("private");
        $this->transactionModel->setTransactionType("deposit");
        $this->transactionModel->setTransactionAmount("10000");
        $this->transactionModel->setCurrency("JPY");
        $result = $method->invokeArgs($this->services, [$this->transactionModel]);
        $this->assertEquals(3, $result);
    }

    public function testConvertCurrencyToEUR()
    {
        $this->transactionModel->setDate("2016-01-05");
        $this->transactionModel->setUserId("1");
        $this->transactionModel->setUserType("private");
        $this->transactionModel->setTransactionType("deposit");
        $this->transactionModel->setTransactionAmount("10000");
        $this->transactionModel->setCurrency("JPY");
        $result = $this->convertCurrency($this->transactionModel, $this->setting, 100);
        $this->assertEquals(12953, $result);
    }

    public function testConvertCurrencyFromEUR()
    {
        $this->transactionModel->setDate("2016-01-05");
        $this->transactionModel->setUserId("1");
        $this->transactionModel->setUserType("private");
        $this->transactionModel->setTransactionType("deposit");
        $this->transactionModel->setTransactionAmount("10000");
        $this->transactionModel->setCurrency("JPY");
        $result = $this->convertCurrency($this->transactionModel, $this->setting, 100);
        $this->assertEquals(12953, $result);
    }
    /*



        public function testConvertCurrencyToEUR()
        {
            $method      = self::getMethod('Paysera\Controllers\TransactionController', 'convertCurrency');
            $obj         = new TransactionController(new TransactionRepository(), $this->config);
            $transaction = new Transaction();
            $transaction->setDate("2016-01-05");
            $transaction->setUserId("1");
            $transaction->setUserType("private");
            $transaction->setTransactionType("deposit");
            $transaction->setTransactionAmount("10000");
            $transaction->setCurrency("JPY");

            $result = $method->invokeArgs($obj, [$transaction]);

            self::assertEquals(77.21, $result);
        }

        public function testConvertCurrencyFromEUR()
        {
            $method      = self::getMethod('Paysera\Controllers\TransactionController', 'convertCurrency');
            $obj         = new TransactionController(new TransactionRepository(), $this->config);
            $transaction = new Transaction();
            $transaction->setDate("2016-01-05");
            $transaction->setUserId("1");
            $transaction->setUserType("private");
            $transaction->setTransactionType("deposit");
            $transaction->setTransactionAmount("10000");
            $transaction->setCurrency("JPY");

            $result = $method->invokeArgs($obj, [$transaction, 100]);

            self::assertEquals(12953, $result);
        }

        public function testCashOutCommissionOneTransactionprivate()
        {
            $method      = self::getMethod('Paysera\Controllers\TransactionController', 'cashOutCommission');
            $obj         = new TransactionController(new TransactionRepository(), $this->config);
            $transaction = new Transaction();
            $transaction->setDate("2016-01-05");
            $transaction->setUserId("1");
            $transaction->setUserType("private");
            $transaction->setTransactionType("withdraw");
            $transaction->setTransactionAmount("1100");
            $transaction->setCurrency("EUR");

            $result = $method->invokeArgs($obj, [$transaction]);

            self::assertEquals(0.3, $result);
        }

        public function testCashOutCommissionOneTransactionLegalMin()
        {
            $method      = self::getMethod('Paysera\Controllers\TransactionController', 'cashOutCommission');
            $obj         = new TransactionController(new TransactionRepository(), $this->config);
            $transaction = new Transaction();
            $transaction->setDate("2016-01-05");
            $transaction->setUserId("1");
            $transaction->setUserType("business");
            $transaction->setTransactionType("withdraw");
            $transaction->setTransactionAmount("50");
            $transaction->setCurrency("EUR");

            $result = $method->invokeArgs($obj, [$transaction]);

            self::assertEquals(0.25, $result);
        }

        public function testCashOutCommissionOneTransactionLegalMax()
        {
            $method      = self::getMethod('Paysera\Controllers\TransactionController', 'cashOutCommission');
            $obj         = new TransactionController(new TransactionRepository(), $this->config);
            $transaction = new Transaction();
            $transaction->setDate("2016-01-05");
            $transaction->setUserId("1");
            $transaction->setUserType("private");
            $transaction->setTransactionType("withdraw");
            $transaction->setTransactionAmount("5000");
            $transaction->setCurrency("EUR");

            $result = $method->invokeArgs($obj, [$transaction]);

            self::assertEquals(12, $result);
        }*/

}
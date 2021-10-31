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
     * MoneyTransactionServicesTest constructor.
     */

    public function __construct()
    {
        parent::__construct();
        $this->setting = include('./setting.php');
        $this->services = new MoneyTransactionServices(new TransactionRepository(), $this->setting);
        $this->transactionModel = new TransactionModel();
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
        $result = $method->invokeArgs($this->services, [$this->transactionModel, $this->setting]);
        $this->assertEquals(3.00, $result);
    }

    protected static function getMethod($class, $name)
    {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    public function testDepositCommissionUSD()
    {
        $method = self::getMethod('CommissionTask\Service\MoneyTransactionServices', 'depositCommission');
        $this->transactionModel->setDate("2016-01-05");
        $this->transactionModel->setUserId("1");
        $this->transactionModel->setUserType("private");
        $this->transactionModel->setTransactionType("deposit");
        $this->transactionModel->setTransactionAmount("100.00");
        $this->transactionModel->setCurrency("USD");
        $result = $method->invokeArgs($this->services, [$this->transactionModel, $this->setting]);
        $this->assertEquals(0.03, $result);
    }

    public function testDepositCommissionJPY()
    {
        $method = self::getMethod('CommissionTask\Service\MoneyTransactionServices', 'depositCommission');
        $this->transactionModel->setDate("2016-01-05");
        $this->transactionModel->setUserId("1");
        $this->transactionModel->setUserType("private");
        $this->transactionModel->setTransactionType("deposit");
        $this->transactionModel->setTransactionAmount("10000");
        $this->transactionModel->setCurrency("JPY");
        $result = $method->invokeArgs($this->services, [$this->transactionModel, $this->setting]);
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
        $result = $this->convertCurrency($this->transactionModel, $this->setting);
        $precision = $this->setting['currencyConversion'][$this->transactionModel->getCurrency()]['precision'];
        $this->assertEquals(78.00, $this->roundUp($result, $this->setting, $precision));
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
        $precision = $this->setting['currencyConversion'][$this->transactionModel->getCurrency()]['precision'];
        $this->assertEquals(12953, $this->roundUp($result, $this->setting, $precision));
    }

    public function testWithdrawCommissionTransactionPrivate()
    {
        $method = self::getMethod('CommissionTask\Service\MoneyTransactionServices', 'withdrawCommission');
        $this->transactionModel->setDate("2016-01-05");
        $this->transactionModel->setUserId("1");
        $this->transactionModel->setUserType("private");
        $this->transactionModel->setTransactionType("withdraw");
        $this->transactionModel->setTransactionAmount("1100");
        $this->transactionModel->setCurrency("EUR");
        $result = $method->invokeArgs($this->services, [$this->transactionModel, $this->setting]);
        $this->assertEquals(0.3, $result);
    }

    public function testWithdrawCommissionTransactionBusiness()
    {
        $method = self::getMethod('CommissionTask\Service\MoneyTransactionServices', 'withdrawCommission');
        $this->transactionModel->setDate("2016-01-05");
        $this->transactionModel->setUserId("1");
        $this->transactionModel->setUserType("business");
        $this->transactionModel->setTransactionType("withdraw");
        $this->transactionModel->setTransactionAmount("50");
        $this->transactionModel->setCurrency("EUR");
        $result = $method->invokeArgs($this->services, [$this->transactionModel, $this->setting]);
        $this->assertEquals(0.25, $result);
    }
}
<?php

use CommissionTask\Service\MoneyTransactionServices;
use CommissionTask\Repositories\TransactionRepository;

require_once __DIR__ . '/vendor/autoload.php';

if ($argc != 2) {
    die('File not specified');
}

$setting = include('setting.php');

$transactionController = new MoneyTransactionServices(new TransactionRepository() , $setting);
$transactionController->index($argv[1]);
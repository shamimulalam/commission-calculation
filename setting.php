<?php
return [
    'depositCommissionPercent' => 0.0003,
    'withdrawCommissionPercentCommon' => 0.003,
    'withdrawBusinessCommissionPercent' => 0.005,
    'withdrawCommissionCommonFreeTransactionsLimit' => 3,
    'withdrawCommissionCommonDiscount' => 1000.00,
    'commissionPrecision' => 2,
    'currencyConversion' => [
        'EUR' => [
            'rate' => 1,
            'precision' => 2,
        ],
        'USD' => [
            'rate' => 1.1497,
            'precision' => 2,
        ],
        'JPY' => [
            'rate' => 129.53,
            'precision' => 0,
        ]
    ],
    "user_types" => [
        "private" => [
            "commision_rate" => ".30"
        ],
        "business" => [
            "commision_rate" => ".30"
        ]
    ]

];
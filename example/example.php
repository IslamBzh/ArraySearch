<?php

require '../vendor/autoload.php';

use Islambzh\ArraySearch\ArraySearch;

$array = [
    'items' => [
        [
            'price' => 190,
            'name' => 'DS 1',
            'art' => 'DS-1542'
        ],
        [
            'price' => 120,
            'name' => 'DS 2',
            'art' => 'DS-1543'
        ]
    ],
    'name' => 'Shop #1',
    'info' => [
        'phone' => '+79999999999',
        'zip' => '123456',
        'city' => 'Moscow',
        'street' => 'Lenina'
    ]
];

var_dump(ArraySearch::getArray($array, 'name'));
// string(7) "Shop #1"

var_dump(ArraySearch::getArray($array, 'info', 'zip'));
// string(6) "123456"

var_dump(ArraySearch::getArray($array, 'items', ['price' => 120]));
// array(3) {
//   ["price"]=>
//   int(120)
//   ["name"]=>
//   string(4) "DS 2"
//   ["art"]=>
//   string(7) "DS-1543"
// }

$params = [
    'name',
    'phone' => ['info', 'phone'],
    'item' => ['items', ['price' => 120]]
];
var_dump(ArraySearch::intersectArray($array, $params));
// array(3) {
//   ["name"]=>
//   string(7) "Shop #1"
//   ["phone"]=>
//   string(12) "+79999999999"
//   ["item"]=>
//   array(3) {
//     ["price"]=>
//     int(120)
//     ["name"]=>
//     string(4) "DS 2"
//     ["art"]=>
//     string(7) "DS-1543"
//   }
// }
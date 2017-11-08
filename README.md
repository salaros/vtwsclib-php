[![vTiger versions](https://img.shields.io/badge/vTiger-5.x%20|%206.x%20|%207.x-green.svg)](https://wiki.vtiger.com/index.php/Webservices_tutorials)
[![License](https://img.shields.io/packagist/l/salaros/vtwsclib-php.svg)](https://raw.githubusercontent.com/salaros/vtwsclib-php/master/LICENSE)
[![Packagist version](https://img.shields.io/packagist/v/salaros/vtwsclib-php.svg)](https://packagist.org/packages/salaros/vtwsclib-php)
[![Packagist downloads](https://img.shields.io/packagist/dt/salaros/vtwsclib-php.svg)](https://packagist.org/packages/salaros/vtwsclib-php)
[![Monthly Downloads](https://img.shields.io/packagist/dm/doctrine/orm.svg)]()

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/salaros/vtwsclib-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/salaros/vtwsclib-php/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/salaros/vtwsclib-php/badges/build.png?b=master)](https://scrutinizer-ci.com/g/salaros/vtwsclib-php/build-status/master)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/f5764af3-0382-444c-ada6-3c2b0f8bf39b.svg)](https://insight.sensiolabs.com/projects/f5764af3-0382-444c-ada6-3c2b0f8bf39b)
[![Dependency Status](https://www.versioneye.com/user/projects/555af8f2634daacd41000171/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/555af8f2634daacd41000171)
[![composer.lock](https://poser.pugx.org/salaros/vtwsclib-php/composerlock)](https://packagist.org/packages/salaros/vtwsclib-php)

vtwsclib-php
============

A PHP client for [vTiger](https://www.vtiger.com/)'s [Web Services](https://wiki.vtiger.com/index.php/Webservices_tutorials) APIs. Works well vTiger **forks** such as [VTE CRM](http://vtecrm.com/en/).

## Installing via Composer

The recommended way to install **vtwsclib-php** is through [Composer](https://getcomposer.org/download/).

    composer require "salaros/vtwsclib-php:*"

..or edit your composer.json file manually by appending *salaros/vtwsclib-php*:

    "require": {
        ...
        "salaros/vtwsclib-php": "*"
    }

## How to use

Here are some examples of how to use vtwsclib-php:

```php
<?php
require 'vendor/autoload.php';

use Salaros\Vtiger\VTWSCLib\WSClient;

$client = new WSClient('https://vtiger.mycompany.com/', 'admin', '<accessKey>');

// Let's print out the information about Leads module,
// including the list of required and optional fields etc
print_r($client->modules->getOne('Leads'));

// The output
Array
(
  [label] => Leads
  [name] => Leads
  [createable] => 1
  [updateable] => 1
  [deleteable] => 1
  [retrieveable] => 1
  [fields] => Array
    (
        ...
        [1] => Array
        (
          [name] => firstname
          [label] => First Name
          [mandatory] =>
          [type] => Array
            (
              [name] => string
            )

          [nullable] => 1
          [editable] => 1
          [default] =>
        )
        ...
    )
)
```

### Create a new entity

Check if a lead record exists, by using the first and the last name as constraints:

```php
// Look for Ivan Petrov among Leads
$ivanPetrovId = $client->entities->getNumericID('Leads', [
    'firstname'         => 'Ivan',
    'lastname'          => 'Petrov',
]);

// Add his record if it doesn't exist
if (intval($ivanPetrovId) < 1) {
    $ivanPetrov = $client->entities->createOne('Leads', [
        'salutationtype'    => 'Mr.',
        'firstname'         => 'Ivan',
        'lastname'          => 'Petrov',
        'phone'             => "+7 495 4482237",
        'fax'               => "+7 495 3895096",
        'company'           => "ACME Ltd",
        'email'             => "roga-kopyta.ru",
        'leadsource'        => "Trade Show",
        'website'           => "roga-kopyta.ru",
        'leadstatus'        => "Cold",
    ]);
    print_r($ivanPetrov);
} else {
    echo('Ivan Petrov\'s record already exists!' . PHP_EOL);
}

// The output
Array
(
    [salutationtype] => Mr.
    [firstname] => Ivan
    [lead_no] => LEA4
    [phone] => +7 495 4482237
    [lastname] => Petrov
    [mobile] =>
    [company] => ACME Ltd
    [fax] => +7 495 3895096
    [email] => roga-kopyta.ru
    [leadsource] => Trade Show
    [website] => roga-kopyta.ru
    [leadstatus] => Cold
    [annualrevenue] => 0.00000000
    ...
    [id] => 10x9
)
```

### Look for an existing entry

Check if a contact record exists, by using the first and the last name as constraints. If there is a John Smith among your contacts the code below will print out only some fields (listed below). This is useful when you don't need all the information available.

```php
// Look for John Smith in Contacts
// and print out only some fields
$johnSmith = $client->entities->findOne('Contacts', [
    'firstname'             => 'John',
    'lastname'              => 'Smith',
], [
    'id',
    'salutationtype',
    'firstname',
    'lastname',
    'email',
    'phone'
]);
if (false !== $johnSmith) {
    print_r($johnSmith);
} else {
    echo('John Smith\'s record doesn\'t exists!' . PHP_EOL);
}

// The output
Array
(
    [id] => 12x3
    [salutationtype] => Mr.
    [firstname] => John
    [lastname] => Smith
    [email] => smith@contoso.biz
    [phone] => +103030303
)
```

### Sync (list updated and deleted entries since ...)

There is another useful function provided by vTiger, it allows one to get the information about the items modified and/or deleted since a given date.

```php
// Fetch entities updated and/or deleted since
// the midnight of the first day of this month
$lastModTime = strtotime('first day of this month midnight');
$leadsSyncInfo = $client->entities->sync($lastModTime, 'Leads');
if (!isset($leadsSyncInfo['updated'])) {
    // ... do something with updated entries
}
if (!isset($leadsSyncInfo['deleted'])) {
    // ... and something else with deleted entries
}
```

### Other features

You can update, remove entities, run custom SQL-like queries and much more.

### More stuff (extensibility)

Please note that it's possible to add custom web service

```sql
INSERT INTO `vtiger_ws_operation` 
       ( `name`, `handler_path`, `handler_method`, `type`, `prelogin`)
VALUES ('new_operation', 'include/Webservices/MyCoolWebService.php', 'new_operation_method', 'PUT', 0);

-- Replace <new_operation_ID> with the actual ID generated by the query above 
INSERT INTO `vtiger_ws_operation_parameters` (`operationid`, `name`, `type`, `sequence`)
VALUES (<new_operation_ID>, 'someParam', 'String', 1);

INSERT INTO `vtiger_ws_operation_parameters` (`operationid`, `name`, `type`, `sequence`)
VALUES (<new_operation_ID>, 'dateParam', 'TimeStamp ', 2);
```

now create the following file `include/Webservices/MyCoolWebService.php`

```php
<?php

function new_operation_method($someParam, $dateParam){
    global $log,$adb;

    // ... do something here

    return $result;
}
```
So later you could use this new method via vtwsclib-php in this way

```php
<?php
$params = [
    'someParam' => 'foobar',
    'dateParam' => strtotime('now'),
];
$result = $client->invokeOperation('new_operation', $params, 'POST');
```

## More useful resources

* [vTiger Webservices Tutorials](https://wiki.vtiger.com/index.php/Webservices_tutorials)
* [vtwsclib-1.4.pdf](http://code.vtiger.com/vtiger/vtigercrm-sdk/blob/8230a46668d007ad1f01d2a892f5378c57f328c6/vtwsclib/1.4/vtwsclib-1.4.pdf) from http://code.vtiger.com

[![vTiger versions](https://img.shields.io/badge/vTiger-5.x%20|%206.x%20|%207.x-green.svg?maxAge=3600)](https://wiki.vtiger.com/index.php/Webservices_tutorials)
[![License](https://img.shields.io/packagist/l/salaros/vtwsclib-php.svg?maxAge=3600)](https://raw.githubusercontent.com/salaros/vtwsclib-php/master/LICENSE)
[![GitHub top language](https://img.shields.io/github/languages/top/salaros/vtwsclib-php.svg?maxAge=3600)](https://github.com/salaros/vtwsclib-php/search?l=php)
[![Packagist version](https://img.shields.io/packagist/v/salaros/vtwsclib-php.svg?maxAge=3600)](https://packagist.org/packages/salaros/vtwsclib-php)
[![Packagist downloads](https://img.shields.io/packagist/dt/salaros/vtwsclib-php.svg?maxAge=3600)](https://packagist.org/packages/salaros/vtwsclib-php)
[![Monthly Downloads](https://img.shields.io/packagist/dm/salaros/vtwsclib-php.svg?maxAge=3600)](https://packagist.org/packages/salaros/vtwsclib-php)

[![Documentation Status](https://readthedocs.org/projects/vtwsclib-php/badge/?version=latest)](http://vtwsclib-php.readthedocs.io/en/latest/?badge=latest)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/salaros/vtwsclib-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/salaros/vtwsclib-php/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/salaros/vtwsclib-php/badges/build.png?b=master)](https://scrutinizer-ci.com/g/salaros/vtwsclib-php/build-status/master)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/f5764af3-0382-444c-ada6-3c2b0f8bf39b.svg?maxAge=3600)](https://insight.sensiolabs.com/projects/f5764af3-0382-444c-ada6-3c2b0f8bf39b)
[![composer.lock](https://poser.pugx.org/salaros/vtwsclib-php/composerlock)](https://packagist.org/packages/salaros/vtwsclib-php)

[![Donate Patreon](https://img.shields.io/badge/donate-Patreon-f96854.svg)](https://www.patreon.com/salaros/)
[![Donate Paypal](https://img.shields.io/badge/donate-PayPal-009cde.svg)](https://paypal.me/salarosIT)
[![Donate Liberapay](https://img.shields.io/badge/donate-Liberapay-ffc600.svg)](https://liberapay.com/salaros/)

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

Here are some [examples](https://github.com/salaros/vtwsclib-php/wiki) of how to use vtwsclib-php:

```php
<?php
require 'vendor/autoload.php';

use Salaros\Vtiger\VTWSCLib\WSClient;

$client = new WSClient('https://vtiger.mycompany.com/', 'admin', '<accessKey>');
```

Here you can find more **[detailed examples](https://github.com/salaros/vtwsclib-php/wiki)** on how to use **vtwsclib-php**.

## Official documentation

The official documentation can be found [here](http://vtwsclib-php.readthedocs.io/en/latest/WSClient.html).

## Support
This is a development reposiroty for `vtwsclib-php` and should _not_ be used for support.
Please visit [StackOverflow's vtwsclib-php topic](https://stackoverflow.com/questions/tagged/vtwsclib-php) for any support request or click [here to ask a vtwsclib-php-related question](https://stackoverflow.com/questions/ask?tags=vtwsclib-php+vtiger+web-services+api+php).

## Contributions
Anyone is welcome to contribute to the development of this plugin. There are various ways to do so:

1. Found a bug? Raise an [issue](https://github.com/salaros/vtwsclib-php/issues?direction=desc&labels=bug&page=1&sort=created&state=open) on GitHub.
2. Send me a Pull Request with your bug fixes and/or new features.
3. Provide feedback and suggestions on [enhancements](https://github.com/salaros/vtwsclib-php/issues?direction=desc&labels=enhancement&page=1&sort=created&state=open).


## More useful resources
* [vTiger Webservices Tutorials](https://wiki.vtiger.com/index.php/Webservices_tutorials)
* [vtwsclib-1.4.pdf](http://code.vtiger.com/vtiger/vtigercrm-sdk/blob/8230a46668d007ad1f01d2a892f5378c57f328c6/vtwsclib/1.4/vtwsclib-1.4.pdf) from http://code.vtiger.com

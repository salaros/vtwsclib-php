[![License](https://img.shields.io/packagist/l/salaros/vtwsclib-php.svg)]()
[![Packagist](https://img.shields.io/packagist/v/salaros/vtwsclib-php.svg)](https://packagist.org/packages/salaros/vtwsclib-php)
[![Packagist](https://img.shields.io/packagist/dt/salaros/vtwsclib-php.svg)]()
[![Libraries.io for GitHub](https://img.shields.io/librariesio/github/phoenixframework/phoenix.svg)]()
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/f5764af3-0382-444c-ada6-3c2b0f8bf39b.svg)](https://insight.sensiolabs.com/projects/f5764af3-0382-444c-ada6-3c2b0f8bf39b)
[![composer.lock](https://poser.pugx.org/salaros/vtwsclib-php/composerlock?format=flat)](https://packagist.org/packages/salaros/vtwsclib-php)

vtwsclib-php
============

Vtiger Web Services PHP Client Library

## Installing via Composer

The recommended way to install **vtwsclib-php** is through [Composer](https://getcomposer.org/).

    # Install Composer
    curl -sS https://getcomposer.org/installer | php

Next, run the Composer command to install the latest stable version of Guzzle:

    composer require salaros/vtwsclib-php

..or edit your composer.json file manually by adding:

    {
        "require": {
            "salaros/vtwsclib-php": "dev-master"
        }
    }

After installing, you need to require Composer's autoloader:

    require 'vendor/autoload.php';

## How to use

    $client=new WSClient($url);
    $login=$client->login($user, $accessKey);
    
..new usage examples are coming soon..

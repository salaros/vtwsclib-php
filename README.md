[![Dependency Status](https://www.versioneye.com/user/projects/555af8f2634daacd41000171/badge.svg?style=flat)](https://www.versioneye.com/user/projects/555af8f2634daacd41000171)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f5764af3-0382-444c-ada6-3c2b0f8bf39b/mini.png)](https://insight.sensiolabs.com/projects/f5764af3-0382-444c-ada6-3c2b0f8bf39b)

# vtwsclib-php

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

#!/bin/bash

NAMESPACE="Salaros\\Vtiger\\VTWSCLib"

if [ -f ./vendor/sphpdox/sphpdox/sphpdox.php ]; then
    [[ ! -d ./vendor/sphpdox/sphpdox/ ]] && ln -sv $(pwd)/vendor/ ./vendor/sphpdox/sphpdox/
    ./vendor/sphpdox/sphpdox/sphpdox.php process ${NAMESPACE} ./src -o ./docs
else
    echo 'Please install sphpdox via composer require --dev "sphpdox/sphpdox:dev-master"'
fi


#!/bin/bash

NAMESPACE="Salaros\\Vtiger\\VTWSCLib"
NAMESPACE_PATH=$(echo ${NAMESPACE} | sed -e 's~\\~/~g')
    echo $NAMESPACE_PATH

if [ -f ./vendor/sphpdox/sphpdox/sphpdox.php ]; then
    [[ ! -d ./vendor/sphpdox/sphpdox/ ]] && ln -sv $(pwd)/vendor/ ./vendor/sphpdox/sphpdox/
    ./vendor/sphpdox/sphpdox/sphpdox.php process ${NAMESPACE} ./src -o ./docs/build

    mv -v ./docs/build/$NAMESPACE_PATH/* ./docs/
    rm -rf ./docs/build/./*
else
    echo 'Please install sphpdox via composer require --dev "sphpdox/sphpdox:dev-master"'
fi


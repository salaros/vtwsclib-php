#!/bin/bash

NAMESPACE="Salaros\\Vtiger\\VTWSCLib"
NAMESPACE_2_PATH=$(echo ${NAMESPACE} | sed -e 's~\\~/~g')
BUILD_PATH="./_build"
SPHPDOX_PATH="../vendor/sphpdox/sphpdox"
SRC_PATH="../src"

if [ -f ${SPHPDOX_PATH}/sphpdox.php ]; then
    [[ ! -d ${SPHPDOX_PATH}/ ]] && ln -sv $(pwd)/vendor/ ${SPHPDOX_PATH}/
    php -f ${SPHPDOX_PATH}/sphpdox.php process ${NAMESPACE} ${SRC_PATH} -o ${BUILD_PATH}

    mv -v ${BUILD_PATH}/$NAMESPACE_2_PATH/* ./
    rm -rf ${BUILD_PATH}/./*
else
    echo 'Please install sphpdox via composer require --dev "sphpdox/sphpdox:dev-master"'
fi


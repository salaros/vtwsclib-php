<?php
/**
* Vtiger Web Services PHP Client Library
*
* The MIT License (MIT)
*
* Copyright (c) 2015, Zhmayev Yaroslav <salaros@salaros.com>
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
* @author    Zhmayev Yaroslav <salaros@salaros.com>
* @copyright 2015-2016 Zhmayev Yaroslav
* @license   The MIT License (MIT)
*/

namespace Salaros\Vtiger\VTWSCLib;

use Salaros\Vtiger\VTWSCLib\WSClient;

/**
* Vtiger Web Services PHP Client Session class
*
* Class Modules
* @package Salaros\Vtiger\VTWSCLib
*/
class Modules
{
    private $wsClient;

    /**
     * Class constructor
     * @param string $wsClient  Parent WSClient instance
     */
    public function __construct($wsClient)
    {
        $this->wsClient = $wsClient;
    }

    /**
     * Lists all the Vtiger entity types available through the API
     * @access public
     * @return array List of entity types
     */
    public function getAll()
    {
        $result = $this->wsClient->invokeOperation('listtypes', [ ], 'GET');
        $modules = $result[ 'types' ];

        $result = array();
        foreach ($modules as $moduleName) {
            $result[ $moduleName ] = [ 'name' => $moduleName ];
        }
        return $result;
    }

    /**
     * Get the type information about a given VTiger entity type.
     * @access public
     * @param  string $moduleName Name of the module / entity type
     * @return array  Result object
     */
    public function getOne($moduleName)
    {
        return $this->wsClient->invokeOperation('describe', [ 'elementType' => $moduleName ], 'GET');
    }

    /**
     * Gets the entity ID prepended with module / entity type ID
     * @access private
     * @param  string       $moduleName   Name of the module / entity type
     * @param  string       $entityID     Numeric entity ID
     * @return boolean|string Returns false if it is not possible to retrieve module / entity type ID
     */
    public function getTypedID($moduleName, $entityID)
    {
        if (stripos((string) $entityID, 'x') !== false) {
            return $entityID;
        }

        if (empty($entityID) || intval($entityID) < 1) {
            throw new WSException('Entity ID must be a valid number');
        }

        $type = $this->getType($moduleName);
        if (!$type || !array_key_exists('idPrefix', $type)) {
            $errorMessage = sprintf("The following module is not installed: %s", $moduleName);
            throw new WSException($errorMessage);
        }

        return "{$type[ 'idPrefix' ]}x{$entityID}";
    }
}

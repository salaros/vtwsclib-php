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

use Salaros\Vtiger\VTWSCLib\Entities;
use Salaros\Vtiger\VTWSCLib\Modules;
use Salaros\Vtiger\VTWSCLib\Session;
use Salaros\Vtiger\VTWSCLib\WSException;

/**
 * Vtiger Web Services PHP Client
 *
 * Class WSClient
 * @package Salaros\Vtiger\VTWSCLib
 */
class WSClient
{
    private $session = null;
    
    public $modules = null;
    public $entities = null;

    const USE_ACCESSKEY = 1;
    const USE_PASSWORD = 2;
    
    /**
     * Class constructor
     * @param string $vtigerUrl  The URL of the remote WebServices server
     * @param  string $username  User name
     * @param  string $secret  Access key token (shown on user's profile page) or password, depends on $loginMode
     * @param  integer [$loginMode = self::USE_ACCESSKEY|USE_PASSWORD]  Login mode, defaults to username + accessKey
     * @param string [$wsBaseURL = 'webservice.php']  WebServices base URL appended to vTiger root URL
     * @param int Optional request timeout in seconds
     */
    public function __construct($vtigerUrl, $username, $secret, $loginMode = self::USE_ACCESSKEY, $wsBaseURL = 'webservice.php', $requestTimeout = 0)
    {
        $this->modules = new Modules($this);
        $this->entities = new Entities($this);
        $this->session = new Session($vtigerUrl, $wsBaseURL, $requestTimeout);

        $loginOK = false;
        switch ($loginMode) {
            case self::USE_ACCESSKEY:
                $loginOK = $this->session->login($username, $secret);
                break;

            case self::USE_PASSWORD:
                $loginOK = $this->session->loginPassword($username, $secret);
                break;
            
            default:
                throw new WSException(sprintf('Unknown login mode: %s', $loginMode));
        }

        if (!$loginOK) {
            throw new WSException(sprintf(
                'Failed to log into vTiger CRM (User: %s, URL: %s)',
                $username,
                $vtigerUrl
            ));
        }
    }

    /**
     * Invokes custom operation (defined in vtiger_ws_operation table)
     * @access public
     * @param  string  $operation  Name of the webservice to invoke
     * @param  array   [$params = null] Parameter values to operation
     * @param  string  [$method = 'POST'] HTTP request method (GET, POST etc)
     * @return array Result object
     */
    public function invokeOperation($operation, array $params = null, $method = 'POST')
    {
        if (is_array($params) && !empty($params) && !is_assoc_array($params)) {
            throw new WSException(
                "You have to specified a list of operation parameters, but apparently 
                it's not an associative array ('prop' => value)!"
            );
        }

        $params[ 'operation' ] = $operation;
        return $this->session->sendHttpRequest($params, $method);
    }

    /**
     * VTiger provides a simple query language for fetching data.
     * This language is quite similar to select queries in SQL.
     * There are limitations, the queries work on a single Module,
     * embedded queries are not supported, and does not support joins.
     * But this is still a powerful way of getting data from Vtiger.
     * Query always limits its output to 100 records,
     * Client application can use limit operator to get different records.
     * @access public
     * @param  string $query SQL-like expression
     * @return array  Query results
     */
    public function runQuery($query)
    {
        // Make sure the query ends with ;
        $query = (strripos($query, ';') != strlen($query) - 1)
            ? trim($query .= ';')
            : trim($query);

        return $this->invokeOperation('query', [ 'query' => $query ], 'GET');
    }

    /**
     * Gets an array containing the basic information about current API user
     * @access public
     * @return array Basic information about current API user
     */
    public function getCurrentUser()
    {
        return $this->session->getUserInfo();
    }

    /**
     * Gets an array containing the basic information about the connected vTiger instance
     * @access public
     * @return array Basic information about the connected vTiger instance
     */
    public function getVtigerInfo()
    {
        return [
            'vtiger' => $this->session->getVtigerVersion(),
            'api' => $this->session->getVtigerApiVersion(),
        ];
    }
}

if (!function_exists('is_assoc_array')) {

    /**
     * Checks if an array is associative or not
     * @param  string  Array to test
     * @return boolean Returns true in a given array is associative and false if it's not
     */
    function is_assoc_array(array $array)
    {
        if (empty($array) || !is_array($array)) {
            return false;
        }

        foreach (array_keys($array) as $key) {
            if (!is_int($key)) {
                return true;
            }
        }
        return false;
    }
}

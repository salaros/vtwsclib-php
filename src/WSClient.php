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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Vtiger Web Services PHP Client
 *
 * Class WSClient
 * @package Salaros\Vtiger\VTWSCLib
 */
class WSClient
{
    // HTTP Client instance
    protected $httpClient = null;

    // Service URL to which client connects to
    protected $serviceBaseURL = 'webservice.php';

    // Webservice login validity
    private $serviceServerTime = false;
    private $serviceExpireTime = false;
    private $serviceToken = false;

    // Webservice user credentials
    private $userName = false;
    private $accessKey = false;

    // Webservice login credentials
    private $userID = false;
    private $sessionName = false;

    // Vtiger CRM and WebServices API version
    private $apiVersion = false;
    private $vtigerVersion = false;

    // Last operation error information
    protected $lastErrorMessage = null;

    /**
     * Class constructor
     * @param string $url The URL of the remote WebServices server
     */
    public function __construct($url)
    {
        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = sprintf('http://%s', $url);
        }
        if (strripos($url, '/') != (strlen($url)-1)) {
            $url .= '/';
        }

        // Gets target URL for WebServices API requests
        $this->httpClient = new Client([
            'base_uri' => $url
        ]);
    }

    /**
     * Check if server response contains an error, therefore the requested operation has failed
     * @access private
     * @param  array $jsonResult Server response object to check for errors
     * @return boolean  True if response object contains an error
     */
    private function checkForError(array $jsonResult)
    {
        if (isset($jsonResult['success']) && (bool)$jsonResult['success'] === true) {
            $this->lastErrorMessage = null;
            return false;
        }

        $this->lastErrorMessage = new WSClientError(
            $jsonResult['error']['message'],
            $jsonResult['error']['code']
        );

        return true;
    }

    /**
     * Checks and performs a login operation if requried and repeats login if needed
     * @access private
     */
    private function checkLogin()
    {
        if (time() <= $this->serviceExpireTime) {
            return;
        }
        $this->login($this->userName, $this->accessKey);
    }

    /**
     * Sends HTTP request to VTiger web service API endpoint
     * @access private
     * @param  array $requestData HTTP request data
     * @param  string $method HTTP request method (GET, POST etc)
     * @return array Returns request result object (null in case of failure)
     */
    private function sendHttpRequest(array $requestData, $method = 'POST')
    {
        try {
            switch ($method) {
                case 'GET':
                    $response = $this->httpClient->get($this->serviceBaseURL, ['query' => $requestData]);
                    break;
                case 'POST':
                    $response = $this->httpClient->post($this->serviceBaseURL, ['form_params' => $requestData]);
                    break;
                default:
                    $this->lastErrorMessage = new WSClientError("Unknown request type {$method}");
                    return null;
            }
        } catch (RequestException $ex) {
            $this->lastError = new WSClientError(
                $ex->getMessage(),
                $ex->getCode()
            );
            return null;
        }

        $jsonRaw = $response->getBody();
        $jsonObj = json_decode($jsonRaw, true);

        return (!is_array($jsonObj) || $this->checkForError($jsonObj))
            ? null
            : $jsonObj['result'];
    }

    /**
     * Gets a challenge token from the server and stores for future requests
     * @access private
     * @param  string $username VTiger user name
     * @return booleanReturns false in case of failure
     */
    private function passChallenge($username)
    {
        $getdata = [
            'operation' => 'getchallenge',
            'username'  => $username
        ];
        $result = $this->sendHttpRequest($getdata, 'GET');
        
        if (!is_array($result) || !isset($result['token'])) {
            return false;
        }

        $this->serviceServerTime = $result['serverTime'];
        $this->serviceExpireTime = $result['expireTime'];
        $this->serviceToken = $result['token'];

        return true;
    }

    /**
     * Login to the server using username and VTiger access key token
     * @access public
     * @param  string $username VTiger user name
     * @param  string $accessKey VTiger access key token (visible on user profile/settings page)
     * @return boolean Returns true if login operation has been successful
     */
    public function login($username, $accessKey)
    {
        // Do the challenge before loggin in
        if ($this->passChallenge($username) === false) {
            return false;
        }

        $postdata = [
            'operation' => 'login',
            'username'  => $username,
            'accessKey' => md5($this->serviceToken.$accessKey)
        ];

        $result = $this->sendHttpRequest($postdata);
        if (!$result || !is_array($result)) {
            return false;
        }

        // Backuping logged in user credentials
        $this->userName = $username;
        $this->accessKey = $accessKey;

        // Session data
        $this->sessionName = $result['sessionName'];
        $this->userID = $result['userId'];

        // Vtiger CRM and WebServices API version
        $this->apiVersion = $result['version'];
        $this->vtigerVersion = $result['vtigerVersion'];

        return true;
    }

    /**
     * Allows you to login using username and password instead of access key (works on some VTige forks)
     * @access public
     * @param  string $username VTiger user name
     * @param  string $password VTiger password (used to access CRM using the standard login page)
     * @param  string $accessKey This parameter will be filled with user's VTiger access key
     * @return boolean  Returns true if login operation has been successful
     */
    public function loginPassword($username, $password, &$accessKey = null)
    {
        // Do the challenge before loggin in
        if ($this->passChallenge($username) === false) {
            return false;
        }

        $postdata = [
            'operation' => 'login_pwd',
            'username' => $username,
            'password' => $password
        ];

        $result = $this->sendHttpRequest($postdata);
        if (!$result || !is_array($result) || count($result) !== 1) {
            return false;
        }

        $accessKey = array_key_exists('accesskey', $result)
            ? $result['accesskey']
            : $result[0];

        return $this->login($username, $accessKey);
    }

    /**
     * Gets last operation error, if any
     * @access public
     * @return WSClientError The error object
     */
    public function getLastError()
    {
        return $this->lastErrorMessage;
    }

    /**
     * Returns the client library version.
     * @access public
     * @return string Client library version
     */
    public function getVersion()
    {
        global $wsclient_version;
        return $wsclient_version;
    }

    /**
     * Lists all the Vtiger entity types available through the API
     * @access public
     * @return array List of entity types
     */
    public function getTypes()
    {
        // Perform re-login if required.
        $this->checkLogin();

        $getdata = [
            'operation' => 'listtypes',
            'sessionName'  => $this->sessionName
        ];

        $result = $this->sendHttpRequest($getdata, 'GET');
        $modules = $result['types'];

        $result = array();
        foreach ($modules as $moduleName) {
            $result[$moduleName] = ['name' => $moduleName];
        }
        return $result;
    }

    /**
     * Get the type information about a given VTiger entity type.
     * @access public
     * @param  string $moduleName Name of the module / entity type
     * @return array  Result object
     */
    public function getType($moduleName)
    {
        // Perform re-login if required.
        $this->checkLogin();

        $getdata = [
            'operation' => 'describe',
            'sessionName'  => $this->sessionName,
            'elementType' => $moduleName
        ];

        return $this->sendHttpRequest($getdata, 'GET');
    }

    /**
     * Gets the entity ID prepended with module / entity type ID
     * @access private
     * @param  string       $moduleName   Name of the module / entity type
     * @param  string       $entityID     Numeric entity ID
     * @return boolean|string Returns false if it is not possible to retrieve module / entity type ID
     */
    private function getTypedID($moduleName, $entityID)
    {
        if (stripos((string)$entityID, 'x') !== false) {
            return $entityID;
        }

        if (empty($entityID) || intval($entityID) < 1) {
            throw new \Exception('Entity ID must be a valid number');
        }

        $type = $this->getType($moduleName);
        if (!$type || !array_key_exists('idPrefix', $type)) {
            $errorMessage = sprintf("The following module is not installed: %s", $moduleName);
            $this->lastErrorMessage = new WSClientError($errorMessage);
            return false;
        }

        return "{$type['idPrefix']}x{$entityID}";
    }

    /**
     * Invokes custom operation (defined in vtiger_ws_operation table)
     * @access public
     * @param  string  $operation  Name of the webservice to invoke
     * @param  array   [$params = null] Parameter values to operation
     * @param  string  [$method   = 'POST'] HTTP request method (GET, POST etc)
     * @return array Result object
     */
    public function invokeOperation($operation, array $params = null, $method = 'POST')
    {
        if (!empty($params) || !is_array($params) || !$this->isAssocArray($params)) {
            $this->lastErrorMessage = new WSClientError(
                "You have to specified a list of operation parameters, 
                but apparently it's not an associative array ('prop' => value), you must fix it!"
            );
            return false;
        }

        // Perform re-login if required
        $this->checkLogin();

        $requestData = [
            'operation' => $operation,
            'sessionName' => $this->sessionName
        ];

        if (!empty($params) && is_array($params)) {
            $requestData = array_merge($requestData, $params);
        }

        return $this->sendHttpRequest($requestData, $method);
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
    public function query($query)
    {
        // Perform re-login if required.
        $this->checkLogin();

        // Make sure the query ends with ;
        $query = (strripos($query, ';') != strlen($query)-1)
            ? trim($query .= ';')
            : trim($query);

        $getdata = [
            'operation' => 'query',
            'sessionName' => $this->sessionName,
            'query' => $query
        ];

        return $this->sendHttpRequest($getdata, 'GET');
    }

    /**
     * Retrieves an entity by ID
     * @param  string $moduleName The name of the module / entity type
     * @param  string $entityID The ID of the entity to retrieve
     * @return boolean  Entity data
     */
    public function entityRetrieveByID($moduleName, $entityID)
    {
        // Perform re-login if required.
        $this->checkLogin();

        // Preprend so-called moduleid if needed
        $entityID = $this->getTypedID($moduleName, $entityID);

        $getdata = [
            'operation' => 'retrieve',
            'sessionName' => $this->sessionName,
            'id' => $entityID
        ];

        return $this->sendHttpRequest($getdata, 'GET');
    }

    /**
     * Uses VTiger queries to retrieve the entity matching a list of constraints
     * @param  string  $moduleName   The name of the module / entity type
     * @param  array   $params  Data used to find a matching entry
     * @return array   $select  The list of fields to select (defaults to SQL-like '*' - all the fields)
     * @return int  The matching record
     */
    public function entityRetrieve($moduleName, array $params, array $select = [])
    {
        $records = $this->entitiesRetrieve($moduleName, $params, $select, 1);
        if (false === $records || !isset($records[0])) {
            return false;
        }
        return $records[0];
    }

    /**
     * Retrieves the ID of the entity matching a list of constraints + prepends '<module_id>x' string to it
     * @param  string $moduleName   The name of the module / entity type
     * @param  array   $params  Data used to find a matching entry
     * @return int  Type ID (a numeric ID + '<module_id>x')
     */
    public function entityRetrieveTypeID($moduleName, array $params)
    {
        $records = $this->entitiesRetrieve($moduleName, $params, ['id'], 1);
        if (false === $records || !isset($records[0]['id']) || empty($records[0]['id'])) {
            return false;
        }
        return $records[0]['id'];
    }

    /**
     * Retrieve a numeric ID of the entity matching a list of constraints
     * @param  string $moduleName   The name of the module / entity type
     * @param  array   $params  Data used to find a matching entry
     * @return int  Numeric ID
     */
    public function entityRetrieveID($moduleName, array $params)
    {
        $entityID = $this->entityRetrieveTypeID($moduleName, $params);
        $entityIDParts = explode('x', $entityID, 2);
        return (is_array($entityIDParts) && count($entityIDParts) === 2)
            ? $entityIDParts[1]
            : -1;
    }

    /**
     * Creates an entity for the giving module
     * @param  string $moduleName   Name of the module / entity type for which the entry has to be created
     * @param  array $params Entity data
     * @return array  Entity creation results
     */
    public function entityCreate($moduleName, array $params)
    {
        if (!$this->checkParams($params, 'be able to create an entity')) {
            return false;
        }

        // Perform re-login if required.
        $this->checkLogin();

        // Assign record to logged in user if not specified
        if (!isset($params['assigned_user_id'])) {
            $params['assigned_user_id'] = $this->userID;
        }

        $postdata = [
            'operation'   => 'create',
            'sessionName' => $this->sessionName,
            'elementType' => $moduleName,
            'element'     => json_encode($params)
        ];

        return $this->sendHttpRequest($postdata);
    }

    /**
     * Updates an entity
     * @param  string $moduleName   The name of the module / entity type
     * @param  array $params Entity data
     * @return array  Entity update result
     */
    public function entityUpdate($moduleName, array $params)
    {
        if (!$this->checkParams($params, 'be able to update the entity(ies)')) {
            return false;
        }

        // Fail if no ID was supplied
        if (!array_key_exists('id', $params) || empty($params['id'])) {
            $this->lastErrorMessage = new WSClientError(
                "The list of contraints must contain a valid ID"
            );
            return false;
        }

        // Perform re-login if required.
        $this->checkLogin();

        // Preprend so-called moduleid if needed
        $params['id'] = $this->getTypedID($moduleName, $params['id']);

        // Check if the entity exists + retrieve its data so it can be used below
        $entityData = $this->entityRetrieve($moduleName, [ 'id' => $params['id'] ]);
        if ($entityData === false && !is_array($entityData)) {
            $this->lastErrorMessage = new WSClientError("Such entity doesn't exist, so it cannot be updated");
            return false;
        }

        // The new data overrides the existing one needed to provide
        // mandatory field values to WS 'update' operation
        $params = array_merge(
            $entityData,
            $params
        );

        $postdata = [
            'operation'   => 'update',
            'sessionName' => $this->sessionName,
            'elementType' => $moduleName,
            'element'     => json_encode($params)
        ];

        return $this->sendHttpRequest($postdata);
    }

    /**
     * Provides entity removal functionality
     * @param  string $moduleName   The name of the module / entity type
     * @param  string $entityID The ID of the entity to delete
     * @return array  Removal status object
     */
    public function entityDelete($moduleName, $entityID)
    {
        // Perform re-login if required.
        $this->checkLogin();

        // Preprend so-called moduleid if needed
        $entityID = $this->getTypedID($moduleName, $entityID);

        $postdata = [
            'operation' => 'delete',
            'sessionName' => $this->sessionName,
            'id' => $entityID
        ];

        return $this->sendHttpRequest($postdata);
    }

    /**
     * Retrieves multiple records using module name and a set of constraints
     * @param  string   $moduleName  The name of the module / entity type
     * @param  array    $params  Data used to find matching entries
     * @return array    $select  The list of fields to select (defaults to SQL-like '*' - all the fields)
     * @return int      $limit  limit the list of entries to N records (acts like LIMIT in SQL)
     * @return bool|array  The array containing matching entries or false if nothing was found
     */
    public function entitiesRetrieve($moduleName, array $params, array $select = [], $limit = 0)
    {
        if (!$this->checkParams($params, 'be able to retrieve entity(ies)')) {
            return false;
        }

        // Perform re-login if required.
        $this->checkLogin();

        // Builds the query
        $query = $this->buildQuery($moduleName, $params, $select, $limit);

        // Run the query
        $records = $this->query($query);
        if (false === $records || !is_array($records) || (count($records) <= 0)) {
            return false;
        }

        return $records;
    }

    /**
     * Sync will return a sync result object containing details of changes after modifiedTime
     * @param  int [$modifiedTime = null]    The date of the first change
     * @param  string [$moduleName = null]   The name of the module / entity type
     * @return array  Sync result object
     */
    public function entitiesSync($modifiedTime = null, $moduleName = null)
    {
        // Perform re-login if required.
        $this->checkLogin();

        $modifiedTime = (empty($modifiedTime))
            ? strtotime('today midnight')
            : intval($modifiedTime);

        $requestData = [
            'operation' => 'sync',
            'sessionName' => $this->sessionName,
            'modifiedTime' => $modifiedTime
        ];

        if (!empty($moduleName)) {
            $requestData['elementType'] = $moduleName;
        }

        return $this->sendHttpRequest($requestData, true);
    }

    /**
     * Builds the query using the supplied parameters
     * @param  string   $moduleName  The name of the module / entity type
     * @param  array    $params  Data used to find matching entries
     * @return array    $select  The list of fields to select (defaults to SQL-like '*' - all the fields)
     * @return int      $limit  limit the list of entries to N records (acts like LIMIT in SQL)
     * @return string   The query build out of the supplied parameters
     */
    private function buildQuery($moduleName, array $params, array $select = [], $limit = 0)
    {
        $criteria = array();
        $select=(empty($select)) ? '*' : implode(',', $select);
        $query=sprintf("SELECT %s FROM $moduleName WHERE ", $select);
        foreach ($params as $param => $value) {
            $criteria[] = "{$param} LIKE '{$value}'";
        }

        $query.=implode(" AND ", $criteria);
        if (intval($limit) > 0) {
            $query.=sprintf(" LIMIT %s", intval($limit));
        }
        return $query;
    }

    /**
     * Checks if if params holds valid entity data/search constraints, otherwise returns false
     * @param  array    $params  Array holding entity data/search constraints
     * @return boolean  Returns true if params holds valid entity data/search constraints, otherwise returns false
     */
    private function checkParams(array $params, $paramsPurpose)
    {
        if (empty($params) || !is_array($params) || !$this->isAssocArray($params)) {
            $this->lastErrorMessage = new WSClientError(sprintf(
                "You have to specify at least one search parameter (prop => value) in order to %s",
                $paramsPurpose
            ));
            return false;
        }
        return true;
    }

    /**
     * A helper method, used to check in an array is associative or not
     * @param  string  Array to test
     * @return boolean Returns true in a given array is associative and false if it's not
     */
    private function isAssocArray(array $array)
    {
        foreach (array_keys($array) as $key) {
            if (!is_int($key)) {
                return true;
            }
        }
        return false;
    }
}

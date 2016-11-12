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
    protected $lastErrorMessage = false;

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
     * Check if result has any error
     * @param  array $jsonResult [[Description]]
     * @return boolean  [[Description]]
     */
    private function checkForError($jsonResult)
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
     * Checks and performs a login operation if requried
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
     * [[Description]]
     * @access private
     * @param  [[Type]] $username [[Description]]
     * @param  [[Type]] $accessKey [[Description]]
     * @return array [[Description]]
     */
    private function sendHttpRequest(array $reqdata, $method = 'POST')
    {
        try {
            switch ($method) {
                case 'GET':
                    $response = $this->httpClient->get($this->serviceBaseURL, ['query' => $reqdata]);
                    break;
                case 'POST':
                    $response = $this->httpClient->post($this->serviceBaseURL, ['form_params' => $reqdata]);
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

        return ($this->checkForError($jsonObj))
            ? null
            : $jsonObj['result'];
    }

    /**
     * Performs the challenge
     * @access private
     * @param  string $username [[Description]]
     * @return array [[Description]]
     */
    private function passChallenge($username)
    {
        $getdata = [
            'operation' => 'getchallenge',
            'username'  => $username
        ];
        $result = $this->sendHttpRequest($getdata, 'GET');

        $this->serviceServerTime = $result['serverTime'];
        $this->serviceExpireTime = $result['expireTime'];
        $this->serviceToken = $result['token'];

        return true;
    }

    /**
     * [[Description]]
     * @param  [[Type]] $username [[Description]]
     * @param  [[Type]] $accessKey [[Description]]
     * @return boolean [[Description]]
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
     * [[Description]]
     * @param  [[Type]] $username [[Description]]
     * @param  [[Type]] $password [[Description]]
     * @return boolean  [[Description]]
     */
    public function loginPassword($username, $password, &$accesskey = null)
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

        $accesskey = array_key_exists('accesskey', $result)
            ? $result['accesskey']
            : $result[0];

        return $this->login($username, $accesskey);
    }

    /**
     * Gets last operation error, if any
     * @return WSClientError The error object
     */
    public function getLastError()
    {
        return $this->lastErrorMessage;
    }

    /**
     * Returns the client library version.
     * @return string Client library version
     */
    public function getVersion()
    {
        global $wsclient_version;
        return $wsclient_version;
    }

    /**
     * [[Description]]
     * @return array [[Description]]
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
     * [[Description]]
     * @param  string $moduleName [[Description]]
     * @return array  [[Description]]
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
     * [[Description]]
     * @param  [[Type]]       $moduleName [[Description]]
     * @param  [[Type]]       $entityID     [[Description]]
     * @return boolean|string [[Description]]
     */
    public function getTypedID($moduleName, $entityID)
    {
        if (stripos((string)$entityID, 'x') !== false) {
            return $entityID;
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
     * Invokes custom operation
     * @param  string  $method  Name of the webservice to invoke
     * @param  array   [$params = null] Object $type null or parameter values to method
     * @param  string  [$type   = 'POST'] Request method (POST/GET)
     * @return boolean [[Description]]
     */
    public function invokeOperation($operation, array $params = null, $method = 'POST')
    {
        // Perform re-login if required
        $this->checkLogin();

        $reqdata = [
            'operation' => $operation,
            'sessionName' => $this->sessionName
        ];

        if (!empty($params) && is_array($params)) {
            $reqdata = array_merge($params);
        }

        return $this->sendHttpRequest($reqdata, $method);
    }

    /**
     * [[Description]]
     * @param  [[Type]] $query [[Description]]
     * @return boolean  [[Description]]
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
     * [[Description]]
     * @param  [[Type]] $entityID [[Description]]
     * @return boolean  [[Description]]
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
     * [[Description]]
     * @param  string $moduleName   [[Description]]
     * @param  array $params [[Description]]
     * @return boolean  [[Description]]
     */
    public function entityRetrieveID($moduleName, array $params)
    {
        // Perform re-login if required.
        $this->checkLogin();

        if (empty($params) || !is_array($params)) {
            $errorMessage = "You have to specify at least on parameter (prop => value) in order to retrieve entity ID";
            $this->lastErrorMessage = new WSClientError($errorMessage);
            return false;
        }

        // Build the query
        $criteria = array();
        $query="SELECT id FROM $moduleName WHERE ";
        foreach ($params as $param => $value) {
            $criteria[] = "{$param} LIKE '{$value}'";
        }

        $query.=implode(" AND ", $criteria);
        $query.=" LIMIT 1";

        $records = $this->query($query);
        if (!$records || !is_array($records) || (count($records) !== 1)) {
            return false;
        }

        $entityID = $records[0]['id'];
        $entityIDParts = explode('x', $entityID, 2);
        return (is_array($entityIDParts) && count($entityIDParts) === 2)
            ? $entityIDParts[1]
            : -1;
    }

    /**
     * [[Description]]
     * @param  string $moduleName   [[Description]]
     * @param  array $params [[Description]]
     * @return boolean  [[Description]]
     */
    public function entityCreate($moduleName, array $params)
    {
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
     * [[Description]]
     * @param  string $moduleName   [[Description]]
     * @param  array $params [[Description]]
     * @return boolean  [[Description]]
     */
    public function entityUpdate($moduleName, array $params)
    {
        // Perform re-login if required.
        $this->checkLogin();

        // Assign record to logged in user if not specified
        if (!isset($params['assigned_user_id'])) {
            $params['assigned_user_id'] = $this->userID;
        }

        if (array_key_exists('id', $params)) {
            $data = $this->entityRetrieveByID($moduleName, $params['id']);
            if ($data !== false && is_array($data)) {
                $entityID = $data['id'];
                $params = array_merge(
                    $data,      // needed to provide mandatory field values
                    $params,    // updated data override
                    ['id'=>$entityID] // fixing id, might be useful when non <moduleid>x<id> one was specified
                );
            }
        }

        $postdata = [
                'operation'   => 'update',
                'sessionName' => $this->sessionName,
                'elementType' => $moduleName,
                'element'     => json_encode($params)
        ];

        return $this->sendHttpRequest($postdata);
    }

    /**
     * [[Description]]
     * @param  string $moduleName   [[Description]]
     * @param  string $entityID [[Description]]
     * @return boolean  [[Description]]
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
     * [[Description]]
     * @param  [[Type]] [$modifiedTime = null] [[Description]]
     * @param  [[Type]] [$moduleName = null]   [[Description]]
     * @return boolean  [[Description]]
     */
    public function entitiesSync($modifiedTime = null, $moduleName = null)
    {
        // Perform re-login if required.
        $this->checkLogin();

        $modifiedTime = (empty($modifiedTime))
            ? strtotime('today midnight')
            : intval($modifiedTime);

        $reqdata = [
            'operation' => 'sync',
            'sessionName' => $this->sessionName,
            'modifiedTime' => $modifiedTime
        ];

        if (!empty($moduleName)) {
            $reqdata['elementType'] = $moduleName;
        }

        return $this->sendHttpRequest($reqdata, true);
    }
}

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
* Vtiger Web Services PHP Client Session class
*
* Class Session
* @package Salaros\Vtiger\VTWSCLib
* @internal
*/
class Session
{
    // HTTP Client instance
    protected $httpClient = null;

    // Service URL to which client connects to
    protected $vtigerUrl = null;
    protected $wsBaseURL = null;

    // Vtiger CRM and WebServices API version
    private $vtigerApiVersion = '0.0';
    private $vtigerVersion = '0.0';
    
    // Webservice login validity
    private $serviceExpireTime = null;
    private $serviceToken = null;

    // Webservice user credentials
    private $userName = null;
    private $accessKey = null;

    // Webservice login credentials
    private $userID = null;
    private $sessionName = null;

    /**
     * Class constructor
     * @param string $vtigerUrl  The URL of the remote WebServices server
     * @param string [$wsBaseURL = 'webservice.php']  WebServices base URL appended to vTiger root URL
     */
    public function __construct($vtigerUrl, $wsBaseURL = 'webservice.php')
    {
        $this->vtigerUrl = self::fixVtigerBaseUrl($vtigerUrl);
        $this->serviceBaseURL = $wsBaseURL;

        // Gets target URL for WebServices API requests
        $this->httpClient = new Client([
            'base_uri' => $this->vtigerUrl
        ]);
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
            'accessKey' => md5($this->serviceToken . $accessKey)
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
        $this->vtigerApiVersion = $result['version'];
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

        $this->accessKey = array_key_exists('accesskey', $result)
            ? $result['accesskey']
            : $result[0];

        return $this->login($username, $accessKey);
    }

    /**
     * Gets a challenge token from the server and stores for future requests
     * @access private
     * @param  string $username VTiger user name
     * @return boolean Returns false in case of failure
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

        $this->serviceExpireTime = $result['expireTime'];
        $this->serviceToken = $result['token'];

        return true;
    }

    /**
     * Gets an array containing the basic information about current API user
     * @access public
     * @return array Basic information about current API user
     */
    public function getUserInfo()
    {
        return [
            'id' => $this->userID,
            'userName' => $this->userName,
            'accessKey' => $this->accessKey,
        ];
    }

    /**
     * Gets vTiger version, retrieved on successful login
     * @access public
     * @return string vTiger version, retrieved on successful login
     */
    public function getVtigerVersion()
    {
        return $this->vtigerVersion;
    }

    /**
     * Gets vTiger WebServices API version, retrieved on successful login
     * @access public
     * @return string vTiger WebServices API version, retrieved on successful login
     */
    public function getVtigerApiVersion()
    {
        return $this->vtigerApiVersion;
    }

    /**
     * Sends HTTP request to VTiger web service API endpoint
     * @access private
     * @param  array $requestData HTTP request data
     * @param  string $method HTTP request method (GET, POST etc)
     * @return array Returns request result object (null in case of failure)
     */
    public function sendHttpRequest(array $requestData, $method = 'POST')
    {
        if (!isset($requestData['operation'])) {
            throw new WSException('Request data must contain the name of the operation!');
        }

        $requestData['sessionName'] = $this->sessionName;

        // Perform re-login if required.
        if ('getchallenge' !== $requestData['operation'] && time() > $this->serviceExpireTime) {
            $this->login($this->userName, $this->accessKey);
        }
        
        try {
            switch ($method) {
                case 'GET':
                    $response = $this->httpClient->get($this->serviceBaseURL, ['query' => $requestData]);
                    break;
                case 'POST':
                    $response = $this->httpClient->post($this->serviceBaseURL, ['form_params' => $requestData]);
                    break;
                default:
                    throw new WSException("Unsupported request type {$method}");
            }
        } catch (RequestException $ex) {
            $urlFailed = $this->httpClient->getConfig('base_uri') . $this->serviceBaseURL;
            throw new WSException(
                sprintf('Failed to execute %s call on "%s" URL', $method, $urlFailed),
                'FAILED_SENDING_REQUEST',
                $ex
            );
        }

        $jsonRaw = $response->getBody();
        $jsonObj = json_decode($jsonRaw, true);

        return (!is_array($jsonObj) || self::checkForError($jsonObj))
            ? null
            : $jsonObj['result'];
    }

    /**
     *  Cleans and fixes vTiger URL
     * @access private
     * @static
     * @param  string  Base URL of vTiger CRM
     * @return boolean Returns cleaned and fixed vTiger URL
     */
    private static function fixVtigerBaseUrl($baseUrl)
    {
        if (!preg_match('/^https?:\/\//i', $baseUrl)) {
            $baseUrl = sprintf('http://%s', $baseUrl);
        }
        if (strripos($baseUrl, '/') !== strlen($baseUrl)-1) {
            $baseUrl .= '/';
        }
        return $baseUrl;
    }

    /**
     * Check if server response contains an error, therefore the requested operation has failed
     * @access private
     * @static
     * @param  array $jsonResult Server response object to check for errors
     * @return boolean  True if response object contains an error
     */
    private static function checkForError(array $jsonResult)
    {
        if (isset($jsonResult['success']) && (bool)$jsonResult['success'] === true) {
            return false;
        }

        if (isset($jsonResult['error'])) {
            $error = $jsonResult['error'];
            throw new WSException(
                $error['message'],
                $error['code']
            );
        }

        // This should never happen
        throw new WSException('Unknown error');
    }
}

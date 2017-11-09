----------------------------------
Salaros\\Vtiger\\VTWSCLib\\Session
----------------------------------

.. php:namespace: Salaros\\Vtiger\\VTWSCLib

.. php:class:: Session

    Vtiger Web Services PHP Client Session class

    Class Session

    .. php:attr:: httpClient

        protected

    .. php:attr:: vtigerUrl

        protected

    .. php:attr:: wsBaseURL

        protected

    .. php:method:: __construct($vtigerUrl, $wsBaseURL = 'webservice.php')

        Class constructor

        :type $vtigerUrl: string
        :param $vtigerUrl: The URL of the remote WebServices server
        :param $wsBaseURL:

    .. php:method:: login($username, $accessKey)

        Login to the server using username and VTiger access key token

        :param $username:
        :param $accessKey:
        :returns: boolean Returns true if login operation has been successful

    .. php:method:: loginPassword($username, $password, $accessKey = null)

        Allows you to login using username and password instead of access key
        (works on some VTige forks)

        :param $username:
        :param $password:
        :param $accessKey:
        :returns: boolean  Returns true if login operation has been successful

    .. php:method:: passChallenge($username)

        Gets a challenge token from the server and stores for future requests

        :param $username:
        :returns: boolean Returns false in case of failure

    .. php:method:: getUserInfo()

        Gets an array containing the basic information about current API user

        :returns: array Basic information about current API user

    .. php:method:: getVtigerVersion()

        Gets vTiger version, retrieved on successful login

        :returns: string vTiger version, retrieved on successful login

    .. php:method:: getVtigerApiVersion()

        Gets vTiger WebServices API version, retrieved on successful login

        :returns: string vTiger WebServices API version, retrieved on successful login

    .. php:method:: sendHttpRequest($requestData, $method = 'POST')

        Sends HTTP request to VTiger web service API endpoint

        :param $requestData:
        :param $method:
        :returns: array Returns request result object (null in case of failure)

    .. php:method:: fixVtigerBaseUrl($baseUrl)

        Cleans and fixes vTiger URL

        :type $baseUrl: string
        :param $baseUrl:
        :returns: string Returns cleaned and fixed vTiger URL

    .. php:method:: checkForError($jsonResult)

        Check if server response contains an error, therefore the requested
        operation has failed

        :param $jsonResult:
        :returns: boolean  True if response object contains an error

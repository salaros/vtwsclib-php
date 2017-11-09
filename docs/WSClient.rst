-----------------------------------
Salaros\\Vtiger\\VTWSCLib\\WSClient
-----------------------------------

.. php:namespace: Salaros\\Vtiger\\VTWSCLib

.. php:class:: WSClient

    Vtiger Web Services PHP Client

    Class WSClient

    .. php:attr:: modules

    .. php:attr:: entities

    .. php:method:: __construct($vtigerUrl, $username, $secret, $loginMode = self::USE_ACCESSKEY, $wsBaseURL = 'webservice.php')

        Class constructor

        :type $vtigerUrl: string
        :param $vtigerUrl: The URL of the remote WebServices server
        :param $username:
        :param $secret:
        :param $loginMode:
        :param $wsBaseURL:

    .. php:method:: invokeOperation($operation, $params = null, $method = 'POST')

        Invokes custom operation (defined in vtiger_ws_operation table)

        :param $operation:
        :param $params:
        :param $method:
        :returns: array Result object

    .. php:method:: runQuery($query)

        VTiger provides a simple query language for fetching data.
        This language is quite similar to select queries in SQL.
        There are limitations, the queries work on a single Module,
        embedded queries are not supported, and does not support joins.
        But this is still a powerful way of getting data from Vtiger.
        Query always limits its output to 100 records,
        Client application can use limit operator to get different records.

        :param $query:
        :returns: array  Query results

    .. php:method:: getCurrentUser()

        Gets an array containing the basic information about current API user

        :returns: array Basic information about current API user

    .. php:method:: getVtigerInfo()

        Gets an array containing the basic information about the connected vTiger
        instance

        :returns: array Basic information about the connected vTiger instance

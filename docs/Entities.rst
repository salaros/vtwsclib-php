-----------------------------------
Salaros\\Vtiger\\VTWSCLib\\Entities
-----------------------------------

.. php:namespace: Salaros\\Vtiger\\VTWSCLib

.. php:class:: Entities

    Vtiger Web Services PHP Client Session class

    Class Entities

    .. php:method:: __construct($wsClient)

        Class constructor

        :type $wsClient: object
        :param $wsClient: Parent WSClient instance

    .. php:method:: findOneByID($moduleName, $entityID, $select = [ ])

        Retrieves an entity by ID

        :param $moduleName:
        :param $entityID:
        :param $select:
        :returns: array   $select  The list of fields to select (defaults to SQL-like '*' - all the fields)

    .. php:method:: findOne($moduleName, $params, $select = [ ])

        Retrieve the entity matching a list of constraints

        :param $moduleName:
        :param $params:
        :param $select:
        :returns: array   $select  The list of fields to select (defaults to SQL-like '*' - all the fields)

    .. php:method:: getID($moduleName, $params)

        Retrieves the ID of the entity matching a list of constraints + prepends
        '<module_id>x' string to it

        :param $moduleName:
        :param $params:
        :returns: string  Type ID (a numeric ID + '<module_id>x')

    .. php:method:: getNumericID($moduleName, $params)

        Retrieve a numeric ID of the entity matching a list of constraints

        :param $moduleName:
        :param $params:
        :returns: integer  Numeric ID

    .. php:method:: createOne($moduleName, $params)

        Creates an entity for the giving module

        :param $moduleName:
        :param $params:
        :returns: array  Entity creation results

    .. php:method:: updateOne($moduleName, $entityID, $params)

        Updates an entity

        :param $moduleName:
        :param $entityID:
        :param $params:
        :returns: array  Entity update result

    .. php:method:: deleteOne($moduleName, $entityID)

        Provides entity removal functionality

        :param $moduleName:
        :param $entityID:
        :returns: array  Removal status object

    .. php:method:: findMany($moduleName, $params, $select = [ ], $limit = 0)

        Retrieves multiple records using module name and a set of constraints

        :param $moduleName:
        :param $params:
        :param $select:
        :param $limit:
        :returns: array    $select  The list of fields to select (defaults to SQL-like '*' - all the fields)

    .. php:method:: sync($modifiedTime = null, $moduleName = null)

        Sync will return a sync result object containing details of changes after
        modifiedTime

        :param $modifiedTime:
        :param $moduleName:
        :returns: array  Sync result object

    .. php:method:: getQueryString($moduleName, $params, $select = [ ], $limit = 0)

        Builds the query using the supplied parameters

        :param $moduleName:
        :param $params:
        :param $select:
        :param $limit:
        :returns: string    $select  The list of fields to select (defaults to SQL-like '*' - all the fields)

----------------------------------
Salaros\\Vtiger\\VTWSCLib\\Modules
----------------------------------

.. php:namespace: Salaros\\Vtiger\\VTWSCLib

.. php:class:: Modules

    Vtiger Web Services PHP Client Session class

    Class Modules

    .. php:method:: __construct($wsClient)

        Class constructor

        :type $wsClient: object
        :param $wsClient: Parent WSClient instance

    .. php:method:: getAll()

        Lists all the Vtiger entity types available through the API

        :returns: array List of entity types

    .. php:method:: getOne($moduleName)

        Get the type information about a given VTiger entity type.

        :param $moduleName:
        :returns: array  Result object

    .. php:method:: getTypedID($moduleName, $entityID)

        Gets the entity ID prepended with module / entity type ID

        :param $moduleName:
        :param $entityID:
        :returns: boolean|string Returns false if it is not possible to retrieve module / entity type ID

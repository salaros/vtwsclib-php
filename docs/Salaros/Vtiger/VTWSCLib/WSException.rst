--------------------------------------
Salaros\\Vtiger\\VTWSCLib\\WSException
--------------------------------------

.. php:namespace: Salaros\\Vtiger\\VTWSCLib

.. php:class:: WSException

    Vtiger Web Services PHP Client Exception class

    Class WSException

    .. php:attr:: message

        protected

    .. php:attr:: code

        protected

    .. php:attr:: file

        protected

    .. php:attr:: line

        protected

    .. php:method:: __construct($message, $code = 'UNKNOWN', Exception $previous = null)

        Redefine the exception so message isn't optional

        :param $message:
        :param $code:
        :type $previous: Exception
        :param $previous:

    .. php:method:: __toString()

        Custom string representation of object

        :returns: string A custom string representation of exception

    .. php:method:: getIterator()

        Retrieve an external iterator

        :returns: \Traversable An instance of an object implementing \Traversable

    .. php:method:: getAllProperties()

        Gets all the properties of the object

        :returns: array Array of properties

    .. php:method:: __clone()

    .. php:method:: __wakeup()

    .. php:method:: getMessage()

    .. php:method:: getCode()

    .. php:method:: getFile()

    .. php:method:: getLine()

    .. php:method:: getTrace()

    .. php:method:: getPrevious()

    .. php:method:: getTraceAsString()

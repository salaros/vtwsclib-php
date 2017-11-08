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

use \Exception;
use \IteratorAggregate;

/**
* Vtiger Web Services PHP Client Exception class
*
* Class WSException
* @package Salaros\Vtiger\VTWSCLib
*/
class WSException extends Exception implements IteratorAggregate
{
    protected $message;
    protected $code;
    
    /**
     * Redefine the exception so message isn't optional
     * @access public
     */
    public function __construct($message, $code = 'UNKNOWN', Exception $previous = null)
    {
        $this->message = $message;
        $this->code = $code;
        
        // make sure everything is assigned properly
        parent::__construct($this->message, 0, $previous);
    }
    
    /**
     * Custom string representation of object
     * @access public
     * @return string A custom string representation of exception
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
    
    /**
    * Retrieve an external iterator
    * @access public
    * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
    * @return \Traversable An instance of an object implementing \Traversable
    */
    public function getIterator()
    {
        $properties = $this->getAllProperties();
        $iterator = new \ArrayIterator($properties);
        
        return $iterator;
    }
    
    /**
    * Gets all the properties of the object
    * @access public
    * @return array Array of properties
    */
    private function getAllProperties()
    {
        $allProperties = get_object_vars($this);
        $properties = array();
        foreach ($allProperties as $fullName => $value) {
            $fullNameComponents = explode("\0", $fullName);
            $propertyName = array_pop($fullNameComponents);
            if ($propertyName && isset($value)) {
                $properties[$propertyName] = $value;
            }
        }
        
        return $properties;
    }
}

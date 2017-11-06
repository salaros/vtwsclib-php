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

/**
 * Vtiger Web Services PHP Client error class
 *
 * Class WSClientError
 * @package Salaros\Vtiger\VTWSCLib
 */
class WSClientError
{
    protected $errorCode;
    protected $errorMessage;

    /**
     * WSClientError constructor
     * @param string $errorMessage    The error message
     * @param int [$errorCode = 0]    The error code
     */
    public function __construct($errorMessage, $errorCode = 0)
    {
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }

    /**
     * Allows WSClientError class to override the default behavior
     * when its instances are treated as strings
     * @return string Returns the error message with error code
     */
    public function __toString()
    {
        return sprintf("WSClient Error [ %s ]: %s", $this->errorCode, $this->errorMessage);
    }

    /**
     * Returns JSON representation of WSClientError class instance
     * @return string JSON representation of WSClientError class instance
     */
    public function toJson()
    {
        $error = [
            'code' => $this->errorCode,
            'message' => $this->errorMessage,
            'error' => true
        ];

        return json_encode($error, true);
    }
}

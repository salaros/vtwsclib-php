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
 * @copyright 2015 Zhmayev Yaroslav
 * @license   The MIT License (MIT)
 */

namespace Salaros\Vtiger\VTWSCLib;

/**
 * Vtiger Web Services PHP Client error class
 *
 * Class WSClient_Error
 * @package Salaros\Vtiger\VTWSCLib
 */
class WSClient_Error {

    protected $_code,
              $_message;

    /**
     * [[Description]]
     * @param [[Type]] $_message    [[Description]]
     * @param [[Type]] [$_code = 0] [[Description]]
     */
    public function __construct($_message, $_code = 0) {
        $this->_code = $_code;
        $this->_message = $_message;
    }

    /**
     * [[Description]]
     * @return string [[Description]]
     */
    public function __toString()
    {
        return "WSClient Error [{$this->_code}]: {$this->_message}";
    }

    /**
     * [[Description]]
     * @param  [[Type]] [$addErrorProp = true] [[Description]]
     * @return [[Type]] [[Description]]
     */
    public function toJson($addErrorProp = true) {
        $error = [
            'code' => $_code,
            'message' => $_message
        ];

        if($addErrorProp)
            $error = array_merge(['error' => true], $error);

        return json_encode($error);
    }

}

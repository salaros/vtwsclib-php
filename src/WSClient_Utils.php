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
 * Vtiger Web Services PHP Client Utils class
 *
 * Class WSClient_Utils
 * @package Salaros\Vtiger\VTWSCLib
 */
class WSClient_Utils {
    /**
     * Gets actual record ID from the response ID
     * @param  [[Type]] $id [[Description]]
     * @return [[Type]] [[Description]]
     */
    public static function getRecordID($id) {
        $ex = explode('x', $id, 2);
        return (count($ex) !== 2)
            ? -1
            : $ex[1];
    }

    /**
     * Gets target URL for WebServices API requests
     * @param  string $url [[Description]]
     * @return string The complete URL of the service
     */
    public static function getServiceURL($url) {
        if(strripos($url, 'http://', -strlen($url)) === FALSE) {
            $url = 'http://'.$url;
        }

        if(strripos($url, '/') != (strlen($url)-1)) {
            $url .= '/';
        }

        return $url;
    }
}

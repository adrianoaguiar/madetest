<?php
/**
 * Made.com
 *
 * PHP Version 5
 *
 * @category  Made
 * @package   Made_Test
 * @author    Mike Whitby <michael.whitby@made.com>
 * @copyright Copyright (c) 2011 Ibuildings. (http://www.ibuildings.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      N/A
 */

/**
 * Magento Controller Response HttpTestCase
 *
 * @category Made
 * @package  Made_Test
 * @author   Alistair Stead <alistair@ibuildings.com>
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link     N/A
 */
class Made_Test_Controller_Response_HttpTestCase
    extends Zend_Controller_Response_HttpTestCase
{
    /**
     * Send response
     *
     * @return string The response
     */
    public function sendResponse()
    {
        Mage::dispatchEvent('http_response_send_before', array('response'=>$this));
        return parent::sendResponse();
    }

    /**
     * Convenience method for getting the response body
     *
     * @return string The response
     */
    public function __toString()
    {
        return $this->sendResponse();
    }

    /**
     * "send" headers by returning array of all headers that would be sent
     *
     * @return array
     */
    public function sendHeaders()
    {
        $headers = array();
        foreach ($this->_headersRaw as $header) {
            $headers[] = $header;
        }
        foreach ($this->_headers as $header) {
            $name = $header['name'];
            $key  = strtolower($name);
            if (array_key_exists($name, $headers)) {
                if ($header['replace']) {
                    $headers[$key] = $header['name'] . ': ' . $header['value'];
                }
            } else {
                $headers[$key] = $header['name'] . ': ' . $header['value'];
            }
        }
        return $headers;
    }
}

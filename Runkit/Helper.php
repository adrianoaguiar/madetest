<?php
/**
 * Made.com
 *
 * PHP Version 5
 *
 * @category  Made
 * @package   Made_Test
 * @author    Mike Whitby <michael.whitby@made.com>
 * @copyright 2012 made.com
 * @license   http://www.made.com/license.txt Commercial license
 * @link      N/A
 */

/**
 * Provides alterations to Magento core classes at runtime via runkit
 *
 * These alterations are only supported via Padriac's version of runkit which is
 * at {@link https://github.com/padraic/runkit} - the PEAR version of runkit is
 * known to not work due to method adding issues
 *
 * @category Made
 * @package  Made_Test
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://www.made.com/license.txt Commercial license
 * @link     N/A
 */
class Made_Test_Runkit_Helper
{
    /**
     * Check for runkit and magento existence
     * 
     * @static
     *
     * @throws Exception
     *
     * @return void
     */
    protected static function _prerequisiteChecks()
    {
        if (!extension_loaded('runkit')) {
            throw new Exception('Runkit extension not loaded');
        }
        if (!class_exists('Mage')) {
            throw new Exception('Mage class not found');
        }
    }

    /**
     * Rewrites {@see Mage::getModel()} to use a callback
     *
     * @static
     *
     * @throws Exception
     *
     * @param $callback A callback as accepted by call_user_func()
     *
     * @return void
     */
    public static function hookGetModel($callback)
    {
        self::_prerequisiteChecks();

        if (defined('Mage::MADE_TEST_GETMODEL_TAINTED')) {
            throw new Exception('getModel() is already hooked');
        }

        if (!is_callable($callback)) {
            throw new Exception('Invalid callback supplied');
        }

        runkit_method_copy('Mage', 'origGetModel', 'Mage', 'getModel');

        // we need to store the callback so getModel() can access it
        Mage::register('made_test_getmodel_callback', $callback);

        runkit_method_redefine(
            'Mage',
            'getModel',
            '$modelClass = \'\', $arguments = array()',
            '$callback = self::registry(\'made_test_getmodel_callback\');
             return call_user_func($callback, $modelClass, $arguments);',
            RUNKIT_ACC_PUBLIC
        );

        // sorry for constant usage here - runkit can't add static properties
        runkit_constant_add('Mage::MADE_TEST_GETMODEL_TAINTED', true);
    }

    /**
     * Restore {@see Mage::getModel()} to its original state
     *
     * @static
     *
     * @throws Exception
     *
     * @return void
     */
    public static function unhookGetModel()
    {
        self::_prerequisiteChecks();

        if (!defined('Mage::MADE_TEST_GETMODEL_TAINTED')) {
            throw new Exception('getModel() has not been hooked');
        }

        if (!method_exists('Mage', 'origGetModel')) {
            throw new Exception('origGetModel() does not exist');
        }

        runkit_method_remove('Mage', 'getModel');
        runkit_method_rename('Mage', 'origGetModel', 'getModel');

        Mage::unregister('made_test_getmodel_callback');

        runkit_constant_remove('Mage::MADE_TEST_GETMODEL_TAINTED');
    }

    /**
     * Rewrites {@see Mage::getResourceModel()} to use a callback
     *
     * @static
     *
     * @throws Exception
     *
     * @param $callback A callback as accepted by call_user_func()
     *
     * @return void
     */
    public static function hookGetResourceModel($callback)
    {
        self::_prerequisiteChecks();

        if (defined('Mage::MADE_TEST_GETRESOURCEMODEL_TAINTED')) {
            throw new Exception('getResourceModel() is already hooked');
        }

        if (!is_callable($callback)) {
            throw new Exception('Invalid callback supplied');
        }

        runkit_method_copy('Mage', 'origGetResourceModel', 'Mage', 'getResourceModel');

        // we need to store the callback so getModel() can access it
        Mage::register('made_test_getresourcemodel_callback', $callback);

        runkit_method_redefine(
            'Mage',
            'getResourceModel',
            '$modelClass = \'\', $arguments = array()',
            '$callback = self::registry(\'made_test_getresourcemodel_callback\');
             return call_user_func($callback, $modelClass, $arguments);',
            RUNKIT_ACC_PUBLIC
        );

        // sorry for constant usage here - runkit can't add static properties
        runkit_constant_add('Mage::MADE_TEST_GETRESOURCEMODEL_TAINTED', true);
    }

    /**
     * Restore {@see Mage::getResourceModel()} to its original state
     *
     * @static
     *
     * @throws Exception
     *
     * @return void
     */
    public static function unhookGetResourceModel()
    {
        self::_prerequisiteChecks();

        if (!defined('Mage::MADE_TEST_GETRESOURCEMODEL_TAINTED')) {
            throw new Exception('getResourceModel() has not been hooked');
        }

        if (!method_exists('Mage', 'origGetResourceModel')) {
            throw new Exception('origGetResourceModel() does not exist');
        }

        runkit_method_remove('Mage', 'getResourceModel');
        runkit_method_rename('Mage', 'origGetResourceModel', 'getResourceModel');

        Mage::unregister('made_test_getresourcemodel_callback');

        runkit_constant_remove('Mage::MADE_TEST_GETRESOURCEMODEL_TAINTED');
    }

    /**
     * Rewrites {@see Mage::helper()} to use a callback
     *
     * @static
     *
     * @throws Exception
     *
     * @param $callback A callback as accepted by call_user_func()
     *
     * @return void
     */
    public static function hookHelper($callback)
    {
        self::_prerequisiteChecks();

        if (defined('Mage::MADE_TEST_HELPER_TAINTED')) {
            throw new Exception('helper() is already hooked');
        }

        if (!is_callable($callback)) {
            throw new Exception('Invalid callback supplied');
        }

        runkit_method_copy('Mage', 'origHelper', 'Mage', 'helper');

        // we need to store the callback so getModel() can access it
        Mage::register('made_test_helper_callback', $callback);

        runkit_method_redefine(
            'Mage',
            'helper',
            '$name',
            '$callback = self::registry(\'made_test_helper_callback\');
             return call_user_func($callback, $name);',
            RUNKIT_ACC_PUBLIC
        );

        // sorry for constant usage here - runkit can't add static properties
        runkit_constant_add('Mage::MADE_TEST_HELPER_TAINTED', true);
    }

    /**
     * Restore {@see Mage::helper()} to its original state
     *
     * @static
     *
     * @throws Exception
     *
     * @return void
     */
    public static function unhookHelper()
    {
        self::_prerequisiteChecks();

        if (!defined('Mage::MADE_TEST_HELPER_TAINTED')) {
            throw new Exception('helper() has not been hooked');
        }

        if (!method_exists('Mage', 'origHelper')) {
            throw new Exception('origHelper() does not exist');
        }

        runkit_method_remove('Mage', 'helper');
        runkit_method_rename('Mage', 'origHelper', 'helper');

        Mage::unregister('made_test_helper_callback');

        runkit_constant_remove('Mage::MADE_TEST_HELPER_TAINTED');
    }

    /**
     * Creates Mage_Core_Model_App::setRequest() to allow request injection in
     * order to allow controller testing
     *
     * @static
     *
     * @throws Exception
     *
     * @return void
     */
    public static function addSetRequest()
    {
        if (method_exists('Mage_Core_Model_App', 'setRequest')) {
            throw new Exception('Mage_Core_Model_App::setRequest() already exists');
        }

        runkit_method_add(
            'Mage_Core_Model_App',
            'setRequest',
            'Zend_Controller_Request_Abstract $request',
            '$this->_request = $request;',
            RUNKIT_ACC_PUBLIC
        );
    }

    /**
     * Removes the Mage_Core_Model_App::setRequest() method as added by
     * {@see addSetRequest()}
     *
     * @static
     *
     * @throws Exception
     *
     * @return void
     */
    public static function removeSetRequest()
    {
        if (!method_exists('Mage_Core_Model_App', 'setRequest')) {
            throw new Exception('Mage_Core_Model_App::setRequest() doesn\'t exist');
        }

        runkit_method_remove('Mage_Core_Model_App', 'setRequest');
    }

    /**
     * Creates Mage_Core_Model_App::setResponse() to allow response injection in
     * order to allow controller testing
     *
     * @static
     *
     * @throws Exception
     *
     * @return void
     */
    public static function addSetResponse()
    {
        if (method_exists('Mage_Core_Model_App', 'setResponse')) {
            throw new Exception('Mage_Core_Model_App::setResponse() already exists');
        }

        runkit_method_add(
            'Mage_Core_Model_App',
            'setResponse',
            'Zend_Controller_Response_Abstract $response',
            '$this->_response = $response;',
            RUNKIT_ACC_PUBLIC
        );
    }

    /**
     * Removes the Mage_Core_Model_App::setResponse() method as added by
     * {@see addSetResponse()}
     *
     * @static
     *
     * @throws Exception
     *
     * @return void
     */
    public static function removeSetResponse()
    {
        if (!method_exists('Mage_Core_Model_App', 'setResponse')) {
            throw new Exception('Mage_Core_Model_App::setResponse() doesn\'t exist');
        }

        runkit_method_remove('Mage_Core_Model_App', 'setResponse');
    }

    /**
     * Creates Mage::setApp()
     *
     * @static
     *
     * @throws Exception
     *
     * @return void
     */
    public static function addSetApp()
    {
        if (method_exists('Mage', 'setApp')) {
            throw new Exception('Mage::setApp() already exists');
        }

        runkit_method_add(
            'Mage',
            'setApp',
            '$app',
            'self::$_app = $app;',
            RUNKIT_ACC_PUBLIC
        );
    }

    /**
     * Removes Mage::setApp()
     *
     * @static
     *
     * @throws Exception
     *
     * @return void
     */
    public static function removeSetApp()
    {
        if (!method_exists('Mage', 'setApp')) {
            throw new Exception('Mage::setApp() doesn\'t exist');
        }

        runkit_method_remove('Mage', 'setApp');
    }
}

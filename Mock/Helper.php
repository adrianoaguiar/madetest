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
 * Mock helper
 *
 * An easy way to set up the Magento mocking environment
 *
 * @category Made
 * @package  Made_Test
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://www.made.com/license.txt Commercial license
 * @link     N/A
 */
class Made_Test_Mock_Helper
{
    /**
     * Set a mock app into Mage. This requires Mage::setApp() to be present
     *
     * @return Mage_Core_Model_App
     */
    static public function mockApp()
    {
        // @todo throw when Mage::setApp() is not present

        $app = PHPUnit_Framework_MockObject_Generator::getMock(
            'Mage_Core_Model_App',
            array(),
            array(),
            '',
            false,
            true,
            true
        );

        Mage::setApp($app);

        return $app;
    }

    /**
     * Set a mock store into Mage::app(), requires {@link mockApp()} to have
     * been called prior
     *
     * @return Mage_Core_Model_Store
     */
    static public function mockStore()
    {
        // @todo throw when Mage::app() is not a mock
        
        $store = PHPUnit_Framework_MockObject_Generator::getMock(
            'Mage_Core_Model_Store',
            array(),
            array(),
            '',
            false,
            true,
            true
        );

        // set the store into Mage_Core_Model_App
        Mage::app()->expects(new PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount())
                   ->method('getStore')
                   ->will(new PHPUnit_Framework_MockObject_Stub_Return($store));
        
        return $store;
    }

    /**
     * Set a mock layout into Mage::app(), requires {@link mockApp()} to have
     * been called prior
     *
     * @param Made_Test_Mock_Factory $blockFactory The block factory
     *
     * @return Mage_Core_Model_Layout
     */
    static public function mockLayout(Made_Test_Mock_Factory $blockFactory)
    {
        // @todo throw when Mage::app() is not a mock
        
        $layout = PHPUnit_Framework_MockObject_Generator::getMock(
            'Mage_Core_Model_Layout',
            array(),
            array(),
            '',
            false,
            true,
            true
        );

        // create a callback for createBlock()
        $cb = function($type, $name = '', array $attribs = array()) use ($blockFactory) {
            return $blockFactory->get($type);
        };
        $layout->expects(new PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount())
            ->method('createBlock')
            ->will(
                new PHPUnit_Framework_MockObject_Stub_ReturnCallback($cb)
            );

        // set the layout into Mage_Core_Model_App
        Mage::app()->expects(new PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount())
            ->method('getLayout')
            ->will(new PHPUnit_Framework_MockObject_Stub_Return($layout));
        
        return $layout;
    }

    /**
     * Set a mock front controller into Mage::app(), requires {@link mockApp()}
     * to have been called prior
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    static public function mockFrontController()
    {
        // @todo throw when Mage::app() is not a mock
        
        $frontController = PHPUnit_Framework_MockObject_Generator::getMock(
            'Mage_Core_Controller_Varien_Front',
            array(),
            array(),
            '',
            false,
            true,
            true
        );

        // set the front controller into Mage_Core_Model_App
        Mage::app()->expects(new PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount())
                   ->method('getFrontController')
                   ->will(new PHPUnit_Framework_MockObject_Stub_Return($frontController));
        
        return $frontController;
    }
}

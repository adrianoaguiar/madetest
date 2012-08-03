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
 * Controller testcase
 *
 * @category Made
 * @package  Made_Test
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://www.made.com/license.txt Commercial license
 * @link     N/A
 */
abstract class Made_Test_PHPUnit_Controller_TestCase
    extends Made_Test_PHPUnit_Abstract_TestCase
{
    /**
     * Setup
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        Made_Test_Runkit_Helper::addSetRequest();
        Made_Test_Runkit_Helper::addSetResponse();

        Mage::app()->setRequest(new Made_Test_Controller_Request_HttpTestCase());
        Mage::app()->setResponse(new Made_Test_Controller_Response_HttpTestCase());
    }

    /**
     * Tear down
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->resetRequest();
        $this->resetResponse();

        Made_Test_Runkit_Helper::removeSetRequest();
        Made_Test_Runkit_Helper::removeSetResponse();

        parent::tearDown();
    }

    /**
     * Dispatch the Mage request
     *
     * If a URL is provided, sets it as the request URI in the request object.
     * Dispatches the application request.
     *
     * @param string|null $url
     *
     * @return void
     *
     * @author Alistair Stead <alistair@ibuildings.com>
     */
    public function dispatch($url = null)
    {
        // we shouldn't need to set this, but some legacy code may exist
        $prependSlash = (substr($url, 0, 1) == '/') ? '' : '/';
        $_SERVER['REQUEST_URI'] = $prependSlash . $url;

        $request = $this->getRequest();
        if (null !== $url) {
            $request->setRequestUri($url);
        }
        $request->setPathInfo(null);
        
        Mage::app()->run(array(
            'scope_code' => '',
            'scope_type' => '',
        ));
    }

    /**
     * Get the request object
     *
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Get the response object
     *
     * @return Zend_Controller_Response_Abstract
     */
    public function getResponse()
    {
        return Mage::app()->getResponse();
    }

    /**
     * Reset the request object
     *
     * @return void
     */
    public function resetRequest()
    {
        $request = $this->getRequest();

        if (!(method_exists($request, 'clearQuery')
            && method_exists($request, 'clearQuery'))
        ) {
            return;
        }

        $request->clearQuery();
        $request->clearPost();
    }

    /**
     * Reset the response object
     *
     * @return void
     */
    public function resetResponse()
    {
        $response = $this->getResponse();

        if (!(method_exists($response, 'clearAllHeaders')
            && method_exists($response, 'clearBody'))
        ) {
            return;
        }

        $response->clearAllHeaders();
        $response->clearBody();
    }

    /**
     * Reset Application state
     *
     * @todo this does not work, keeps coming up with a CMS route
     * 
     * @return void
     */
    public function reset()
    {
        $_GET = array();
        $_POST = array();
        $this->tearDown();
        $this->setUp();
    }

    public function assertFullRoute($fullRoute, $message = '')
    {
        $this->addToAssertionCount(1);
        $actualFullRoute = sprintf(
            '%s_%s_%s',
            $this->getRequest()->getRequestedRouteName(),
            $this->getRequest()->getControllerName(),
            $this->getRequest()->getActionName()
        );
        if ($fullRoute != $actualFullRoute) {
            $msg = sprintf('Failed asserting matched full route was "%s", actual route is %s',
                $fullRoute,
                $actualFullRoute
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertNotFullRoute($fullRoute, $message = '')
    {
        $this->addToAssertionCount(1);
        $actualFullRoute = sprintf(
            '%s_%s_%s',
            $this->getRequest()->getRequestedRouteName(),
            $this->getRequest()->getControllerName(),
            $this->getRequest()->getActionName()
        );
        if ($fullRoute == $actualFullRoute) {
            $msg = sprintf('Failed asserting matched full route was NOT "%s", actual route is %s',
                $fullRoute,
                $actualFullRoute
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertRoute($route, $message = '')
    {
        $this->addToAssertionCount(1);
        if ($route != $this->getRequest()->getRequestedRouteName()) {
            $msg = sprintf('Failed asserting matched route was "%s", actual route is %s',
                $route,
                $this->getRequest()->getRequestedRouteName()
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertNotRoute($route, $message = '')
    {
        $this->addToAssertionCount(1);
        if ($route == $this->getRequest()->getRequestedRouteName()) {
            $msg = sprintf('Failed asserting route matched was NOT "%s"', $route);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }
    
    public function assertControllerModule($module, $message = '')
    {
        $this->addToAssertionCount(1);
        if ($module != $this->getRequest()->getControllerModule()) {
            $msg = sprintf('Failed asserting last controller module used "%s" was "%s"',
                $this->getRequest()->getControllerModule(),
                $module
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }
    
    public function assertNotControllerModule($module, $message = '')
    {
        $this->addToAssertionCount(1);
        if ($module == $this->getRequest()->getControllerModule()) {
            $msg = sprintf('Failed asserting last controller module used "%s" was NOT "%s"',
                $this->getRequest()->getControllerModule(),
                $module
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertController($controller, $message = '')
    {
        $this->addToAssertionCount(1);
        if ($controller != $this->getRequest()->getControllerName()) {
            $msg = sprintf('Failed asserting last controller used "%s" was "%s"',
                $this->getRequest()->getControllerName(),
                $controller
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertNotController($controller, $message = '')
    {
        $this->addToAssertionCount(1);
        if ($controller == $this->getRequest()->getControllerName()) {
            $msg = sprintf('Failed asserting last controller used "%s" was NOT "%s"',
                $this->getRequest()->getControllerName(),
                $controller
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertAction($action, $message = '')
    {
        $this->addToAssertionCount(1);
        if ($action != $this->getRequest()->getActionName()) {
            $msg = sprintf('Failed asserting last action "%s" was "%s"',
                $this->getRequest()->getActionName(),
                $action
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertNotAction($action, $message = '')
    {
        $this->addToAssertionCount(1);
        if ($action == $this->getRequest()->getActionName()) {
            $msg = sprintf('Failed asserting last action "%s" was NOT "%s"',
                $this->getRequest()->getActionName(),
                $action
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertResponseCode($code, $message = '')
    {
        $this->addToAssertionCount(1);
        if ($code != $this->getResponse()->getHttpResponseCode()) {
            $msg = sprintf('Failed asserting response code "%s" was "%s"',
                $this->getResponse()->getHttpResponseCode(),
                $code
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertNotResponseCode($code, $message = '')
    {
        $this->addToAssertionCount(1);
        if ($code == $this->getResponse()->getHttpResponseCode()) {
            $msg = sprintf('Failed asserting response code "%s" was NOT "%s"',
                $this->getResponse()->getHttpResponseCode(),
                $code
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertRedirect($message = '')
    {
        $this->addToAssertionCount(1);
        if (!$this->getResponse()->isRedirect()) {
            $msg = 'Failed asserting that redirection occured';
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertNotRedirect($message = '')
    {
        $this->addToAssertionCount(1);
        if ($this->getResponse()->isRedirect()) {
            $msg = 'Failed asserting that no redirection occured';
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertRedirectTo($url, $message = '')
    {
        $this->addToAssertionCount(1);
        if (substr($url, 0, 10) != 'Location: ') {
            $url = 'Location: ' . $url;
        }
        $headers = $this->getResponse()->sendHeaders();
        $redirectTo = (isset($headers['location'])) ? $headers['location'] : null;
        if ($url != $redirectTo) {
            $msg = sprintf('Failed asserting redirection occured to "%s", actually "%s"',
                substr($url, 10),
                substr($redirectTo, 10)
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    public function assertNotRedirectTo($url, $message = '')
    {
        $this->addToAssertionCount(1);
        if (substr($url, 0, 10) != 'Location: ') {
            $url = 'Location: ' . $url;
        }
        $headers = $this->getResponse()->getHeaders();
        $redirectTo = (isset($headers['location'])) ? $headers['location'] : null;
        if ($url == $redirectTo) {
            $msg = sprintf('Failed asserting redirection did NOT occur to "%s", actually "%s"',
                substr($url, 10),
                substr($redirectTo, 10)
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }
}

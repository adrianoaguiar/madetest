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
 * Abstract testcase
 *
 * @category Made
 * @package  Made_Test
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://www.made.com/license.txt Commercial license
 * @link     N/A
 */
abstract class Made_Test_PHPUnit_Abstract_TestCase
    extends PHPUnit_Framework_TestCase
{
    /**
     * If switching stores or faking the admin area, this value holds the old
     * store id. Only used during integration and functional tests
     *
     * @var int
     */
    protected $_oldStoreId;

    /**
     * The various cache keys, used with {@see _setCache()}
     */
    const CACHE_KEY_BLOCK           = 'block_html';
    const CACHE_KEY_COLLECTIONS     = 'collections';
    const CACHE_KEY_CONFIG          = 'config';
    const CACHE_KEY_WEBSERVICES     = 'config_api';
    const CACHE_KEY_EAV             = 'eav';
    const CACHE_KEY_FPC             = 'full_page';
    const CACHE_KEY_LAYOUT          = 'layout';
    const CACHE_KEY_TRANSLATE       = 'translate';

    /**
     * @var string The filename currently being executed
     */
    protected $_filename = null;

    /**
     * @var array A named array of factories
     */
    protected $_factories = array();

    /**
     * Set the filename of the currently executing test class
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        $class = new ReflectionClass(get_called_class());
        $this->_filename = $class->getFileName();

        return parent::__construct($name, $data, $dataName);
    }

    /**
     * Setup
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        if ($this->isInGroup('unit')) {
            $this->setUpUnit();
        } elseif ($this->isInGroup('integration')) {
            $this->setUpIntegration();
        } elseif ($this->isInGroup('functional')) {
            $this->setUpFunctional();
        }
    }

    /**
     * Tear down
     *
     * @return void
     */
    protected function tearDown()
    {
        if ($this->isInGroup('unit')) {
            $this->tearDownUnit();
        } elseif ($this->isInGroup('integration')) {
            $this->tearDownIntegration();
        } elseif ($this->isInGroup('functional')) {
            $this->tearDownFunctional();
        }

        parent::tearDown();
    }

    /**
     * Setup for unit tests
     *
     * @return void
     */
    protected function setUpUnit()
    {
        // stop sessions being started
        $_SESSION = array();
        
        // use runkit to add set app
        Made_Test_Runkit_Helper::addSetApp();

        // create and inject factories
        $modelFactory         = new Made_Test_Mock_Factory();
        $resourceModelFactory = new Made_Test_Mock_Factory();
        $blockFactory         = new Made_Test_Mock_Factory();
        $helperFactory        = new Made_Test_Mock_Factory();
        $this->setFactory('model', $modelFactory);
        $this->setFactory('resourceModel', $resourceModelFactory);
        $this->setFactory('block', $blockFactory);
        $this->setFactory('helper', $helperFactory);

        // inject mock app, store and layout
        Made_Test_Mock_Helper::mockApp();
        Made_Test_Mock_Helper::mockStore();
        Made_Test_Mock_Helper::mockLayout($blockFactory);

        // use runkit to hook things
        Made_Test_Runkit_Helper::hookGetModel(array($modelFactory, 'get'));
        Made_Test_Runkit_Helper::hookGetResourceModel(array($resourceModelFactory, 'get'));
        Made_Test_Runkit_Helper::hookHelper(array($helperFactory, 'get'));
    }

    /**
     * Teardown for unit tests
     *
     * @return void
     */
    protected function tearDownUnit()
    {
        // unhook runkit
        Made_Test_Runkit_Helper::unHookGetModel();
        Made_Test_Runkit_Helper::unHookGetResourceModel();
        Made_Test_Runkit_Helper::unHookHelper();

        // remove factories
        $this->_factories = array();

        // remove set app
        Made_Test_Runkit_Helper::removeSetApp();

        // reset magento
        Mage::reset();
    }

    /**
     * Setup for integration tests
     *
     * @return void
     */
    protected function setUpIntegration()
    {
        /**
         * @todo we can't mock blocks with integration testing yet
         */
        
        // stop sessions being started
        $_SESSION = array();

        // start magento
        Mage::app();
        Mage::setIsDeveloperMode(true);

        // create and inject factories
        $modelFactory         = new Made_Test_Mock_Factory();
        $resourceModelFactory = new Made_Test_Mock_Factory();
        $blockFactory         = new Made_Test_Mock_Factory();
        $helperFactory        = new Made_Test_Mock_Factory();
        $this->setFactory('model', $modelFactory);
        $this->setFactory('resourceModel', $resourceModelFactory);
        $this->setFactory('block', $blockFactory);
        $this->setFactory('helper', $helperFactory);

        // use runkit to hook things
        Made_Test_Runkit_Helper::hookGetModel(array($modelFactory, 'get'));
        Made_Test_Runkit_Helper::hookGetResourceModel(array($resourceModelFactory, 'get'));
        Made_Test_Runkit_Helper::hookHelper(array($helperFactory, 'get'));

        // set final callbacks on factories
        $modelFactory->setFinalCallback(function($objectClass = '', $arguments = array()) {
            return Mage::origGetModel($objectClass, $arguments);
        });
        $resourceModelFactory->setFinalCallback(function($objectClass = '', $arguments = array()) {
            return Mage::origGetResourceModel($objectClass, $arguments);
        });
        $helperFactory->setFinalCallback(function($name) {
            return Mage::origHelper($name);
        });
    }

    /**
     * Teardown for integration tests
     *
     * @return void
     */
    protected function tearDownIntegration()
    {
        // unhook runkit
        Made_Test_Runkit_Helper::unHookGetModel();
        Made_Test_Runkit_Helper::unHookGetResourceModel();
        Made_Test_Runkit_Helper::unHookHelper();

        // remove factories
        $this->_factories = array();

        // reset magento
        Mage::reset();
    }

    /**
     * Setup for functional tests
     *
     * @return void
     */
    protected function setUpFunctional()
    {
        // stop sessions being started
        $_SESSION = array();

        // start magento
        Mage::app();
        Mage::setIsDeveloperMode(true);
    }

    /**
     * Teardown for functional tests
     *
     * @return void
     */
    protected function tearDownFunctional()
    {
        // reset magento
        Mage::reset();
    }

    /**
     * Retreive the file path for a fixture file from either the test-specific
     * fixture dir, or the global fixture dir
     *
     * @param string $file            The file name
     * @param bool   $testClassSubdir True to look in the test class fixture subdir
     * @param cool   $contents        True to return contents rather than path
     *
     * @return string The file path
     */
    public function getTestFile($file, $testClassSubdir = false, $contents = false)
    {
        $fixtureDir = $GLOBALS['bootstrapdir'] . DS . $GLOBALS['fixturedir'];
        if (DS == substr($fixtureDir, -1)) {
            $fixtureDir = substr($fixtureDir, 0, -1);
        }
        
        /**
          * The testClassSubdir works like this; lets say are in this class:
          *
          *     /srv/made/tests/tests/app/code/local/Made/Shippingoption/Model/Carrier/ShippingOptionTest.php
          *
          * And you call this
          *
          *     $this->getFixtureFile('fixture.xml', true);
          *
          * Then you get this back:
          *
          *     /srv/made/tests/fixtures/app/code/local/Made/Shippingoption/Model/Carrier/ShippingOptionTest/fixture.xml
          *
          * Otherwise you would get this back:
          *
          *     /srv/made/tests/fixtures/fixture.xml
          */
        $prefixDir = '';

        if ($testClassSubdir) {
            $testDirs = array_reverse(explode(DS, dirname($this->_filename)));
            $fixtureDirs = array();
            foreach ($testDirs as $dir) {
                if ($dir == 'tests') break;
                $fixtureDirs[] = $dir;
            }
            $prefixDir = implode(DS, array_reverse($fixtureDirs)) . DS;
            $prefixDir .= strstr(basename($this->_filename), '.', true);
        }

        $return = $fixtureDir . DS . $prefixDir . DS . $file;

        if ($contents) {
            $return = file_get_contents($return);
        }

        return $return;
    }

    /**
     * Shortcut to getting a mock with a disabled constructor, and with certain
     * methods
     *
     * @param string       $class   The class name to get a mock for
     * @param string|array $methods Optional, the methods to mock, CSV string or array
     *
     * @return stdObject The mocked object
     */
    public function mock($class, $methods = null)
    {
        $mockBuilder = $this->getMockBuilder($class);
        $mockBuilder->disableOriginalConstructor();
        if ($methods) {
            if (is_string($methods)) {
                $methods = explode(',', $methods);
            }
            $mockBuilder->setMethods($methods);
        }
        return $mockBuilder->getMock();
    }

    /**
     * Shortcut to setting a return value from a mock method
     *
     * @param stdObject $mockObject The mock object
     * @param string    $method     The method
     * @param mixed     $return     The return value
     *
     * @return stdObject The mock object
     */
    public function mockReturn($mockObject, $method, $return)
    {
        $mockObject->expects($this->any())
            ->method($method)
            ->will($this->returnValue($return));
    }

    /**
     * Set a config value on-the-fly for the current store
     *
     * Should not be used during unit tests
     *
     * @param string $path  The path of the config value
     * @param mixed  $value The config value
     *
     * @return void
     */
    public function setConfigValue($path, $value)
    {
        if ($this->isInGroup('unit')) {
            throw new RuntimeException(__METHOD__ . ' should not be used '
                . 'during unit tests');
        }

        Mage::app()->getStore()->setConfig($path, $value);
    }

    /**
     * Set multiple config values
     *
     * Should not be used during unit tests
     *
     * @param array $values Keys are keys, values are values
     *
     * @return void
     */
    public function setConfigValues(Array $values)
    {
        if ($this->isInGroup('unit')) {
            throw new RuntimeException(__METHOD__ . ' should not be used '
                . 'during unit tests');
        }

        foreach ($values as $key => $val) {
            $this->setConfigValue($key, $val);
        }
    }

    /**
     * Set a named factory
     *
     * @param string                 $name    The factories name
     * @param Made_Test_Mock_Factory $factory The factory
     *
     * @return void
     */
    public function setFactory($name, Made_Test_Mock_Factory $factory)
    {
        $this->_factories[$name] = $factory;
    }

    /**
     * Return a named factory
     *
     * @param string $name The factories name
     *
     * @return Made_Test_Mock_Factory
     */
    public function getFactory($name)
    {
        return $this->_factories[$name];
    }

    /**
     * Change caching for a certain key. To see what keys are availabe, see the
     * class constants starging with CACHE_KEY_
     *
     * Should not be used during unit tests
     *
     * @param string|array $key   the cache key(s) to change
     * @param bool         $setTo whether to enabled or disable the cache
     *
     * @return void
     */
    protected function setCache($keys, $setTo)
    {
        if ($this->isInGroup('unit')) {
            throw new RuntimeException(__METHOD__ . ' should not be used '
                . 'during unit tests');
        }

        if (!is_array($keys)) {
            $keys = array($keys);
        }
        $types = Mage::app()->useCache();
        $changed = false;
        foreach ($keys as $key) {
            if ($types[$key] != $setTo) {
                $changed = true;
                $types[$key] = $setTo;
            }
        }
        if ($changed) {
            Mage::app()->saveUseCache($types);
        }
    }

    /**
     * Get the groups the current test is in
     *
     * @return array
     */
    protected function getGroups()
    {
        $annotations = $this->getAnnotations();
        $methodAnnotations = $annotations['method'];
        if (!array_key_exists('group', $methodAnnotations)) {
            return;
        }
        return $methodAnnotations['group'];
    }

    /**
     * Check to see if the current test is in a group
     *
     * @param string $name The group name
     *
     * @return bool
     */
    protected function isInGroup($name)
    {
        return in_array($name, $this->getGroups());
    }

    /**
     * Allow admin actions
     *
     * Should not be used during unit tests
     *
     * @return void
     */
    protected function allowAdminActions()
    {
        if ($this->isInGroup('unit')) {
            throw new RuntimeException(__METHOD__ . ' should not be used '
                . 'during unit tests');
        }

        $this->_oldStoreId = Mage::app()->getStore()->getId();

        Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
    }

    /**
     * Revert allow admin actions
     *
     * Should not be used during unit tests
     *
     * @return void
     */
    protected function revertAllowAdminActions()
    {
        if ($this->isInGroup('unit')) {
            throw new RuntimeException(__METHOD__ . ' should not be used '
                . 'during unit tests');
        }
        
        Mage::app()->getStore()->setId($this->_oldStoreId);
    }

    /**
     * Create a real product
     *
     * @param string $sku  The SKU
     * @param array  $data Override data
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function createProduct($sku, $data = array())
    {
        $api = Mage::getModel('catalog/product_api');

        $defaultData = array(
            'name'                      => 'Test Product',
            'status'                    => 1,
            'visibility'                => 4,
            'shipment_turnover'         => 7,
            'price'                     => 10,
            'rrp_price'                 => 10,
            'tax_class_id'              => 2,
            'meta_title'                => 'test',
            'meta_keyword'              => 'test',
            'meta_description'          => 'test',
            'package_id'                => 1,
            'shippingoption_base_price' => 10,
            'shippingoption_type'       => 1,
            'website_ids'               => array(1),
        );
        $data = array_merge($defaultData, $data);

        $id = $api->create('simple', 4, $sku, $data);
        $product = Mage::getModel('catalog/product')->load($id);

        return $product;
    }

    /**
     * Delete a real product
     *
     * @return void
     */
    protected function deleteProduct($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $id = $product->getId();
        } else {
            $id = $product;
        }
        
        $this->allowAdminActions();
        Mage::getModel('catalog/product')->setId($id)->delete();
        $this->revertAllowAdminActions();
    }

    /**
     * Create a real category
     *
     * @param string $parentId The parent ID
     * @param array  $data     Override data
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function createCategory($parentId = null, $data = array())
    {
        $api = Mage::getModel('catalog/category_api');

        if ($parentId === null) {
            $parentId = Mage::app()->getStore()->getRootCategoryId();
        }

        $defaultData = array(
            'name'                 => 'Test Category',
            'url_key'              => 'test-category',
            'is_active'            => true,
            'include_in_menu'      => false,
            'made_forward_enabled' => false,
            'available_sort_by'    => array('name'),
            'default_sort_by'      => 'name',
        );
        $data = array_merge($defaultData, $data);

        $id = $api->create($parentId, $data);
        $category = Mage::getModel('catalog/category')->load($id);

        return $category;
    }

    /**
     * Delete a real category
     *
     * @return void
     */
    protected function deleteCategory($category)
    {
        if ($category instanceof Mage_Catalog_Model_Category) {
            $id = $category->getId();
        } else {
            $id = $category;
        }
        
        $this->allowAdminActions();
        Mage::getModel('catalog/category')->setId($id)->delete();
        $this->revertAllowAdminActions();
    }

    /**
     * Add a product to a category
     *
     * @param mixed $category Category
     * @param mixed $product  Product
     *
     * @return void
     */
    protected function addProductToCategory($category, $product)
    {
        $positions = $category->getProductsPosition();
        $productId = $product->getId();
        $positions[$productId] = 0;
        $category->setPostedProducts($positions);
        $category->save();
    }
}

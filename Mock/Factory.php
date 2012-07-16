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
 * Mock factory
 *
 * The factory can be used in conjunction with {@see Made_Test_Runkit_Helper} by
 * calling Made_Test_Mock_Factory::hookAll() which will rewrite Magento methods
 * where needed to ensure that all class instantiation comes through the factory
 *
 * If you don't fancy that, you can inject this factory into the objects under
 * test and call $factory->getModel('blah/blah') rather than Mage::getModel()
 *
 * Either way you choose, you can inject mock single-instance objects which can
 * be supplied back as-is, or cloned, or you can inject a callback to provide
 * your own multiple (or single) instance mocks. Remember you're not limited to
 * PHPUnit mocks - Mockery, Phake or any other good mocking framework (tm) can
 * be used (or even crap ones like {@see Made_Test_HydratingFactory_Xml}
 *
 * @category Made
 * @package  Made_Test
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://www.made.com/license.txt Commercial license
 * @link     N/A
 */
class Made_Test_Mock_Factory
{
    /**
     * Holds mock instances to be returned by getModel()
     *
     * @var array
     */
    protected $_mockInstances = array();

    /**
     * Setting to control cloning of mock instances or not
     *
     * @var array
     */
    protected $_cloneSetting = array();

    /**
     * Callback variables, these could be lambda functions, strings, arrays etc,
     * basically anything expected by call_user_func()
     *
     * @var array
     */
    protected $_mockCallbacks = array();

    /**
     * The callback to be used when no other way of loading a model is present,
     * this should be anything accepted by call_user_func()
     *
     * @var array
     */
    protected $_finalCallback;

    /**
     * Return a mock object, be it via instance, callback or final callback
     *
     * @throws Exception When no viable option exists to return an object
     *
     * @param string $objectClass The Magento object string, such as catalog/product
     * @param array  $arguments  Arguments to the object
     *
     * @return mixed
     */
    public function get($objectClass = '', $arguments = array())
    {
        // returned a (potentially cloned) instance
        if (array_key_exists($objectClass, $this->_mockInstances)) {
            if ($this->_cloneSetting[$objectClass]) {
                return clone $this->_mockInstances[$objectClass];
            } else {
                return $this->_mockInstances[$objectClass];
            }
        }

        // return by callback
        if (array_key_exists($objectClass, $this->_mockCallbacks)) {
            return call_user_func($this->_mockCallbacks[$objectClass]);
        }

        // as a last resort, call the fallback callback
        if (!empty($this->_finalCallback)) {
            return call_user_func($this->_finalCallback, $objectClass);
        }

        throw new Exception("No instance could be returned for $objectClass");
    }

    /**
     * Set a mock instance to be returned when calling {@see get()}, with the
     * option to clone the instance when returning
     *
     * Cloning on return is a way to ensure you have individual instances, but
     * be careful to ensure this is the behaviour you want - it could easily not
     * be! My advice is that {@see setCallbackMock()} is much more flexible and
     * probably what you want
     *
     * @param string   $model    The Magento object string, such as catalog/product
     * @param stdClass $instance An instance of an object
     * @param bool     $clone    True if the instance should be cloned when returned
     *
     * @return Made_Test_Mock_Factory
     */
    public function setInstanceMock($model, $instance, $clone = false)
    {
        $this->_mockInstances[$model] = $instance;
        $this->_cloneSetting[$model] = $clone;

        return $this;
    }

    /**
     * Set a mock to be created via a callback
     *
     * This differs from {@see setInstanceMock()} because setting an instance
     * is effectively dumb - it can return a single instance, or multiple
     * instances via cloning, whereas a callback can contain any behaviour
     * whatsoever, so returning a singleton, or multiple instantiations with
     * totally different data is possible
     *
     * call_user_func can take any of these:
     *
     *      'class:method'              // static method
     *      array($class, '::method')   // static method
     *      'method'                    // global
     *      array($obj, 'method')       // instance method
     *      function() { }              // lamdba
     *
     * @param string       $model    The Magento object string, such as catalog/product
     * @param string|array $callback A callback accepted by {@link http://uk.php.net/manual/en/function.call-user-func.php}
     *
     * @return Made_Test_Mock_Factory
     */
    public function setCallbackMock($model, $callback)
    {
        $this->_mockCallbacks[$model] = $callback;

        return $this;
    }

    /**
     * Set a callback to be used if the mock factory holds no other way of
     * making a mock of the requested object.
     * 
     * @param $callback A callback accepted by {@link http://uk.php.net/manual/en/function.call-user-func.php}
     *                  or a null value to remove the current fallback
     *
     * @return void
     */
    public function setFinalCallback($callback)
    {
        $this->_finalCallback = $callback;
    }

    /**
     * Reset all to defaults
     *
     * @return void
     */
    public function reset()
    {
        $this->_mockInstances = array();
        $this->_cloneSetting  = array();
        $this->_mockCallbacks = array();
        $this->_finalCallback = null;
    }
}

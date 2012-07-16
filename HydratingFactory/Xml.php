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
 * Hydrating Factory - This creates objects and hydrates them (with data (which
 * could be other objects)) from an XML source. It's useful for fixture creation
 * but bear in mind that you lose out on the function expectation and alteration
 * features of mocks from mocking frameworks such as PHPUnit, Mockery and Phake
 *
 * You can use multiple instances of this class, but it's much easier to just
 * use {@see makeMock()} statically
 *
 * @deprecated This is fairly lame, try not to use it. It will go soon
 *
 * @category Made
 * @package  Made_Test
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://www.made.com/license.txt Commercial license
 * @link     N/A
 */
class Made_Test_HydratingFactory_Xml
{
    /**
     * @var SimpleXMLElement The parsed XML
     */
    protected $_simpleXmlElement;

    /**
     * @var mixed The mock object
     */
    protected $_mockObject;

    /**
     * @var Made_Test_HydratingFactory_Xml An instance of this class
     */
    protected static $_instance;

    /**
     * Load either a file or a string
     *
     * @param null $load File or string
     *
     * @return void
     */
    public function load($load = null)
    {
        if (null !== $load) {
            // file
            if (is_file($load)) {
                $this->loadFile($load);
            }
            // string
            elseif (is_string($load)) {
                $this->loadString($load);
            }
        }
    }

    /**
     * Load a file
     *
     * @param $file
     *
     * @return void
     */
    public function loadFile($file)
    {
        $this->_simpleXmlElement = new SimpleXmlElement($file, null, true);

        $this->_mockObject = $this->_getObject($this->_simpleXmlElement);
    }

    /**
     * Load a string
     *
     * @param $str
     *
     * @return void
     */
    public function loadString($str)
    {
        $this->_simpleXmlElement = new SimpleXmlElement($str);

        $this->_mockObject = $this->_getObject($this->_simpleXmlElement);
    }

    /**
     * Get the root object in the XML
     *
     * This starts off the XML to mock object creation process
     *
     * @throws Exception
     *
     * @param SimpleXMLElement $elem
     *
     * @return mixed The object
     */
    protected function _getObject(SimpleXMLElement $elem)
    {
        if (!$className = (string) $elem['class']) {
            throw new Exception('No class property found');
        }

        $object = new $className();

        foreach ($elem->children() as $childElem) {
            $propName = $childElem->getName();
            $val = $this->_getValue($childElem);
            $object->setData($propName, $val);
        }

        return $object;
    }

    /**
     * Gets called for each node in the XML, called by {@see _getObject()} then
     * subsequantally calls either {@see _getObject()} or {@see _getNonObject()}
     *
     * @param SimpleXMLElement $elem
     *
     * @return array|bool|int|null|string
     */
    protected function _getValue(SimpleXMLElement $elem)
    {
        if ((string) $elem['class']) {
            $val = $this->_getObject($elem);
        } else {
            $val = $this->_getNonObject($elem);
        }
        return $val;
    }

    /**
     * Gets called for each non object node in the XML
     *
     * @param SimpleXMLElement $elem
     *
     * @return array|bool|int|null|string
     */
    protected function _getNonObject(SimpleXMLElement $elem)
    {
        // get the value
        $val = (string) $elem;

        // get the datatype, which is within the xs namespace, default to null
        $dataType = null;
        $ns = $elem->getNameSpaces(true);
        if (array_key_exists('xs', $ns)) {
            $xsElem = $elem->attributes($ns['xs']);
            $dataType = (string) $xsElem['type'];
        }

        // array
        if ('array' == $dataType) {
            $arr = array();
            foreach ($elem as $childElem) {
                $arr[] = $this->_getValue($childElem);
            }
            $val = $arr;
        }

        // boolean
        elseif ('boolean' == $dataType) {
            $val = (bool) $val;
        }

        // int
        elseif ('int' == $dataType) {
            $val = (int) $val;
        }

        // null
        elseif (!strlen($val)) {
            $val = null;
        }

        // otherwise the value is left as-is

        return $val;
    }

    /**
     * Return the finished mock object
     *
     * @return mixed
     */
    public function getMock()
    {
        return $this->_mockObject;
    }

    /**
     * Reset
     * 
     * @return void
     */
    public function reset()
    {
        $this->_simpleXmlElement = null;
        $this->_mockObject = null;
    }

    /**
     * Static method to get a mock
     * 
     * @static
     *
     * @param null $load
     *
     * @return mixed
     */
    public static function makeMock($load = null)
    {
        if (!self::$_instance) {
            self::$_instance = new Made_Test_HydratingFactory_Xml;
        }
        self::$_instance->load($load);
        $mock = self::$_instance->getMock();
        self::$_instance->reset();
        return $mock;
    }
}

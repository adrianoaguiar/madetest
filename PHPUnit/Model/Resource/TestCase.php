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
 * Resource model testcase
 *
 * @category Made
 * @package  Made_Test
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://www.made.com/license.txt Commercial license
 * @link     N/A
 */
abstract class Made_Test_PHPUnit_Model_Resource_TestCase
    extends Made_Test_PHPUnit_Model_TestCase
{
    /**
     * Get a resource model
     *
     * Should not be used during unit tests
     *
     * @param string $class The resource model class shortcut
     *
     * @return Varien_Data_Collection_Db
     */
    public function getResourceModel($class)
    {
        if ($this->isInGroup('unit')) {
            throw new RuntimeException(__METHOD__ . ' should not be used '
                . 'during unit tests');
        }

        return Mage::getResourceModel($class);
    }
}

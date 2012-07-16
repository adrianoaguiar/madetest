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
 * Helper testcase
 *
 * @category Made
 * @package  Made_Test
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://www.made.com/license.txt Commercial license
 * @link     N/A
 */
abstract class Made_Test_PHPUnit_Helper_TestCase
    extends Made_Test_PHPUnit_Abstract_TestCase
{
    /**
     * Get a helper
     *
     * Should not be used during unit tests
     *
     * @param string $class The helper class shortcut
     *
     * @return Mage_Core_Helper_Abstract
     */
    public function getHelper($class)
    {
        if ($this->isInGroup('unit')) {
            throw new RuntimeException(__METHOD__ . ' should not be used '
                . 'during unit tests');
        }

        return Mage::helper($class);
    }
}

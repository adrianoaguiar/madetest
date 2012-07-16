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
 * Model testcase
 *
 * @category Made
 * @package  Made_Test
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://www.made.com/license.txt Commercial license
 * @link     N/A
 */
abstract class Made_Test_PHPUnit_Model_TestCase
    extends Made_Test_PHPUnit_Abstract_TestCase
{
    /**
     * Get a model
     *
     * Should not be used during unit tests
     *
     * @param string $class The model class shortcut
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getModel($class)
    {
        if ($this->isInGroup('unit')) {
            throw new RuntimeException(__METHOD__ . ' should not be used '
                . 'during unit tests');
        }

        return Mage::getModel($class);
    }
}

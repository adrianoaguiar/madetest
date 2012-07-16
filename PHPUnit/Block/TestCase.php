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
 * Block testcase
 *
 * @category Made
 * @package  Made_Test
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://www.made.com/license.txt Commercial license
 * @link     N/A
 */
abstract class Made_Test_PHPUnit_Block_TestCase
    extends Made_Test_PHPUnit_Abstract_TestCase
{
    /**
     * Create a block
     *
     * @param string $type      Block type
     * @param string $blockName Block name (anonymous otherwise)
     * @param array $attributes Block attributes
     *
     * @return Mage_Core_Block_Abstract
     */
    public function createBlock($type, $name='', array $attributes = array())
    {
        if ($this->isInGroup('unit')) {
            throw new RuntimeException(__METHOD__ . ' should not be used '
                . 'during unit tests');
        }

        return Mage::app()->getLayout()->createBlock($type, $name, $attributes);
    }

    /**
     * Setup for unit tests
     *
     * @return void
     */
    protected function setUpUnit()
    {
        parent::setUpUnit();

        /**
         * Mage_Core_Block_Abstract::toHtml() tries to translate blocks, so we
         * need to mock the translation model
         *
         * @todo see if this is needed, I'm not sure
         */
        $this->getFactory('model')->setInstanceMock(
            'core/translate',
            $this->getMock('Mage_Core_Model_Translate')
        );
    }
}

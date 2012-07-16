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
 * Mock product
 *
 * @deprecated This was made for use with a hydrating factory, and we try not to
 *             use that any more, so this is deprecated
 *
 * @category Made
 * @package  Made_Test
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://www.made.com/license.txt Commercial license
 * @link     N/A
 */
class Made_Test_Mock_Object_Product extends Mage_Catalog_Model_Product
{
    public function load($id, $field=null)
    {
        return $this;
    }
}

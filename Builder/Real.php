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
 * Real object builder, used to create real Magento entities
 *
 * @category Made
 * @package  Made_Test
 * @author   Mike Whitby <michael.whitby@made.com>
 * @license  http://www.made.com/license.txt Commercial license
 * @link     N/A
 */
class Made_Test_Builder_Real
{
    /**
     * The store ID we switched away when using admin actions
     */
    public static $oldStoreId;

    /**
     * Create a product
     *
     * @param string $sku  The SKU, if not passed in will be random
     * @param array  $data Override data
     *
     * @return Mage_Catalog_Model_Product
     */
    public static function createProduct($sku = null, $data = array())
    {
        if (!$sku) {
            $sku = substr(md5(rand() . rand()), 0, 10);
        }

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
     * Delete a product
     *
     * @param Mage_Catalog_Model_Product | int $product The product ID or
     *                                                  instance
     *
     * @return void
     */
    public static function deleteProduct($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $id = $product->getId();
        } else {
            $id = $product;
        }
        
        self::allowAdminActions();
        Mage::getModel('catalog/product')->setId($id)->delete();
        self::revertAllowAdminActions();
    }

    /**
     * Create a category
     *
     * @param string $parentId The parent ID
     * @param array  $data     Override data
     *
     * @return Mage_Catalog_Model_Category
     */
    public static function createCategory($parentId = null, $data = array())
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
     * Delete a category
     *
     * @param Mage_Catalog_Model_Category | int $category The category ID or
     *                                                    instance
     *
     * @return void
     */
    public static function deleteCategory($category)
    {
        if ($category instanceof Mage_Catalog_Model_Category) {
            $id = $category->getId();
        } else {
            $id = $category;
        }
        
        self::allowAdminActions();
        Mage::getModel('catalog/category')->setId($id)->delete();
        self::revertAllowAdminActions();
    }

    /**
     * Add a product to a category
     *
     * @param mixed $category Category
     * @param mixed $product  Product
     *
     * @return void
     */
    public static function addProductToCategory($category, $product)
    {
        $positions = $category->getProductsPosition();
        $productId = $product->getId();
        $positions[$productId] = 0;
        $category->setPostedProducts($positions);
        $category->save();
    }

    /**
     * Create a customer
     *
     * @param string $email E-mail address, if not passed in will be random
     * @param array  $data  Override data
     *
     * @return Mage_Catalog_Model_Product
     */
    public static function createCustomer($email = null, $data = array())
    {
        if (!$email) {
            $email = substr(md5(rand() . rand()), 0, 10) . '@made.local';
        }

        $api = Mage::getModel('customer/customer_api');

        $defaultData = array(
            'email'     => $email,
            'password'  => 'open.123',
            'firstname' => 'Test',
            'lastname'  => 'Test',
            'group_id'  => 1,
        );
        $data = array_merge($defaultData, $data);

        $id = $api->create($data);
        $product = Mage::getModel('customer/customer')->load($id);

        return $product;
    }

    /**
     * Delete a customer
     *
     * @param Mage_Customer_Model_Customer|intString $customer The customer ID, email or instance
     *
     * @return void
     */
    public static function deleteCustomer($customer)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $id = $customer->getId();
        } else if (!is_numeric($customer)) {
            $id = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($customer)
                ->getId();
            if (!$id) {
                return;
            }
        } else {
            $id = $customer;
        }
        
        self::allowAdminActions();
        Mage::getModel('customer/customer')->setId($id)->delete();
        self::revertAllowAdminActions();
    }

    /**
     * Create an order
     *
     * @param array                                     $itemsData       A 2d array of items, keyed by sku on the 1st
     *                                                                   dimension then associative (such as qty => 1) on
     *                                                                   the 2nd dimension - see examples below
     * @param Mage_Customer_Model_Customer | int | null $customer        Null to have no customer assigned to the order,
     *                                                                   or an int or instance of Mage_Customer_Model_Customer
     * @param array                                     $billingAddress  An associative array of billing address data
     * @param array                                     $shippingAddress An associative array of shipping address data
     * @param string                                    $shippingMethod  The shipping method. If a method was passed in
     *                                                                   the shipping address it will get overwritten
     *                                                                   with the method defined here
     * @param array                                     $quoteData       An associative array of quote data, such as
     *                                                                   store_id or customer_email
     *
     * Example - Create an order for customer ID 1, with 2 items, and the default address data:
     *
     * <code>
     * real::createOrder(
     *     array(
     *         'SKU1' => array('qty' => 4),
     *         'SKU2' => array('qty' => 2),
     *     ),
     *     Mage::getModel('customer/customer')>load(1),
     *     null,
     *     null,
     *     'madeshippingoption_optionpricing'
     * );
     * </code>
     *
     * Example - Create a guest order, with 1 item, and specific address data:
     *
     * <code>
     * real::createOrder(
     *     array(
     *         'SKU1' => array('qty' => 1),
     *     ),
     *     null,
     *     array(
     *         'firstname'     => 'Ben',
     *         'lastname'      => 'Fox',
     *         'email'         => 'benfox@example.com',
     *         'telephone'     => '077 6259 3560',
     *         'street'        => '1 York Road',
     *         'city'          => 'Robertsbridge',
     *         'region_id'     => 4,
     *         'postcode'      => 'TN32 5LF',
     *         'country_id'    => 'GB',
     *     ),
     *     array(
     *         'firstname'     => 'Ben',
     *         'lastname'      => 'Fox',
     *         'email'         => 'benfox@example.com',
     *         'telephone'     => '077 6259 3560',
     *         'street'        => '1 York Road',
     *         'city'          => 'Robertsbridge',
     *         'region_id'     => 4,
     *         'postcode'      => 'TN32 5LF',
     *         'country_id'    => 'GB',
     *     ),
     *     'madeshippingoption_optionpricing'
     * );
     * </code>
     *
     * @return Mage_Sales_Model_Order The order
     */
    public static function createOrder(array $itemsData,
                                       $customer = null,
                                       $billingAddress = array(),
                                       $shippingAddress = array(),
                                       $shippingMethod = 'flatrate_flatrate',
                                       $quoteData = array())
    {
        // argument conversion
        if (is_numeric($customer)) {
            $customer = Mage::getModel('customer/customer')->load($customer);
        }
        if (!is_array($billingAddress)) {
            $billingAddress = array();
        }
        if (!is_array($shippingAddress)) {
            $shippingAddress = array();
        }
        if (!is_array($quoteData)) {
            $quoteData = array();
        }

        // default data
        $defaultAddressData = array(
            'firstname'     => 'Eva',
            'lastname'      => 'Perry',
            'email'         => 'evaperry@example.com',
            'telephone'     => '079 4645 4750',
            'street'        => '62 Argyll Street',
            'city'          => 'Arbroath',
            'region_id'     => 4,
            'postcode'      => 'DD11 3UD',
            'country_id'    => 'GB',
        );
        $quoteData = array(
            'store_id'       => 1,
            'customer_email' => 'evaperry@example.com',
        );

        // create quote
        $quote = Mage::getModel('sales/quote');
        $quote->addData($quoteData);

        // customer (optional)
        if ($customer) {
            $quote->setCustomer($customer);
        }

        // items
        $productSingleton = Mage::getSingleton('catalog/product');
        foreach ($itemsData as $sku => $itemData) {
            if (!$productId = $productSingleton->getIdBySku($sku)) {
                Mage::throwException("product with sku of $sku does not exist");
            }
            try {
                $product = Mage::getModel('catalog/product');
                $product->load($productId);
                
                $quote->addProduct($product, $itemData['qty']);
            } catch (Exception $e) {
                Mage::throwException("could not add $sku to quote: " . $e->getMessage());
            }
        }

        // addresses
        $billingAddress = array_merge($defaultAddressData, $billingAddress);
        $shippingAddress = array_merge($defaultAddressData, $shippingAddress);
        try {
            // set shipping method
            if ($shippingMethod) {
                $shippingAddress['shipping_method'] = $shippingMethod;
            }

            // add billing address to the quote
            $quote->getBillingAddress()->addData($billingAddress);

            // add shipping address to the quote
            if ($shippingAddress) {
                $shippingAddressObj = $quote->getShippingAddress();
                $shippingAddressObj->addData($shippingAddress);
                $shippingAddressObj->setCollectShippingRates(true);
            }
        } catch (Exception $e) {
            Mage::throwException('could not set addresses: ' . $e->getMessage());
        }

        // payment
        try {
            $quote->getPayment()->setMethod('checkmo');
            $quote->collectTotals();
            $quote->save();
        } catch (Exception $e) {
            Mage::throwException('could not set payment: ' . $e->getMessage());
        }

        // place order
        try {
            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();
            $order = $service->getOrder();

            // we seem to have to do this, would have thought it would get done
            // for us, but hey ho - check that this is indeed the case
            $quote->save();
        } catch (Exception $e) {
            Mage::throwException('could not convert quote to order: ' . $e->getMessage());
        }

        // invoice
        try {
            $invoice = $order->prepareInvoice();
            $invoice->register();

            $transactionSave = Mage::getModel('core/resource_transaction');
            $transactionSave->addObject($invoice);
            $transactionSave->addObject($order);
            $transactionSave->save();
        } catch (Exception $e) {
            Mage::throwException('could not invoice order: ' . $e->getMessage());
        }

        // reload and return
        $order = Mage::getModel('sales/order')->loadByIncrementId($order->getIncrementId());
        return $order;
    }

    /**
     * Delete an order
     *
     * @param Mage_Sales_Model_Order|int $order The order ID, increment ID, or instance
     *
     * @return void
     */
    public static function deleteOrder($order)
    {
        if ($order instanceof Mage_Sales_Model_Order) {
            $id = $order->getId();
        } else if (is_numeric($order) && strlen($order) == 9) {
            $id = Mage::getModel('sales/order')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByIncrementId($order)
                ->getId();
            if (!$id) {
                return;
            }
        } else {
            $id = $order;
        }
        
        self::allowAdminActions();
        Mage::getModel('sales/order')->setId($id)->delete();
        self::revertAllowAdminActions();
    }

    /**
     * Create COIs for an order and return them as an array
     *
     * @param Mage_Sales_Model_Order | int $order  The order ID or instance
     * @param string                       $status The status for the COIs
     *
     * @return array The created COIs
     */
    public static function createCoisForOrder($order, $status = 'pending')
    {
        $allowedStatuses = array(
            'cancelled',
            'pending',
            'warehouse',
            'dispatched',
            'delivered',
        );
        if (!in_array($status, $allowedStatuses)) {
            throw new InvalidArgumentException("Invalid status code of $status");
        }

        if (is_int($order)) {
            $order = Mage::getModel('sales/order')->load($order);
        }

        $cois = array();
        foreach ($order->getAllItems() as $item) {
            $cois = array_merge($cois, self::createCoisForOrderItem($item, $status));
        }

        return $cois;
    }

    /**
     * Create COIs for an order item and return them as an array
     *
     * @param Mage_Sales_Model_Order | int $orderItem The order ID or instance
     * @param string                       $status    The status for the COIs
     *
     * @return array The created COIs
     */
    public static function createCoisForOrderItem($orderItem, $status = 'pending')
    {
        $allowedStatuses = array(
            'cancelled',
            'pending',
            'warehouse',
            'dispatched',
            'delivered',
        );
        if (!in_array($status, $allowedStatuses)) {
            throw new InvalidArgumentException("Invalid status code of $status");
        }

        if (is_int($orderItem)) {
            $orderItem = Mage::getModel('sales/order_item')->load($orderItem);
        }

        $order = $orderItem->getOrder();
        $product = Mage::getModel('catalog/product')->load($orderItem->getProductId());

        // +30 days for the PAD
        $padDateTime = new DateTime();
        $padDateTime->add(DateInterval::createFromDateString('+30 days'));

        // +7 days for the dispatch
        $dispatchDateTime = new DateTime();
        $dispatchDateTime->add(DateInterval::createFromDateString('+7 days'));

        // set carrier data depending on status
        if ($status == 'dispatched') {
            $status = 'shipped';
            $carrier = 'yodel';
            $carrierDesc = 'yodel';
            $tracking = '[{"id":"test","url":"test","code":"test"}]';
            $carrierPhone = '01234 456 456';
            $dispatchId = 1;
        } elseif ($status == 'delivered') {
            $status = 'shipped';
            $carrier = 'yodel';
            $carrierDesc = 'yodel';
            $tracking = '[{"id":"test","url":"test","code":"test","tracking_status":"Delivered"}]';
            $carrierPhone = '01234 456 456';
            $dispatchId = 1;
        } else {
            $carrier = null;
            $carrierDesc = null;
            $tracking = null;
            $carrierPhone = null;
            $dispatchId = null;
        }

        $cois = array();
        for ($i = 0; $i < $orderItem->getQtyOrdered(); $i++) {
            $coi = Mage::getModel('ait_beingmade/customer_order_item');
            $coi->addData(array(
                'batch_id'               => null,
                'order_increment_id'     => $order->getIncrementId(),
                'product_id'             => $product->getId(),
                'price_paid'             => $product->getPrice(),
                'published_arrival_date' => $padDateTime->format('Y-m-d H:i:s'),
                'status'                 => $status,
                'openerp_coi_id'         => 1,
                'tracking'               => $tracking,
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
                'shipping_address_id'    => $order->getShippingAddressId(),
                'address_last_update'    => date('Y-m-d H:i:s'),
                'dispatch_id'            => $dispatchId,
                'display_updated_at'     => date('Y-m-d H:i:s'),
                'dispatch_date'          => $dispatchDateTime->format('Y-m-d H:i:s'),
                'carrier'                => $carrier,
                'carrier_phone'          => $carrierPhone,
                'carrier_description'    => $carrierDesc,
            ));
            $coi->save();
            $cois[] = $coi;
        }

        return $cois;
    }

    /**
     * Delete multiple COIs
     *
     * @param array $cois An array of COIs
     *
     * @return void
     */
    public static function deleteCois(array $cois)
    {
        foreach ($cois as $coi) {
            self::deleteCoi($coi);
        }
    }

    /**
     * Delete a COI
     *
     * @param AmpersandIT_BeingMade_Model_Customer_Order_Item $coi A COI
     *
     * @return void
     */
    public static function deleteCoi(AmpersandIT_BeingMade_Model_Customer_Order_Item $coi)
    {
        self::allowAdminActions();
        $coi->delete();
        self::revertAllowAdminActions();
    }

    /**
     * Allow admin actions
     *
     * @return void
     */
    public static function allowAdminActions()
    {
        self::$oldStoreId = Mage::app()->getStore()->getId();
        Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
    }

    /**
     * Revert allow admin actions
     *
     * @return void
     */
    public static function revertAllowAdminActions()
    {
        Mage::app()->getStore()->setId(self::$oldStoreId);
        self::$oldStoreId = null;
    }
}

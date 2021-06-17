<?php
namespace Thecon\VerifiedBuyer\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ObjectManager;

class Data extends AbstractHelper
{
    protected $resourceConnection;
    protected $reviewFactory;

    public function __construct(Context $context,
        ResourceConnection $resourceConnection, 
        \Magento\Review\Model\ReviewFactory $reviewFactory)
    {
        $this->resourceConnection = $resourceConnection;
        $this->reviewFactory = $reviewFactory;
        parent::__construct($context);
    }
    public function runSqlQuery($review_id)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableO = $connection->getTableName('sales_order');
        $tableOI = $connection->getTableName('sales_order_item');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $user = $customerSession->getCustomer()->getId();
        $product = $objectManager->get('Magento\Framework\Registry')->registry('current_product');


        $query = "SELECT item_id FROM " . $tableOI . " oi, " . $tableO . " o" . " WHERE oi.product_id = ". $product->getId() ." AND oi.order_id = o.entity_id AND o.customer_id = " . $user;
        $result1 = $connection->fetchAll($query);

        $review = $this->reviewFactory->create()->load($review_id);
        $id = $review->getData('review_id');

        if(is_array($result1) && count($result1) > 0) {
            $qq = "UPDATE review_detail SET isValid = 1 WHERE review_id = " . $id;
            $connection->query($qq);
        }
        else {
            $aa = "UPDATE review_detail SET isValid = 0 WHERE review_id = " . $id;
            $connection->query($aa);
        }
    }
}
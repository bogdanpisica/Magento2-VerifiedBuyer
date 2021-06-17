<?php
namespace Thecon\VerifiedBuyer\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
		$installer = $setup;

		$installer->startSetup();

		if(version_compare($context->getVersion(), '1.0.0', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable( 'review_detail' ),
				'isValid',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,
					'length' => 0,
					'comment' => 'Verified Buyer',
					'after' => 'customer_id'
				]
			);
		}



		$installer->endSetup();
	}
}
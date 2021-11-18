<?php

namespace Barcode\Management\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface {

    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('admin_barcode')) {
            $table = $installer->getConnection()->newTable(
                            $installer->getTable('admin_barcode')
                    )
                    ->addColumn(
                            'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                            ], ' '
                    )
                    ->addColumn(
                            'barcode', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable => false'], ' '
                    )
                    ->addColumn(
                            'power', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable => false'], ' '
                    )
                    ->addColumn(
                            'color', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable => false'], ' '
                    )
                    ->addColumn(
                            'sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], ' '
                    )
                    ->setComment('Table');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                    $installer->getTable('admin_barcode'), $setup->getIdxName(
                            $installer->getTable('admin_barcode'), ['barcode', 'power', 'color', 'sku'], \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                    ), ['barcode', 'power', 'color', 'sku'], \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
    }

}

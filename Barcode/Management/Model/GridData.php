<?php

namespace Barcode\Management\Model;

class GridData {

    protected $connection;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource) {
        $this->connection = $resource->getConnection();
    }

    public function getBarcodeselect($barcode) {
        $tablename = $this->getTableNameDB();
        $select = $this->connection->select()->from(['a' => $tablename])->where('a.barcode=?',$barcode);
        $records = $this->connection->fetchAll($select);
        return $total_rows = count($records);
    }

    public function getSelectById($id) {
        $tablename = $this->getTableNameDB();
        $select = $this->connection->select()->from(['a' => $tablename])->where('a.id=?', (int) $id);
        return $records = $this->connection->fetchAll($select);
    }

    public function getUniqueBarcode($barcode, $id) {
        $tablename = $this->getTableNameDB();
        $select = $this->connection->select()->from(['a' => $tablename])->where('a.barcode=?', $barcode)->where('a.id!=?', (int) $id);
        $records = $this->connection->fetchAll($select);
        return $total_rows = count($records);
    }

    public function getSelectByBarcode($barcode) {
        $tablename = $this->getTableNameDB();
        $select = $this->connection->select()->from(['a' => $tablename])->where('a.barcode=?', $barcode);
        $records = $this->connection->fetchAll($select);
        return $total_rows = count($records);
    }

    public function Updatebarcodedata($csv, $where) {
        $tablename = $this->getTableNameDB();
        $saveData = $this->connection->update($tablename, $csv, $where);
        return $saveData;
    }

    public function getTableNameDB() {
        $tablename = $this->connection->getTableName('admin_barcode');
        return $tablename;
    }

}

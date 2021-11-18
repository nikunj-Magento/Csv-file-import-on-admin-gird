<?php

namespace Barcode\Management\Model;

use Barcode\Management\Api\Data\GridInterface;

class Grid extends \Magento\Framework\Model\AbstractModel implements GridInterface {

    const CACHE_TAG = 'admin_barcode';

    protected $_cacheTag = 'admin_barcode';
    protected $_eventPrefix = 'admin_barcode';

    protected function _construct() {
        $this->_init('Barcode\Management\Model\ResourceModel\Grid');
    }

    public function getEntityId() {
        return $this->getData(self::ID);
    }

    public function setEntityId($entityId) {
        return $this->setData(self::ID, $entityId);
    }

    public function getTitle() {
        return $this->getData(self::BARCODE);
    }

    public function setTitle($title) {
        return $this->setData(self::BARCODE, $title);
    }

    public function getContent() {
        return $this->getData(self::POWER);
    }

    public function setContent($content) {
        return $this->setData(self::POWER, $content);
    }

    public function getPublishDate() {
        return $this->getData(self::COLOR);
    }

    public function setPublishDate($publishDate) {
        return $this->setData(self::COLOR, $publishDate);
    }

    public function getIsActive() {
        return $this->getData(self::SKU);
    }

    public function setIsActive($isActive) {
        return $this->setData(self::SKU, $isActive);
    }

    public function getUpdateTime() {
        return $this->getData(self::UPDATE_TIME);
    }

    public function setUpdateTime($updateTime) {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    public function getCreatedAt() {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt) {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

}

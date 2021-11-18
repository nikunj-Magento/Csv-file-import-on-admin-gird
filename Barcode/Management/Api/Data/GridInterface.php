<?php

namespace Barcode\Management\Api\Data;

interface GridInterface
{
    const ID = 'id';
    const BARCODE = 'barcode';
    const POWER = 'power';
    const COLOR = 'color';
    const SKU = 'sku';

    public function getEntityId();

    public function setEntityId($entityId);

    public function getTitle();

    public function setTitle($title);

    public function getContent();

    public function setContent($content);

    public function getPublishDate();

    public function setPublishDate($publishDate);

    public function getIsActive();

    public function setIsActive($isActive);

    public function getUpdateTime();

    public function setUpdateTime($updateTime);

    public function getCreatedAt();

    public function setCreatedAt($createdAt);
}

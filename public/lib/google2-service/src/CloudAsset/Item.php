<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\CloudAsset;

class Item extends \Google\Model
{
  /**
   * Invalid. An origin type must be specified.
   */
  public const ORIGIN_TYPE_ORIGIN_TYPE_UNSPECIFIED = 'ORIGIN_TYPE_UNSPECIFIED';
  /**
   * This inventory item was discovered as the result of the agent reporting
   * inventory via the reporting API.
   */
  public const ORIGIN_TYPE_INVENTORY_REPORT = 'INVENTORY_REPORT';
  /**
   * Invalid. A type must be specified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * This represents a package that is installed on the VM.
   */
  public const TYPE_INSTALLED_PACKAGE = 'INSTALLED_PACKAGE';
  /**
   * This represents an update that is available for a package.
   */
  public const TYPE_AVAILABLE_PACKAGE = 'AVAILABLE_PACKAGE';
  protected $availablePackageType = SoftwarePackage::class;
  protected $availablePackageDataType = '';
  /**
   * When this inventory item was first detected.
   *
   * @var string
   */
  public $createTime;
  /**
   * Identifier for this item, unique across items for this VM.
   *
   * @var string
   */
  public $id;
  protected $installedPackageType = SoftwarePackage::class;
  protected $installedPackageDataType = '';
  /**
   * The origin of this inventory item.
   *
   * @var string
   */
  public $originType;
  /**
   * The specific type of inventory, correlating to its specific details.
   *
   * @var string
   */
  public $type;
  /**
   * When this inventory item was last modified.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Software package available to be installed on the VM instance.
   *
   * @param SoftwarePackage $availablePackage
   */
  public function setAvailablePackage(SoftwarePackage $availablePackage)
  {
    $this->availablePackage = $availablePackage;
  }
  /**
   * @return SoftwarePackage
   */
  public function getAvailablePackage()
  {
    return $this->availablePackage;
  }
  /**
   * When this inventory item was first detected.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Identifier for this item, unique across items for this VM.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Software package present on the VM instance.
   *
   * @param SoftwarePackage $installedPackage
   */
  public function setInstalledPackage(SoftwarePackage $installedPackage)
  {
    $this->installedPackage = $installedPackage;
  }
  /**
   * @return SoftwarePackage
   */
  public function getInstalledPackage()
  {
    return $this->installedPackage;
  }
  /**
   * The origin of this inventory item.
   *
   * Accepted values: ORIGIN_TYPE_UNSPECIFIED, INVENTORY_REPORT
   *
   * @param self::ORIGIN_TYPE_* $originType
   */
  public function setOriginType($originType)
  {
    $this->originType = $originType;
  }
  /**
   * @return self::ORIGIN_TYPE_*
   */
  public function getOriginType()
  {
    return $this->originType;
  }
  /**
   * The specific type of inventory, correlating to its specific details.
   *
   * Accepted values: TYPE_UNSPECIFIED, INSTALLED_PACKAGE, AVAILABLE_PACKAGE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * When this inventory item was last modified.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Item::class, 'Google_Service_CloudAsset_Item');

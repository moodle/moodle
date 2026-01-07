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

namespace Google\Service\NetworkSecurity;

class AddressGroup extends \Google\Collection
{
  /**
   * Default value.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * IP v4 ranges.
   */
  public const TYPE_IPV4 = 'IPV4';
  /**
   * IP v6 ranges.
   */
  public const TYPE_IPV6 = 'IPV6';
  protected $collection_key = 'purpose';
  /**
   * Required. Capacity of the Address Group
   *
   * @var int
   */
  public $capacity;
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Free-text description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. List of items.
   *
   * @var string[]
   */
  public $items;
  /**
   * Optional. Set of label tags associated with the AddressGroup resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. Name of the AddressGroup resource. It matches pattern
   * `projects/locations/{location}/addressGroups/`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. List of supported purposes of the Address Group.
   *
   * @var string[]
   */
  public $purpose;
  /**
   * Output only. Server-defined fully-qualified URL for this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Required. The type of the Address Group. Possible values are "IPv4" or
   * "IPV6".
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. Capacity of the Address Group
   *
   * @param int $capacity
   */
  public function setCapacity($capacity)
  {
    $this->capacity = $capacity;
  }
  /**
   * @return int
   */
  public function getCapacity()
  {
    return $this->capacity;
  }
  /**
   * Output only. The timestamp when the resource was created.
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
   * Optional. Free-text description of the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. List of items.
   *
   * @param string[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return string[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Optional. Set of label tags associated with the AddressGroup resource.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. Name of the AddressGroup resource. It matches pattern
   * `projects/locations/{location}/addressGroups/`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. List of supported purposes of the Address Group.
   *
   * @param string[] $purpose
   */
  public function setPurpose($purpose)
  {
    $this->purpose = $purpose;
  }
  /**
   * @return string[]
   */
  public function getPurpose()
  {
    return $this->purpose;
  }
  /**
   * Output only. Server-defined fully-qualified URL for this resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Required. The type of the Address Group. Possible values are "IPv4" or
   * "IPV6".
   *
   * Accepted values: TYPE_UNSPECIFIED, IPV4, IPV6
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
   * Output only. The timestamp when the resource was updated.
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
class_alias(AddressGroup::class, 'Google_Service_NetworkSecurity_AddressGroup');

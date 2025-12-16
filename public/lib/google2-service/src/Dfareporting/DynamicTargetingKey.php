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

namespace Google\Service\Dfareporting;

class DynamicTargetingKey extends \Google\Model
{
  public const OBJECT_TYPE_OBJECT_ADVERTISER = 'OBJECT_ADVERTISER';
  public const OBJECT_TYPE_OBJECT_AD = 'OBJECT_AD';
  public const OBJECT_TYPE_OBJECT_CREATIVE = 'OBJECT_CREATIVE';
  public const OBJECT_TYPE_OBJECT_PLACEMENT = 'OBJECT_PLACEMENT';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#dynamicTargetingKey".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this dynamic targeting key. This is a required field. Must be less
   * than 256 characters long and cannot contain commas. All characters are
   * converted to lowercase.
   *
   * @var string
   */
  public $name;
  /**
   * ID of the object of this dynamic targeting key. This is a required field.
   *
   * @var string
   */
  public $objectId;
  /**
   * Type of the object of this dynamic targeting key. This is a required field.
   *
   * @var string
   */
  public $objectType;

  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#dynamicTargetingKey".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of this dynamic targeting key. This is a required field. Must be less
   * than 256 characters long and cannot contain commas. All characters are
   * converted to lowercase.
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
   * ID of the object of this dynamic targeting key. This is a required field.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * Type of the object of this dynamic targeting key. This is a required field.
   *
   * Accepted values: OBJECT_ADVERTISER, OBJECT_AD, OBJECT_CREATIVE,
   * OBJECT_PLACEMENT
   *
   * @param self::OBJECT_TYPE_* $objectType
   */
  public function setObjectType($objectType)
  {
    $this->objectType = $objectType;
  }
  /**
   * @return self::OBJECT_TYPE_*
   */
  public function getObjectType()
  {
    return $this->objectType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicTargetingKey::class, 'Google_Service_Dfareporting_DynamicTargetingKey');

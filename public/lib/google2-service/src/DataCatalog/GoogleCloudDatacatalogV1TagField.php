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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1TagField extends \Google\Model
{
  /**
   * The value of a tag field with a boolean type.
   *
   * @var bool
   */
  public $boolValue;
  /**
   * Output only. The display name of this field.
   *
   * @var string
   */
  public $displayName;
  /**
   * The value of a tag field with a double type.
   *
   * @var 
   */
  public $doubleValue;
  protected $enumValueType = GoogleCloudDatacatalogV1TagFieldEnumValue::class;
  protected $enumValueDataType = '';
  /**
   * Output only. The order of this field with respect to other fields in this
   * tag. Can be set by Tag. For example, a higher value can indicate a more
   * important field. The value can be negative. Multiple fields can have the
   * same order, and field orders within a tag don't have to be sequential.
   *
   * @var int
   */
  public $order;
  /**
   * The value of a tag field with a rich text type. The maximum length is 10
   * MiB as this value holds HTML descriptions including encoded images. The
   * maximum length of the text without images is 100 KiB.
   *
   * @var string
   */
  public $richtextValue;
  /**
   * The value of a tag field with a string type. The maximum length is 2000
   * UTF-8 characters.
   *
   * @var string
   */
  public $stringValue;
  /**
   * The value of a tag field with a timestamp type.
   *
   * @var string
   */
  public $timestampValue;

  /**
   * The value of a tag field with a boolean type.
   *
   * @param bool $boolValue
   */
  public function setBoolValue($boolValue)
  {
    $this->boolValue = $boolValue;
  }
  /**
   * @return bool
   */
  public function getBoolValue()
  {
    return $this->boolValue;
  }
  /**
   * Output only. The display name of this field.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  public function setDoubleValue($doubleValue)
  {
    $this->doubleValue = $doubleValue;
  }
  public function getDoubleValue()
  {
    return $this->doubleValue;
  }
  /**
   * The value of a tag field with an enum type. This value must be one of the
   * allowed values listed in this enum.
   *
   * @param GoogleCloudDatacatalogV1TagFieldEnumValue $enumValue
   */
  public function setEnumValue(GoogleCloudDatacatalogV1TagFieldEnumValue $enumValue)
  {
    $this->enumValue = $enumValue;
  }
  /**
   * @return GoogleCloudDatacatalogV1TagFieldEnumValue
   */
  public function getEnumValue()
  {
    return $this->enumValue;
  }
  /**
   * Output only. The order of this field with respect to other fields in this
   * tag. Can be set by Tag. For example, a higher value can indicate a more
   * important field. The value can be negative. Multiple fields can have the
   * same order, and field orders within a tag don't have to be sequential.
   *
   * @param int $order
   */
  public function setOrder($order)
  {
    $this->order = $order;
  }
  /**
   * @return int
   */
  public function getOrder()
  {
    return $this->order;
  }
  /**
   * The value of a tag field with a rich text type. The maximum length is 10
   * MiB as this value holds HTML descriptions including encoded images. The
   * maximum length of the text without images is 100 KiB.
   *
   * @param string $richtextValue
   */
  public function setRichtextValue($richtextValue)
  {
    $this->richtextValue = $richtextValue;
  }
  /**
   * @return string
   */
  public function getRichtextValue()
  {
    return $this->richtextValue;
  }
  /**
   * The value of a tag field with a string type. The maximum length is 2000
   * UTF-8 characters.
   *
   * @param string $stringValue
   */
  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }
  /**
   * @return string
   */
  public function getStringValue()
  {
    return $this->stringValue;
  }
  /**
   * The value of a tag field with a timestamp type.
   *
   * @param string $timestampValue
   */
  public function setTimestampValue($timestampValue)
  {
    $this->timestampValue = $timestampValue;
  }
  /**
   * @return string
   */
  public function getTimestampValue()
  {
    return $this->timestampValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1TagField::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1TagField');

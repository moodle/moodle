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

namespace Google\Service\CertificateAuthorityService;

class AttributeTypeAndValue extends \Google\Model
{
  /**
   * Attribute type is unspecified.
   */
  public const TYPE_ATTRIBUTE_TYPE_UNSPECIFIED = 'ATTRIBUTE_TYPE_UNSPECIFIED';
  /**
   * The "common name" of the subject.
   */
  public const TYPE_COMMON_NAME = 'COMMON_NAME';
  /**
   * The country code of the subject.
   */
  public const TYPE_COUNTRY_CODE = 'COUNTRY_CODE';
  /**
   * The organization of the subject.
   */
  public const TYPE_ORGANIZATION = 'ORGANIZATION';
  /**
   * The organizational unit of the subject.
   */
  public const TYPE_ORGANIZATIONAL_UNIT = 'ORGANIZATIONAL_UNIT';
  /**
   * The locality or city of the subject.
   */
  public const TYPE_LOCALITY = 'LOCALITY';
  /**
   * The province, territory, or regional state of the subject.
   */
  public const TYPE_PROVINCE = 'PROVINCE';
  /**
   * The street address of the subject.
   */
  public const TYPE_STREET_ADDRESS = 'STREET_ADDRESS';
  /**
   * The postal code of the subject.
   */
  public const TYPE_POSTAL_CODE = 'POSTAL_CODE';
  protected $objectIdType = ObjectId::class;
  protected $objectIdDataType = '';
  /**
   * The attribute type of the attribute and value pair.
   *
   * @var string
   */
  public $type;
  /**
   * The value for the attribute type.
   *
   * @var string
   */
  public $value;

  /**
   * Object ID for an attribute type of an attribute and value pair.
   *
   * @param ObjectId $objectId
   */
  public function setObjectId(ObjectId $objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return ObjectId
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * The attribute type of the attribute and value pair.
   *
   * Accepted values: ATTRIBUTE_TYPE_UNSPECIFIED, COMMON_NAME, COUNTRY_CODE,
   * ORGANIZATION, ORGANIZATIONAL_UNIT, LOCALITY, PROVINCE, STREET_ADDRESS,
   * POSTAL_CODE
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
   * The value for the attribute type.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttributeTypeAndValue::class, 'Google_Service_CertificateAuthorityService_AttributeTypeAndValue');

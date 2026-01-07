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

class GoogleCloudDatacatalogV1FieldType extends \Google\Model
{
  /**
   * The default invalid value for a type.
   */
  public const PRIMITIVE_TYPE_PRIMITIVE_TYPE_UNSPECIFIED = 'PRIMITIVE_TYPE_UNSPECIFIED';
  /**
   * A double precision number.
   */
  public const PRIMITIVE_TYPE_DOUBLE = 'DOUBLE';
  /**
   * An UTF-8 string.
   */
  public const PRIMITIVE_TYPE_STRING = 'STRING';
  /**
   * A boolean value.
   */
  public const PRIMITIVE_TYPE_BOOL = 'BOOL';
  /**
   * A timestamp.
   */
  public const PRIMITIVE_TYPE_TIMESTAMP = 'TIMESTAMP';
  /**
   * A Richtext description.
   */
  public const PRIMITIVE_TYPE_RICHTEXT = 'RICHTEXT';
  protected $enumTypeType = GoogleCloudDatacatalogV1FieldTypeEnumType::class;
  protected $enumTypeDataType = '';
  /**
   * Primitive types, such as string, boolean, etc.
   *
   * @var string
   */
  public $primitiveType;

  /**
   * An enum type.
   *
   * @param GoogleCloudDatacatalogV1FieldTypeEnumType $enumType
   */
  public function setEnumType(GoogleCloudDatacatalogV1FieldTypeEnumType $enumType)
  {
    $this->enumType = $enumType;
  }
  /**
   * @return GoogleCloudDatacatalogV1FieldTypeEnumType
   */
  public function getEnumType()
  {
    return $this->enumType;
  }
  /**
   * Primitive types, such as string, boolean, etc.
   *
   * Accepted values: PRIMITIVE_TYPE_UNSPECIFIED, DOUBLE, STRING, BOOL,
   * TIMESTAMP, RICHTEXT
   *
   * @param self::PRIMITIVE_TYPE_* $primitiveType
   */
  public function setPrimitiveType($primitiveType)
  {
    $this->primitiveType = $primitiveType;
  }
  /**
   * @return self::PRIMITIVE_TYPE_*
   */
  public function getPrimitiveType()
  {
    return $this->primitiveType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1FieldType::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1FieldType');

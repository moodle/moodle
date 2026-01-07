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

class GoogleCloudDatacatalogV1FieldTypeEnumType extends \Google\Collection
{
  protected $collection_key = 'allowedValues';
  protected $allowedValuesType = GoogleCloudDatacatalogV1FieldTypeEnumTypeEnumValue::class;
  protected $allowedValuesDataType = 'array';

  /**
   * The set of allowed values for this enum. This set must not be empty and can
   * include up to 100 allowed values. The display names of the values in this
   * set must not be empty and must be case-insensitively unique within this
   * set. The order of items in this set is preserved. This field can be used to
   * create, remove, and reorder enum values. To rename enum values, use the
   * `RenameTagTemplateFieldEnumValue` method.
   *
   * @param GoogleCloudDatacatalogV1FieldTypeEnumTypeEnumValue[] $allowedValues
   */
  public function setAllowedValues($allowedValues)
  {
    $this->allowedValues = $allowedValues;
  }
  /**
   * @return GoogleCloudDatacatalogV1FieldTypeEnumTypeEnumValue[]
   */
  public function getAllowedValues()
  {
    return $this->allowedValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1FieldTypeEnumType::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1FieldTypeEnumType');

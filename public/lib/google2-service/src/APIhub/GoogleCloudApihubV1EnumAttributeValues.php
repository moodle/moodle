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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1EnumAttributeValues extends \Google\Collection
{
  protected $collection_key = 'values';
  protected $valuesType = GoogleCloudApihubV1AllowedValue::class;
  protected $valuesDataType = 'array';

  /**
   * Required. The attribute values in case attribute data type is enum.
   *
   * @param GoogleCloudApihubV1AllowedValue[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return GoogleCloudApihubV1AllowedValue[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1EnumAttributeValues::class, 'Google_Service_APIhub_GoogleCloudApihubV1EnumAttributeValues');

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

namespace Google\Service\Datapipelines;

class GoogleCloudDatapipelinesV1MapValueEntry extends \Google\Model
{
  protected $keyType = GoogleCloudDatapipelinesV1FieldValue::class;
  protected $keyDataType = '';
  protected $valueType = GoogleCloudDatapipelinesV1FieldValue::class;
  protected $valueDataType = '';

  /**
   * @param GoogleCloudDatapipelinesV1FieldValue
   */
  public function setKey(GoogleCloudDatapipelinesV1FieldValue $key)
  {
    $this->key = $key;
  }
  /**
   * @return GoogleCloudDatapipelinesV1FieldValue
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * @param GoogleCloudDatapipelinesV1FieldValue
   */
  public function setValue(GoogleCloudDatapipelinesV1FieldValue $value)
  {
    $this->value = $value;
  }
  /**
   * @return GoogleCloudDatapipelinesV1FieldValue
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatapipelinesV1MapValueEntry::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1MapValueEntry');

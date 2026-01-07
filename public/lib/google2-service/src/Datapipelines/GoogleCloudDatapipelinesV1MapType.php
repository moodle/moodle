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

class GoogleCloudDatapipelinesV1MapType extends \Google\Model
{
  protected $mapKeyTypeType = GoogleCloudDatapipelinesV1FieldType::class;
  protected $mapKeyTypeDataType = '';
  protected $mapValueTypeType = GoogleCloudDatapipelinesV1FieldType::class;
  protected $mapValueTypeDataType = '';

  /**
   * @param GoogleCloudDatapipelinesV1FieldType
   */
  public function setMapKeyType(GoogleCloudDatapipelinesV1FieldType $mapKeyType)
  {
    $this->mapKeyType = $mapKeyType;
  }
  /**
   * @return GoogleCloudDatapipelinesV1FieldType
   */
  public function getMapKeyType()
  {
    return $this->mapKeyType;
  }
  /**
   * @param GoogleCloudDatapipelinesV1FieldType
   */
  public function setMapValueType(GoogleCloudDatapipelinesV1FieldType $mapValueType)
  {
    $this->mapValueType = $mapValueType;
  }
  /**
   * @return GoogleCloudDatapipelinesV1FieldType
   */
  public function getMapValueType()
  {
    return $this->mapValueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatapipelinesV1MapType::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1MapType');

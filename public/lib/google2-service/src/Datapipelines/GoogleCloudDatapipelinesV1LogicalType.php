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

class GoogleCloudDatapipelinesV1LogicalType extends \Google\Model
{
  protected $enumerationTypeType = GoogleCloudDatapipelinesV1LogicalTypeEnumerationType::class;
  protected $enumerationTypeDataType = '';
  protected $fixedBytesType = GoogleCloudDatapipelinesV1LogicalTypeFixedBytes::class;
  protected $fixedBytesDataType = '';

  /**
   * @param GoogleCloudDatapipelinesV1LogicalTypeEnumerationType
   */
  public function setEnumerationType(GoogleCloudDatapipelinesV1LogicalTypeEnumerationType $enumerationType)
  {
    $this->enumerationType = $enumerationType;
  }
  /**
   * @return GoogleCloudDatapipelinesV1LogicalTypeEnumerationType
   */
  public function getEnumerationType()
  {
    return $this->enumerationType;
  }
  /**
   * @param GoogleCloudDatapipelinesV1LogicalTypeFixedBytes
   */
  public function setFixedBytes(GoogleCloudDatapipelinesV1LogicalTypeFixedBytes $fixedBytes)
  {
    $this->fixedBytes = $fixedBytes;
  }
  /**
   * @return GoogleCloudDatapipelinesV1LogicalTypeFixedBytes
   */
  public function getFixedBytes()
  {
    return $this->fixedBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatapipelinesV1LogicalType::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1LogicalType');

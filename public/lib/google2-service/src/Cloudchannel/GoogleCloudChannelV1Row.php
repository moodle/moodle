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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1Row extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * The key for the partition this row belongs to. This field is empty if the
   * report is not partitioned.
   *
   * @var string
   */
  public $partitionKey;
  protected $valuesType = GoogleCloudChannelV1ReportValue::class;
  protected $valuesDataType = 'array';

  /**
   * The key for the partition this row belongs to. This field is empty if the
   * report is not partitioned.
   *
   * @param string $partitionKey
   */
  public function setPartitionKey($partitionKey)
  {
    $this->partitionKey = $partitionKey;
  }
  /**
   * @return string
   */
  public function getPartitionKey()
  {
    return $this->partitionKey;
  }
  /**
   * The list of values in the row.
   *
   * @param GoogleCloudChannelV1ReportValue[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return GoogleCloudChannelV1ReportValue[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1Row::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1Row');

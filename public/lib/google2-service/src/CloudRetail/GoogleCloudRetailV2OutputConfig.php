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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2OutputConfig extends \Google\Model
{
  protected $bigqueryDestinationType = GoogleCloudRetailV2OutputConfigBigQueryDestination::class;
  protected $bigqueryDestinationDataType = '';
  protected $gcsDestinationType = GoogleCloudRetailV2OutputConfigGcsDestination::class;
  protected $gcsDestinationDataType = '';

  /**
   * The BigQuery location where the output is to be written to.
   *
   * @param GoogleCloudRetailV2OutputConfigBigQueryDestination $bigqueryDestination
   */
  public function setBigqueryDestination(GoogleCloudRetailV2OutputConfigBigQueryDestination $bigqueryDestination)
  {
    $this->bigqueryDestination = $bigqueryDestination;
  }
  /**
   * @return GoogleCloudRetailV2OutputConfigBigQueryDestination
   */
  public function getBigqueryDestination()
  {
    return $this->bigqueryDestination;
  }
  /**
   * The Google Cloud Storage location where the output is to be written to.
   *
   * @param GoogleCloudRetailV2OutputConfigGcsDestination $gcsDestination
   */
  public function setGcsDestination(GoogleCloudRetailV2OutputConfigGcsDestination $gcsDestination)
  {
    $this->gcsDestination = $gcsDestination;
  }
  /**
   * @return GoogleCloudRetailV2OutputConfigGcsDestination
   */
  public function getGcsDestination()
  {
    return $this->gcsDestination;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2OutputConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2OutputConfig');

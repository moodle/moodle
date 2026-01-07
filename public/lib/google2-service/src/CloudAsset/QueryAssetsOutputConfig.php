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

namespace Google\Service\CloudAsset;

class QueryAssetsOutputConfig extends \Google\Model
{
  protected $bigqueryDestinationType = GoogleCloudAssetV1QueryAssetsOutputConfigBigQueryDestination::class;
  protected $bigqueryDestinationDataType = '';

  /**
   * BigQuery destination where the query results will be saved.
   *
   * @param GoogleCloudAssetV1QueryAssetsOutputConfigBigQueryDestination $bigqueryDestination
   */
  public function setBigqueryDestination(GoogleCloudAssetV1QueryAssetsOutputConfigBigQueryDestination $bigqueryDestination)
  {
    $this->bigqueryDestination = $bigqueryDestination;
  }
  /**
   * @return GoogleCloudAssetV1QueryAssetsOutputConfigBigQueryDestination
   */
  public function getBigqueryDestination()
  {
    return $this->bigqueryDestination;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryAssetsOutputConfig::class, 'Google_Service_CloudAsset_QueryAssetsOutputConfig');

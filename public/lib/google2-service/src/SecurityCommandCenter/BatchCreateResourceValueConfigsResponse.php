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

namespace Google\Service\SecurityCommandCenter;

class BatchCreateResourceValueConfigsResponse extends \Google\Collection
{
  protected $collection_key = 'resourceValueConfigs';
  protected $resourceValueConfigsType = GoogleCloudSecuritycenterV1ResourceValueConfig::class;
  protected $resourceValueConfigsDataType = 'array';

  /**
   * The resource value configs created
   *
   * @param GoogleCloudSecuritycenterV1ResourceValueConfig[] $resourceValueConfigs
   */
  public function setResourceValueConfigs($resourceValueConfigs)
  {
    $this->resourceValueConfigs = $resourceValueConfigs;
  }
  /**
   * @return GoogleCloudSecuritycenterV1ResourceValueConfig[]
   */
  public function getResourceValueConfigs()
  {
    return $this->resourceValueConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchCreateResourceValueConfigsResponse::class, 'Google_Service_SecurityCommandCenter_BatchCreateResourceValueConfigsResponse');

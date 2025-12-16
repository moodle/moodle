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

class GoogleCloudApihubV1LookupApiHubInstanceResponse extends \Google\Model
{
  protected $apiHubInstanceType = GoogleCloudApihubV1ApiHubInstance::class;
  protected $apiHubInstanceDataType = '';

  /**
   * API Hub instance for a project if it exists, empty otherwise.
   *
   * @param GoogleCloudApihubV1ApiHubInstance $apiHubInstance
   */
  public function setApiHubInstance(GoogleCloudApihubV1ApiHubInstance $apiHubInstance)
  {
    $this->apiHubInstance = $apiHubInstance;
  }
  /**
   * @return GoogleCloudApihubV1ApiHubInstance
   */
  public function getApiHubInstance()
  {
    return $this->apiHubInstance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1LookupApiHubInstanceResponse::class, 'Google_Service_APIhub_GoogleCloudApihubV1LookupApiHubInstanceResponse');

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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaAnswerQueryUnderstandingInfo extends \Google\Collection
{
  protected $collection_key = 'queryClassificationInfo';
  protected $queryClassificationInfoType = GoogleCloudDiscoveryengineV1betaAnswerQueryUnderstandingInfoQueryClassificationInfo::class;
  protected $queryClassificationInfoDataType = 'array';

  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerQueryUnderstandingInfoQueryClassificationInfo[]
   */
  public function setQueryClassificationInfo($queryClassificationInfo)
  {
    $this->queryClassificationInfo = $queryClassificationInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerQueryUnderstandingInfoQueryClassificationInfo[]
   */
  public function getQueryClassificationInfo()
  {
    return $this->queryClassificationInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaAnswerQueryUnderstandingInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaAnswerQueryUnderstandingInfo');

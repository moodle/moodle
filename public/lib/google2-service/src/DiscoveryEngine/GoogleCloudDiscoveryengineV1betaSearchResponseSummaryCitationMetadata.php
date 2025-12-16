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

class GoogleCloudDiscoveryengineV1betaSearchResponseSummaryCitationMetadata extends \Google\Collection
{
  protected $collection_key = 'citations';
  protected $citationsType = GoogleCloudDiscoveryengineV1betaSearchResponseSummaryCitation::class;
  protected $citationsDataType = 'array';

  /**
   * @param GoogleCloudDiscoveryengineV1betaSearchResponseSummaryCitation[]
   */
  public function setCitations($citations)
  {
    $this->citations = $citations;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchResponseSummaryCitation[]
   */
  public function getCitations()
  {
    return $this->citations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaSearchResponseSummaryCitationMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchResponseSummaryCitationMetadata');

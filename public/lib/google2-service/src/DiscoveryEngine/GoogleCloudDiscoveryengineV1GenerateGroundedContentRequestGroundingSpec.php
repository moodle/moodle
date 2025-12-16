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

class GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSpec extends \Google\Collection
{
  protected $collection_key = 'groundingSources';
  protected $groundingSourcesType = GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSource::class;
  protected $groundingSourcesDataType = 'array';

  /**
   * @param GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSource[]
   */
  public function setGroundingSources($groundingSources)
  {
    $this->groundingSources = $groundingSources;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSource[]
   */
  public function getGroundingSources()
  {
    return $this->groundingSources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSpec');

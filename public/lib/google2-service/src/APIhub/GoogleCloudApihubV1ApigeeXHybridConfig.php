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

class GoogleCloudApihubV1ApigeeXHybridConfig extends \Google\Model
{
  protected $environmentFilterType = GoogleCloudApihubV1EnvironmentFilter::class;
  protected $environmentFilterDataType = '';

  /**
   * Optional. The filter to apply on the resources managed by the gateway
   * plugin instance. If provided this filter applies environment specific
   * filtering.
   *
   * @param GoogleCloudApihubV1EnvironmentFilter $environmentFilter
   */
  public function setEnvironmentFilter(GoogleCloudApihubV1EnvironmentFilter $environmentFilter)
  {
    $this->environmentFilter = $environmentFilter;
  }
  /**
   * @return GoogleCloudApihubV1EnvironmentFilter
   */
  public function getEnvironmentFilter()
  {
    return $this->environmentFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1ApigeeXHybridConfig::class, 'Google_Service_APIhub_GoogleCloudApihubV1ApigeeXHybridConfig');

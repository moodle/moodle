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

class ListEffectiveSecurityHealthAnalyticsCustomModulesResponse extends \Google\Collection
{
  protected $collection_key = 'effectiveSecurityHealthAnalyticsCustomModules';
  protected $effectiveSecurityHealthAnalyticsCustomModulesType = GoogleCloudSecuritycenterV1EffectiveSecurityHealthAnalyticsCustomModule::class;
  protected $effectiveSecurityHealthAnalyticsCustomModulesDataType = 'array';
  /**
   * If not empty, indicates that there may be more effective custom modules to
   * be returned.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Effective custom modules belonging to the requested parent.
   *
   * @param GoogleCloudSecuritycenterV1EffectiveSecurityHealthAnalyticsCustomModule[] $effectiveSecurityHealthAnalyticsCustomModules
   */
  public function setEffectiveSecurityHealthAnalyticsCustomModules($effectiveSecurityHealthAnalyticsCustomModules)
  {
    $this->effectiveSecurityHealthAnalyticsCustomModules = $effectiveSecurityHealthAnalyticsCustomModules;
  }
  /**
   * @return GoogleCloudSecuritycenterV1EffectiveSecurityHealthAnalyticsCustomModule[]
   */
  public function getEffectiveSecurityHealthAnalyticsCustomModules()
  {
    return $this->effectiveSecurityHealthAnalyticsCustomModules;
  }
  /**
   * If not empty, indicates that there may be more effective custom modules to
   * be returned.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListEffectiveSecurityHealthAnalyticsCustomModulesResponse::class, 'Google_Service_SecurityCommandCenter_ListEffectiveSecurityHealthAnalyticsCustomModulesResponse');

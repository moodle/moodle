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

class ListDescendantSecurityHealthAnalyticsCustomModulesResponse extends \Google\Collection
{
  protected $collection_key = 'securityHealthAnalyticsCustomModules';
  /**
   * If not empty, indicates that there may be more custom modules to be
   * returned.
   *
   * @var string
   */
  public $nextPageToken;
  protected $securityHealthAnalyticsCustomModulesType = GoogleCloudSecuritycenterV1SecurityHealthAnalyticsCustomModule::class;
  protected $securityHealthAnalyticsCustomModulesDataType = 'array';

  /**
   * If not empty, indicates that there may be more custom modules to be
   * returned.
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
  /**
   * Custom modules belonging to the requested parent and its descendants.
   *
   * @param GoogleCloudSecuritycenterV1SecurityHealthAnalyticsCustomModule[] $securityHealthAnalyticsCustomModules
   */
  public function setSecurityHealthAnalyticsCustomModules($securityHealthAnalyticsCustomModules)
  {
    $this->securityHealthAnalyticsCustomModules = $securityHealthAnalyticsCustomModules;
  }
  /**
   * @return GoogleCloudSecuritycenterV1SecurityHealthAnalyticsCustomModule[]
   */
  public function getSecurityHealthAnalyticsCustomModules()
  {
    return $this->securityHealthAnalyticsCustomModules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListDescendantSecurityHealthAnalyticsCustomModulesResponse::class, 'Google_Service_SecurityCommandCenter_ListDescendantSecurityHealthAnalyticsCustomModulesResponse');

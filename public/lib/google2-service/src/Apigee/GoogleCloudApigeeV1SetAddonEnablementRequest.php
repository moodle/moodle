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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SetAddonEnablementRequest extends \Google\Model
{
  /**
   * If the Analytics should be enabled in the environment.
   *
   * @var bool
   */
  public $analyticsEnabled;
  /**
   * If the API Security should be enabled in the environment.
   *
   * @var bool
   */
  public $apiSecurityEnabled;

  /**
   * If the Analytics should be enabled in the environment.
   *
   * @param bool $analyticsEnabled
   */
  public function setAnalyticsEnabled($analyticsEnabled)
  {
    $this->analyticsEnabled = $analyticsEnabled;
  }
  /**
   * @return bool
   */
  public function getAnalyticsEnabled()
  {
    return $this->analyticsEnabled;
  }
  /**
   * If the API Security should be enabled in the environment.
   *
   * @param bool $apiSecurityEnabled
   */
  public function setApiSecurityEnabled($apiSecurityEnabled)
  {
    $this->apiSecurityEnabled = $apiSecurityEnabled;
  }
  /**
   * @return bool
   */
  public function getApiSecurityEnabled()
  {
    return $this->apiSecurityEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SetAddonEnablementRequest::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SetAddonEnablementRequest');

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

class GoogleCloudApigeeV1ListAppGroupAppsResponse extends \Google\Collection
{
  protected $collection_key = 'appGroupApps';
  protected $appGroupAppsType = GoogleCloudApigeeV1AppGroupApp::class;
  protected $appGroupAppsDataType = 'array';
  /**
   * Token that can be sent as `next_page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * List of AppGroup apps and their credentials.
   *
   * @param GoogleCloudApigeeV1AppGroupApp[] $appGroupApps
   */
  public function setAppGroupApps($appGroupApps)
  {
    $this->appGroupApps = $appGroupApps;
  }
  /**
   * @return GoogleCloudApigeeV1AppGroupApp[]
   */
  public function getAppGroupApps()
  {
    return $this->appGroupApps;
  }
  /**
   * Token that can be sent as `next_page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages.
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
class_alias(GoogleCloudApigeeV1ListAppGroupAppsResponse::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ListAppGroupAppsResponse');

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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1ListTelemetryUsersResponse extends \Google\Collection
{
  protected $collection_key = 'telemetryUsers';
  /**
   * Token to specify next page in the list.
   *
   * @var string
   */
  public $nextPageToken;
  protected $telemetryUsersType = GoogleChromeManagementV1TelemetryUser::class;
  protected $telemetryUsersDataType = 'array';

  /**
   * Token to specify next page in the list.
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
   * Telemetry users returned in the response.
   *
   * @param GoogleChromeManagementV1TelemetryUser[] $telemetryUsers
   */
  public function setTelemetryUsers($telemetryUsers)
  {
    $this->telemetryUsers = $telemetryUsers;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryUser[]
   */
  public function getTelemetryUsers()
  {
    return $this->telemetryUsers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1ListTelemetryUsersResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1ListTelemetryUsersResponse');

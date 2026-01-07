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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1PlatformDataSource extends \Google\Model
{
  /**
   * Default value. Don't use.
   */
  public const COMMON_DATA_SOURCE_UNKNOWN = 'UNKNOWN';
  /**
   * Google Workspace users. The user can only view and select users from their
   * Google Workspace organization.
   */
  public const COMMON_DATA_SOURCE_USER = 'USER';
  /**
   * A data source shared by all Google Workspace applications, such as users in
   * a Google Workspace organization.
   *
   * @var string
   */
  public $commonDataSource;
  protected $hostAppDataSourceType = HostAppDataSourceMarkup::class;
  protected $hostAppDataSourceDataType = '';

  /**
   * A data source shared by all Google Workspace applications, such as users in
   * a Google Workspace organization.
   *
   * Accepted values: UNKNOWN, USER
   *
   * @param self::COMMON_DATA_SOURCE_* $commonDataSource
   */
  public function setCommonDataSource($commonDataSource)
  {
    $this->commonDataSource = $commonDataSource;
  }
  /**
   * @return self::COMMON_DATA_SOURCE_*
   */
  public function getCommonDataSource()
  {
    return $this->commonDataSource;
  }
  /**
   * A data source that's unique to a Google Workspace host application, such
   * spaces in Google Chat. This field supports the Google API Client Libraries
   * but isn't available in the Cloud Client Libraries. To learn more, see
   * [Install the client
   * libraries](https://developers.google.com/workspace/chat/libraries).
   *
   * @param HostAppDataSourceMarkup $hostAppDataSource
   */
  public function setHostAppDataSource(HostAppDataSourceMarkup $hostAppDataSource)
  {
    $this->hostAppDataSource = $hostAppDataSource;
  }
  /**
   * @return HostAppDataSourceMarkup
   */
  public function getHostAppDataSource()
  {
    return $this->hostAppDataSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1PlatformDataSource::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1PlatformDataSource');

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

namespace Google\Service\Advisorynotifications;

class GoogleCloudAdvisorynotificationsV1Settings extends \Google\Model
{
  /**
   * Required. Fingerprint for optimistic concurrency returned in Get requests.
   * Must be provided for Update requests. If the value provided does not match
   * the value known to the server, ABORTED will be thrown, and the client
   * should retry the read-modify-write cycle.
   *
   * @var string
   */
  public $etag;
  /**
   * Identifier. The resource name of the settings to retrieve. Format:
   * organizations/{organization}/locations/{location}/settings or
   * projects/{projects}/locations/{location}/settings.
   *
   * @var string
   */
  public $name;
  protected $notificationSettingsType = GoogleCloudAdvisorynotificationsV1NotificationSettings::class;
  protected $notificationSettingsDataType = 'map';

  /**
   * Required. Fingerprint for optimistic concurrency returned in Get requests.
   * Must be provided for Update requests. If the value provided does not match
   * the value known to the server, ABORTED will be thrown, and the client
   * should retry the read-modify-write cycle.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Identifier. The resource name of the settings to retrieve. Format:
   * organizations/{organization}/locations/{location}/settings or
   * projects/{projects}/locations/{location}/settings.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. Map of each notification type and its settings to get/set all
   * settings at once. The server will validate the value for each notification
   * type.
   *
   * @param GoogleCloudAdvisorynotificationsV1NotificationSettings[] $notificationSettings
   */
  public function setNotificationSettings($notificationSettings)
  {
    $this->notificationSettings = $notificationSettings;
  }
  /**
   * @return GoogleCloudAdvisorynotificationsV1NotificationSettings[]
   */
  public function getNotificationSettings()
  {
    return $this->notificationSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAdvisorynotificationsV1Settings::class, 'Google_Service_Advisorynotifications_GoogleCloudAdvisorynotificationsV1Settings');

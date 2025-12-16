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

namespace Google\Service\DisplayVideo;

class GoogleAudience extends \Google\Model
{
  /**
   * Default value when type is not specified or is unknown.
   */
  public const GOOGLE_AUDIENCE_TYPE_GOOGLE_AUDIENCE_TYPE_UNSPECIFIED = 'GOOGLE_AUDIENCE_TYPE_UNSPECIFIED';
  /**
   * Affinity type Google audience.
   */
  public const GOOGLE_AUDIENCE_TYPE_GOOGLE_AUDIENCE_TYPE_AFFINITY = 'GOOGLE_AUDIENCE_TYPE_AFFINITY';
  /**
   * In-Market type Google audience.
   */
  public const GOOGLE_AUDIENCE_TYPE_GOOGLE_AUDIENCE_TYPE_IN_MARKET = 'GOOGLE_AUDIENCE_TYPE_IN_MARKET';
  /**
   * Installed-Apps type Google audience.
   *
   * @deprecated
   */
  public const GOOGLE_AUDIENCE_TYPE_GOOGLE_AUDIENCE_TYPE_INSTALLED_APPS = 'GOOGLE_AUDIENCE_TYPE_INSTALLED_APPS';
  /**
   * New-Mobile-Devices type Google audience.
   *
   * @deprecated
   */
  public const GOOGLE_AUDIENCE_TYPE_GOOGLE_AUDIENCE_TYPE_NEW_MOBILE_DEVICES = 'GOOGLE_AUDIENCE_TYPE_NEW_MOBILE_DEVICES';
  /**
   * Life-Event type Google audience.
   */
  public const GOOGLE_AUDIENCE_TYPE_GOOGLE_AUDIENCE_TYPE_LIFE_EVENT = 'GOOGLE_AUDIENCE_TYPE_LIFE_EVENT';
  /**
   * Extended-Demographic type Google audience.
   */
  public const GOOGLE_AUDIENCE_TYPE_GOOGLE_AUDIENCE_TYPE_EXTENDED_DEMOGRAPHIC = 'GOOGLE_AUDIENCE_TYPE_EXTENDED_DEMOGRAPHIC';
  /**
   * Output only. The display name of the Google audience. .
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The unique ID of the Google audience. Assigned by the system.
   *
   * @var string
   */
  public $googleAudienceId;
  /**
   * Output only. The type of Google audience. .
   *
   * @var string
   */
  public $googleAudienceType;
  /**
   * Output only. The resource name of the google audience.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The display name of the Google audience. .
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The unique ID of the Google audience. Assigned by the system.
   *
   * @param string $googleAudienceId
   */
  public function setGoogleAudienceId($googleAudienceId)
  {
    $this->googleAudienceId = $googleAudienceId;
  }
  /**
   * @return string
   */
  public function getGoogleAudienceId()
  {
    return $this->googleAudienceId;
  }
  /**
   * Output only. The type of Google audience. .
   *
   * Accepted values: GOOGLE_AUDIENCE_TYPE_UNSPECIFIED,
   * GOOGLE_AUDIENCE_TYPE_AFFINITY, GOOGLE_AUDIENCE_TYPE_IN_MARKET,
   * GOOGLE_AUDIENCE_TYPE_INSTALLED_APPS,
   * GOOGLE_AUDIENCE_TYPE_NEW_MOBILE_DEVICES, GOOGLE_AUDIENCE_TYPE_LIFE_EVENT,
   * GOOGLE_AUDIENCE_TYPE_EXTENDED_DEMOGRAPHIC
   *
   * @param self::GOOGLE_AUDIENCE_TYPE_* $googleAudienceType
   */
  public function setGoogleAudienceType($googleAudienceType)
  {
    $this->googleAudienceType = $googleAudienceType;
  }
  /**
   * @return self::GOOGLE_AUDIENCE_TYPE_*
   */
  public function getGoogleAudienceType()
  {
    return $this->googleAudienceType;
  }
  /**
   * Output only. The resource name of the google audience.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAudience::class, 'Google_Service_DisplayVideo_GoogleAudience');

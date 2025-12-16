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

namespace Google\Service\AndroidEnterprise;

class AppVersion extends \Google\Collection
{
  public const TRACK_appTrackUnspecified = 'appTrackUnspecified';
  public const TRACK_production = 'production';
  public const TRACK_beta = 'beta';
  public const TRACK_alpha = 'alpha';
  protected $collection_key = 'trackId';
  /**
   * True if this version is a production APK.
   *
   * @var bool
   */
  public $isProduction;
  /**
   * The SDK version this app targets, as specified in the manifest of the APK.
   * See http://developer.android.com/guide/topics/manifest/uses-sdk-
   * element.html
   *
   * @var int
   */
  public $targetSdkVersion;
  /**
   * Deprecated, use trackId instead.
   *
   * @var string
   */
  public $track;
  /**
   * Track ids that the app version is published in. Replaces the track field
   * (deprecated), but doesn't include the production track (see isProduction
   * instead).
   *
   * @var string[]
   */
  public $trackId;
  /**
   * Unique increasing identifier for the app version.
   *
   * @var int
   */
  public $versionCode;
  /**
   * The string used in the Play store by the app developer to identify the
   * version. The string is not necessarily unique or localized (for example,
   * the string could be "1.4").
   *
   * @var string
   */
  public $versionString;

  /**
   * True if this version is a production APK.
   *
   * @param bool $isProduction
   */
  public function setIsProduction($isProduction)
  {
    $this->isProduction = $isProduction;
  }
  /**
   * @return bool
   */
  public function getIsProduction()
  {
    return $this->isProduction;
  }
  /**
   * The SDK version this app targets, as specified in the manifest of the APK.
   * See http://developer.android.com/guide/topics/manifest/uses-sdk-
   * element.html
   *
   * @param int $targetSdkVersion
   */
  public function setTargetSdkVersion($targetSdkVersion)
  {
    $this->targetSdkVersion = $targetSdkVersion;
  }
  /**
   * @return int
   */
  public function getTargetSdkVersion()
  {
    return $this->targetSdkVersion;
  }
  /**
   * Deprecated, use trackId instead.
   *
   * Accepted values: appTrackUnspecified, production, beta, alpha
   *
   * @param self::TRACK_* $track
   */
  public function setTrack($track)
  {
    $this->track = $track;
  }
  /**
   * @return self::TRACK_*
   */
  public function getTrack()
  {
    return $this->track;
  }
  /**
   * Track ids that the app version is published in. Replaces the track field
   * (deprecated), but doesn't include the production track (see isProduction
   * instead).
   *
   * @param string[] $trackId
   */
  public function setTrackId($trackId)
  {
    $this->trackId = $trackId;
  }
  /**
   * @return string[]
   */
  public function getTrackId()
  {
    return $this->trackId;
  }
  /**
   * Unique increasing identifier for the app version.
   *
   * @param int $versionCode
   */
  public function setVersionCode($versionCode)
  {
    $this->versionCode = $versionCode;
  }
  /**
   * @return int
   */
  public function getVersionCode()
  {
    return $this->versionCode;
  }
  /**
   * The string used in the Play store by the app developer to identify the
   * version. The string is not necessarily unique or localized (for example,
   * the string could be "1.4").
   *
   * @param string $versionString
   */
  public function setVersionString($versionString)
  {
    $this->versionString = $versionString;
  }
  /**
   * @return string
   */
  public function getVersionString()
  {
    return $this->versionString;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppVersion::class, 'Google_Service_AndroidEnterprise_AppVersion');

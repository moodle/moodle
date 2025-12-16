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

namespace Google\Service\AndroidManagement;

class ApplicationReport extends \Google\Collection
{
  /**
   * The app was sideloaded from an unspecified source.
   */
  public const APPLICATION_SOURCE_APPLICATION_SOURCE_UNSPECIFIED = 'APPLICATION_SOURCE_UNSPECIFIED';
  /**
   * This is a system app from the device's factory image.
   */
  public const APPLICATION_SOURCE_SYSTEM_APP_FACTORY_VERSION = 'SYSTEM_APP_FACTORY_VERSION';
  /**
   * This is an updated system app.
   */
  public const APPLICATION_SOURCE_SYSTEM_APP_UPDATED_VERSION = 'SYSTEM_APP_UPDATED_VERSION';
  /**
   * The app was installed from the Google Play Store.
   */
  public const APPLICATION_SOURCE_INSTALLED_FROM_PLAY_STORE = 'INSTALLED_FROM_PLAY_STORE';
  /**
   * The app was installed using the AMAPI SDK command
   * (https://developers.google.com/android/management/extensibility-sdk-
   * integration). See also: CUSTOM
   */
  public const APPLICATION_SOURCE_CUSTOM = 'CUSTOM';
  /**
   * App state is unspecified
   */
  public const STATE_APPLICATION_STATE_UNSPECIFIED = 'APPLICATION_STATE_UNSPECIFIED';
  /**
   * App was removed from the device
   */
  public const STATE_REMOVED = 'REMOVED';
  /**
   * App is installed on the device
   */
  public const STATE_INSTALLED = 'INSTALLED';
  /**
   * App user facing type is unspecified.
   */
  public const USER_FACING_TYPE_USER_FACING_TYPE_UNSPECIFIED = 'USER_FACING_TYPE_UNSPECIFIED';
  /**
   * App is not user facing.
   */
  public const USER_FACING_TYPE_NOT_USER_FACING = 'NOT_USER_FACING';
  /**
   * App is user facing.
   */
  public const USER_FACING_TYPE_USER_FACING = 'USER_FACING';
  protected $collection_key = 'signingKeyCertFingerprints';
  /**
   * The source of the package.
   *
   * @var string
   */
  public $applicationSource;
  /**
   * The display name of the app.
   *
   * @var string
   */
  public $displayName;
  protected $eventsType = ApplicationEvent::class;
  protected $eventsDataType = 'array';
  /**
   * The package name of the app that installed this app.
   *
   * @var string
   */
  public $installerPackageName;
  protected $keyedAppStatesType = KeyedAppState::class;
  protected $keyedAppStatesDataType = 'array';
  /**
   * Package name of the app.
   *
   * @var string
   */
  public $packageName;
  /**
   * The SHA-256 hash of the app's APK file, which can be used to verify the app
   * hasn't been modified. Each byte of the hash value is represented as a two-
   * digit hexadecimal number.
   *
   * @var string
   */
  public $packageSha256Hash;
  /**
   * The SHA-1 hash of each android.content.pm.Signature
   * (https://developer.android.com/reference/android/content/pm/Signature.html)
   * associated with the app package. Each byte of each hash value is
   * represented as a two-digit hexadecimal number.
   *
   * @var string[]
   */
  public $signingKeyCertFingerprints;
  /**
   * Application state.
   *
   * @var string
   */
  public $state;
  /**
   * Whether the app is user facing.
   *
   * @var string
   */
  public $userFacingType;
  /**
   * The app version code, which can be used to determine whether one version is
   * more recent than another.
   *
   * @var int
   */
  public $versionCode;
  /**
   * The app version as displayed to the user.
   *
   * @var string
   */
  public $versionName;

  /**
   * The source of the package.
   *
   * Accepted values: APPLICATION_SOURCE_UNSPECIFIED,
   * SYSTEM_APP_FACTORY_VERSION, SYSTEM_APP_UPDATED_VERSION,
   * INSTALLED_FROM_PLAY_STORE, CUSTOM
   *
   * @param self::APPLICATION_SOURCE_* $applicationSource
   */
  public function setApplicationSource($applicationSource)
  {
    $this->applicationSource = $applicationSource;
  }
  /**
   * @return self::APPLICATION_SOURCE_*
   */
  public function getApplicationSource()
  {
    return $this->applicationSource;
  }
  /**
   * The display name of the app.
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
   * The list of app events which have occurred in the last 30 hours.
   *
   * @param ApplicationEvent[] $events
   */
  public function setEvents($events)
  {
    $this->events = $events;
  }
  /**
   * @return ApplicationEvent[]
   */
  public function getEvents()
  {
    return $this->events;
  }
  /**
   * The package name of the app that installed this app.
   *
   * @param string $installerPackageName
   */
  public function setInstallerPackageName($installerPackageName)
  {
    $this->installerPackageName = $installerPackageName;
  }
  /**
   * @return string
   */
  public function getInstallerPackageName()
  {
    return $this->installerPackageName;
  }
  /**
   * List of keyed app states reported by the app.
   *
   * @param KeyedAppState[] $keyedAppStates
   */
  public function setKeyedAppStates($keyedAppStates)
  {
    $this->keyedAppStates = $keyedAppStates;
  }
  /**
   * @return KeyedAppState[]
   */
  public function getKeyedAppStates()
  {
    return $this->keyedAppStates;
  }
  /**
   * Package name of the app.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * The SHA-256 hash of the app's APK file, which can be used to verify the app
   * hasn't been modified. Each byte of the hash value is represented as a two-
   * digit hexadecimal number.
   *
   * @param string $packageSha256Hash
   */
  public function setPackageSha256Hash($packageSha256Hash)
  {
    $this->packageSha256Hash = $packageSha256Hash;
  }
  /**
   * @return string
   */
  public function getPackageSha256Hash()
  {
    return $this->packageSha256Hash;
  }
  /**
   * The SHA-1 hash of each android.content.pm.Signature
   * (https://developer.android.com/reference/android/content/pm/Signature.html)
   * associated with the app package. Each byte of each hash value is
   * represented as a two-digit hexadecimal number.
   *
   * @param string[] $signingKeyCertFingerprints
   */
  public function setSigningKeyCertFingerprints($signingKeyCertFingerprints)
  {
    $this->signingKeyCertFingerprints = $signingKeyCertFingerprints;
  }
  /**
   * @return string[]
   */
  public function getSigningKeyCertFingerprints()
  {
    return $this->signingKeyCertFingerprints;
  }
  /**
   * Application state.
   *
   * Accepted values: APPLICATION_STATE_UNSPECIFIED, REMOVED, INSTALLED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Whether the app is user facing.
   *
   * Accepted values: USER_FACING_TYPE_UNSPECIFIED, NOT_USER_FACING, USER_FACING
   *
   * @param self::USER_FACING_TYPE_* $userFacingType
   */
  public function setUserFacingType($userFacingType)
  {
    $this->userFacingType = $userFacingType;
  }
  /**
   * @return self::USER_FACING_TYPE_*
   */
  public function getUserFacingType()
  {
    return $this->userFacingType;
  }
  /**
   * The app version code, which can be used to determine whether one version is
   * more recent than another.
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
   * The app version as displayed to the user.
   *
   * @param string $versionName
   */
  public function setVersionName($versionName)
  {
    $this->versionName = $versionName;
  }
  /**
   * @return string
   */
  public function getVersionName()
  {
    return $this->versionName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplicationReport::class, 'Google_Service_AndroidManagement_ApplicationReport');

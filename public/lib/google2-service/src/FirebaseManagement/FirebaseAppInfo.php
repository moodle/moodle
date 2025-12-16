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

namespace Google\Service\FirebaseManagement;

class FirebaseAppInfo extends \Google\Model
{
  /**
   * Unknown state. This is only used for distinguishing unset values.
   */
  public const PLATFORM_PLATFORM_UNSPECIFIED = 'PLATFORM_UNSPECIFIED';
  /**
   * The Firebase App is associated with iOS.
   */
  public const PLATFORM_IOS = 'IOS';
  /**
   * The Firebase App is associated with Android.
   */
  public const PLATFORM_ANDROID = 'ANDROID';
  /**
   * The Firebase App is associated with web.
   */
  public const PLATFORM_WEB = 'WEB';
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The App is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The App has been soft-deleted. After an App has been in the `DELETED` state
   * for more than 30 days, it is considered expired and will be permanently
   * deleted. Up until this time, you can restore the App by calling `Undelete`
   * ([Android](projects.androidApps/undelete) |
   * [iOS](projects.iosApps/undelete) | [web](projects.webApps/undelete)).
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * The globally unique, Google-assigned identifier (UID) for the Firebase API
   * key associated with the App. Be aware that this value is the UID of the API
   * key, _not_ the [`keyString`](https://cloud.google.com/api-
   * keys/docs/reference/rest/v2/projects.locations.keys#Key.FIELDS.key_string)
   * of the API key. The `keyString` is the value that can be found in the App's
   * configuration artifact
   * ([`AndroidApp`](../../rest/v1beta1/projects.androidApps/getConfig) |
   * [`IosApp`](../../rest/v1beta1/projects.iosApps/getConfig) |
   * [`WebApp`](../../rest/v1beta1/projects.webApps/getConfig)). If `api_key_id`
   * is not set in requests to create the App
   * ([`AndroidApp`](../../rest/v1beta1/projects.androidApps/create) |
   * [`IosApp`](../../rest/v1beta1/projects.iosApps/create) |
   * [`WebApp`](../../rest/v1beta1/projects.webApps/create)), then Firebase
   * automatically associates an `api_key_id` with the App. This auto-associated
   * key may be an existing valid key or, if no valid key exists, a new one will
   * be provisioned.
   *
   * @var string
   */
  public $apiKeyId;
  /**
   * Output only. Immutable. The globally unique, Firebase-assigned identifier
   * for the `WebApp`. This identifier should be treated as an opaque token, as
   * the data format is not specified.
   *
   * @var string
   */
  public $appId;
  /**
   * The user-assigned display name of the Firebase App.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. If the App has been removed from the Project, this is the
   * timestamp of when the App is considered expired and will be permanently
   * deleted. After this time, the App cannot be undeleted (that is, restored to
   * the Project). This value is only provided if the App is in the `DELETED`
   * state.
   *
   * @var string
   */
  public $expireTime;
  /**
   * The resource name of the Firebase App, in the format: projects/PROJECT_ID
   * /iosApps/APP_ID or projects/PROJECT_ID/androidApps/APP_ID or projects/
   * PROJECT_ID/webApps/APP_ID
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Immutable. The platform-specific identifier of the App.
   * *Note:* For most use cases, use `appId`, which is the canonical, globally
   * unique identifier for referencing an App. This string is derived from a
   * native identifier for each platform: `packageName` for an `AndroidApp`,
   * `bundleId` for an `IosApp`, and `webId` for a `WebApp`. Its contents should
   * be treated as opaque, as the native identifier format may change as
   * platforms evolve. This string is only unique within a `FirebaseProject` and
   * its associated Apps.
   *
   * @var string
   */
  public $namespace;
  /**
   * The platform of the Firebase App.
   *
   * @var string
   */
  public $platform;
  /**
   * Output only. The lifecycle state of the App.
   *
   * @var string
   */
  public $state;

  /**
   * The globally unique, Google-assigned identifier (UID) for the Firebase API
   * key associated with the App. Be aware that this value is the UID of the API
   * key, _not_ the [`keyString`](https://cloud.google.com/api-
   * keys/docs/reference/rest/v2/projects.locations.keys#Key.FIELDS.key_string)
   * of the API key. The `keyString` is the value that can be found in the App's
   * configuration artifact
   * ([`AndroidApp`](../../rest/v1beta1/projects.androidApps/getConfig) |
   * [`IosApp`](../../rest/v1beta1/projects.iosApps/getConfig) |
   * [`WebApp`](../../rest/v1beta1/projects.webApps/getConfig)). If `api_key_id`
   * is not set in requests to create the App
   * ([`AndroidApp`](../../rest/v1beta1/projects.androidApps/create) |
   * [`IosApp`](../../rest/v1beta1/projects.iosApps/create) |
   * [`WebApp`](../../rest/v1beta1/projects.webApps/create)), then Firebase
   * automatically associates an `api_key_id` with the App. This auto-associated
   * key may be an existing valid key or, if no valid key exists, a new one will
   * be provisioned.
   *
   * @param string $apiKeyId
   */
  public function setApiKeyId($apiKeyId)
  {
    $this->apiKeyId = $apiKeyId;
  }
  /**
   * @return string
   */
  public function getApiKeyId()
  {
    return $this->apiKeyId;
  }
  /**
   * Output only. Immutable. The globally unique, Firebase-assigned identifier
   * for the `WebApp`. This identifier should be treated as an opaque token, as
   * the data format is not specified.
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * The user-assigned display name of the Firebase App.
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
   * Output only. If the App has been removed from the Project, this is the
   * timestamp of when the App is considered expired and will be permanently
   * deleted. After this time, the App cannot be undeleted (that is, restored to
   * the Project). This value is only provided if the App is in the `DELETED`
   * state.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * The resource name of the Firebase App, in the format: projects/PROJECT_ID
   * /iosApps/APP_ID or projects/PROJECT_ID/androidApps/APP_ID or projects/
   * PROJECT_ID/webApps/APP_ID
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
   * Output only. Immutable. The platform-specific identifier of the App.
   * *Note:* For most use cases, use `appId`, which is the canonical, globally
   * unique identifier for referencing an App. This string is derived from a
   * native identifier for each platform: `packageName` for an `AndroidApp`,
   * `bundleId` for an `IosApp`, and `webId` for a `WebApp`. Its contents should
   * be treated as opaque, as the native identifier format may change as
   * platforms evolve. This string is only unique within a `FirebaseProject` and
   * its associated Apps.
   *
   * @param string $namespace
   */
  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;
  }
  /**
   * @return string
   */
  public function getNamespace()
  {
    return $this->namespace;
  }
  /**
   * The platform of the Firebase App.
   *
   * Accepted values: PLATFORM_UNSPECIFIED, IOS, ANDROID, WEB
   *
   * @param self::PLATFORM_* $platform
   */
  public function setPlatform($platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return self::PLATFORM_*
   */
  public function getPlatform()
  {
    return $this->platform;
  }
  /**
   * Output only. The lifecycle state of the App.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, DELETED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirebaseAppInfo::class, 'Google_Service_FirebaseManagement_FirebaseAppInfo');

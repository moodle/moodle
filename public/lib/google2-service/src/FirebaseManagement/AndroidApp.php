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

class AndroidApp extends \Google\Collection
{
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
  protected $collection_key = 'sha256Hashes';
  /**
   * The globally unique, Google-assigned identifier (UID) for the Firebase API
   * key associated with the `AndroidApp`. Be aware that this value is the UID
   * of the API key, _not_ the [`keyString`](https://cloud.google.com/api-
   * keys/docs/reference/rest/v2/projects.locations.keys#Key.FIELDS.key_string)
   * of the API key. The `keyString` is the value that can be found in the App's
   * [configuration
   * artifact](../../rest/v1beta1/projects.androidApps/getConfig). If
   * `api_key_id` is not set in requests to
   * [`androidApps.Create`](../../rest/v1beta1/projects.androidApps/create),
   * then Firebase automatically associates an `api_key_id` with the
   * `AndroidApp`. This auto-associated key may be an existing valid key or, if
   * no valid key exists, a new one will be provisioned. In patch requests,
   * `api_key_id` cannot be set to an empty value, and the new UID must have no
   * restrictions or only have restrictions that are valid for the associated
   * `AndroidApp`. We recommend using the [Google Cloud
   * Console](https://console.cloud.google.com/apis/credentials) to manage API
   * keys.
   *
   * @var string
   */
  public $apiKeyId;
  /**
   * Output only. Immutable. The globally unique, Firebase-assigned identifier
   * for the `AndroidApp`. This identifier should be treated as an opaque token,
   * as the data format is not specified.
   *
   * @var string
   */
  public $appId;
  /**
   * The user-assigned display name for the `AndroidApp`.
   *
   * @var string
   */
  public $displayName;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and it may be sent with update requests to ensure the client has an up-to-
   * date value before proceeding. Learn more about `etag` in Google's [AIP-154
   * standard](https://google.aip.dev/154#declarative-friendly-resources). This
   * etag is strongly validated.
   *
   * @var string
   */
  public $etag;
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
   * The resource name of the AndroidApp, in the format: projects/
   * PROJECT_IDENTIFIER/androidApps/APP_ID * PROJECT_IDENTIFIER: the parent
   * Project's
   * [`ProjectNumber`](../projects#FirebaseProject.FIELDS.project_number)
   * ***(recommended)*** or its
   * [`ProjectId`](../projects#FirebaseProject.FIELDS.project_id). Learn more
   * about using project identifiers in Google's [AIP 2510
   * standard](https://google.aip.dev/cloud/2510). Note that the value for
   * PROJECT_IDENTIFIER in any response body will be the `ProjectId`. * APP_ID:
   * the globally unique, Firebase-assigned identifier for the App (see
   * [`appId`](../projects.androidApps#AndroidApp.FIELDS.app_id)).
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The canonical package name of the Android app as would appear in
   * the Google Play Developer Console.
   *
   * @var string
   */
  public $packageName;
  /**
   * Output only. Immutable. A user-assigned unique identifier of the parent
   * FirebaseProject for the `AndroidApp`.
   *
   * @var string
   */
  public $projectId;
  /**
   * The SHA1 certificate hashes for the AndroidApp.
   *
   * @var string[]
   */
  public $sha1Hashes;
  /**
   * The SHA256 certificate hashes for the AndroidApp.
   *
   * @var string[]
   */
  public $sha256Hashes;
  /**
   * Output only. The lifecycle state of the App.
   *
   * @var string
   */
  public $state;

  /**
   * The globally unique, Google-assigned identifier (UID) for the Firebase API
   * key associated with the `AndroidApp`. Be aware that this value is the UID
   * of the API key, _not_ the [`keyString`](https://cloud.google.com/api-
   * keys/docs/reference/rest/v2/projects.locations.keys#Key.FIELDS.key_string)
   * of the API key. The `keyString` is the value that can be found in the App's
   * [configuration
   * artifact](../../rest/v1beta1/projects.androidApps/getConfig). If
   * `api_key_id` is not set in requests to
   * [`androidApps.Create`](../../rest/v1beta1/projects.androidApps/create),
   * then Firebase automatically associates an `api_key_id` with the
   * `AndroidApp`. This auto-associated key may be an existing valid key or, if
   * no valid key exists, a new one will be provisioned. In patch requests,
   * `api_key_id` cannot be set to an empty value, and the new UID must have no
   * restrictions or only have restrictions that are valid for the associated
   * `AndroidApp`. We recommend using the [Google Cloud
   * Console](https://console.cloud.google.com/apis/credentials) to manage API
   * keys.
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
   * for the `AndroidApp`. This identifier should be treated as an opaque token,
   * as the data format is not specified.
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
   * The user-assigned display name for the `AndroidApp`.
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
   * This checksum is computed by the server based on the value of other fields,
   * and it may be sent with update requests to ensure the client has an up-to-
   * date value before proceeding. Learn more about `etag` in Google's [AIP-154
   * standard](https://google.aip.dev/154#declarative-friendly-resources). This
   * etag is strongly validated.
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
   * The resource name of the AndroidApp, in the format: projects/
   * PROJECT_IDENTIFIER/androidApps/APP_ID * PROJECT_IDENTIFIER: the parent
   * Project's
   * [`ProjectNumber`](../projects#FirebaseProject.FIELDS.project_number)
   * ***(recommended)*** or its
   * [`ProjectId`](../projects#FirebaseProject.FIELDS.project_id). Learn more
   * about using project identifiers in Google's [AIP 2510
   * standard](https://google.aip.dev/cloud/2510). Note that the value for
   * PROJECT_IDENTIFIER in any response body will be the `ProjectId`. * APP_ID:
   * the globally unique, Firebase-assigned identifier for the App (see
   * [`appId`](../projects.androidApps#AndroidApp.FIELDS.app_id)).
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
   * Immutable. The canonical package name of the Android app as would appear in
   * the Google Play Developer Console.
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
   * Output only. Immutable. A user-assigned unique identifier of the parent
   * FirebaseProject for the `AndroidApp`.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * The SHA1 certificate hashes for the AndroidApp.
   *
   * @param string[] $sha1Hashes
   */
  public function setSha1Hashes($sha1Hashes)
  {
    $this->sha1Hashes = $sha1Hashes;
  }
  /**
   * @return string[]
   */
  public function getSha1Hashes()
  {
    return $this->sha1Hashes;
  }
  /**
   * The SHA256 certificate hashes for the AndroidApp.
   *
   * @param string[] $sha256Hashes
   */
  public function setSha256Hashes($sha256Hashes)
  {
    $this->sha256Hashes = $sha256Hashes;
  }
  /**
   * @return string[]
   */
  public function getSha256Hashes()
  {
    return $this->sha256Hashes;
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
class_alias(AndroidApp::class, 'Google_Service_FirebaseManagement_AndroidApp');

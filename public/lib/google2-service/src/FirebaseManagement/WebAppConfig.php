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

class WebAppConfig extends \Google\Model
{
  /**
   * The [`keyString`](https://cloud.google.com/api-
   * keys/docs/reference/rest/v2/projects.locations.keys#Key.FIELDS.key_string)
   * of the API key associated with the `WebApp`. Note that this value is _not_
   * the [`apiKeyId`](../projects.webApps#WebApp.FIELDS.api_key_id) (the UID) of
   * the API key associated with the `WebApp`.
   *
   * @var string
   */
  public $apiKey;
  /**
   * Immutable. The globally unique, Firebase-assigned identifier for the
   * `WebApp`.
   *
   * @var string
   */
  public $appId;
  /**
   * The domain Firebase Auth configures for OAuth redirects, in the format:
   * PROJECT_ID.firebaseapp.com
   *
   * @var string
   */
  public $authDomain;
  /**
   * **DEPRECATED.** _Instead, find the URL of the default Realtime Database
   * instance using the [list endpoint](https://firebase.google.com/docs/referen
   * ce/rest/database/database-
   * management/rest/v1beta/projects.locations.instances/list) within the
   * Firebase Realtime Database REST API. If the default instance for the
   * Project has not yet been provisioned, the return might not contain a
   * default instance. Note that the config that's generated for the Firebase
   * console or the Firebase CLI uses the Realtime Database endpoint to populate
   * this value for that config._ The URL of the default Firebase Realtime
   * Database instance.
   *
   * @deprecated
   * @var string
   */
  public $databaseURL;
  /**
   * **DEPRECATED.** _Instead, use product-specific REST APIs to find the
   * location of each resource in a Project. This field may not be populated,
   * especially for newly provisioned projects after October 30, 2024._ The ID
   * of the Project's ["location for default Google Cloud
   * resources"](https://firebase.google.com/docs/projects/locations#default-
   * cloud-location), which are resources associated with Google App Engine. The
   * location is one of the available [App Engine
   * locations](https://cloud.google.com/about/locations#region). This field is
   * omitted if the location for default Google Cloud resources has not been
   * set.
   *
   * @deprecated
   * @var string
   */
  public $locationId;
  /**
   * The unique Google-assigned identifier of the Google Analytics web stream
   * associated with the `WebApp`. Firebase SDKs use this ID to interact with
   * Google Analytics APIs. This field is only present if the `WebApp` is linked
   * to a web stream in a Google Analytics App + Web property. Learn more about
   * this ID and Google Analytics web streams in the [Analytics
   * documentation](https://support.google.com/analytics/answer/9304153). To
   * generate a `measurementId` and link the `WebApp` with a Google Analytics
   * web stream, call
   * [`AddGoogleAnalytics`](../../v1beta1/projects/addGoogleAnalytics). For apps
   * using the Firebase JavaScript SDK v7.20.0 and later, Firebase dynamically
   * fetches the `measurementId` when your app initializes Analytics. Having
   * this ID in your config object is optional, but it does serve as a fallback
   * in the rare case that the dynamic fetch fails.
   *
   * @var string
   */
  public $measurementId;
  /**
   * The sender ID for use with Firebase Cloud Messaging.
   *
   * @var string
   */
  public $messagingSenderId;
  /**
   * Immutable. A user-assigned unique identifier for the `FirebaseProject`.
   *
   * @var string
   */
  public $projectId;
  /**
   * Output only. Immutable. The globally unique, Google-assigned canonical
   * identifier for the Project. Use this identifier when configuring
   * integrations and/or making API calls to Google Cloud or third-party
   * services.
   *
   * @var string
   */
  public $projectNumber;
  /**
   * Optional. Duplicate field for the URL of the default Realtime Database
   * instances (if the default instance has been provisioned). If the request
   * asks for the V2 config format, this field will be populated instead of
   * `realtime_database_instance_uri`.
   *
   * @var string
   */
  public $realtimeDatabaseUrl;
  /**
   * **DEPRECATED.** _Instead, find the name of the default Cloud Storage for
   * Firebase bucket using the [list endpoint](https://firebase.google.com/docs/
   * reference/rest/storage/rest/v1beta/projects.buckets/list) within the Cloud
   * Storage for Firebase REST API. If the default bucket for the Project has
   * not yet been provisioned, the return might not contain a default bucket.
   * Note that the config that's generated for the Firebase console or the
   * Firebase CLI uses the Cloud Storage for Firebase endpoint to populate this
   * value for that config._ The name of the default Cloud Storage for Firebase
   * bucket.
   *
   * @deprecated
   * @var string
   */
  public $storageBucket;
  /**
   * Version of the config specification.
   *
   * @var string
   */
  public $version;

  /**
   * The [`keyString`](https://cloud.google.com/api-
   * keys/docs/reference/rest/v2/projects.locations.keys#Key.FIELDS.key_string)
   * of the API key associated with the `WebApp`. Note that this value is _not_
   * the [`apiKeyId`](../projects.webApps#WebApp.FIELDS.api_key_id) (the UID) of
   * the API key associated with the `WebApp`.
   *
   * @param string $apiKey
   */
  public function setApiKey($apiKey)
  {
    $this->apiKey = $apiKey;
  }
  /**
   * @return string
   */
  public function getApiKey()
  {
    return $this->apiKey;
  }
  /**
   * Immutable. The globally unique, Firebase-assigned identifier for the
   * `WebApp`.
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
   * The domain Firebase Auth configures for OAuth redirects, in the format:
   * PROJECT_ID.firebaseapp.com
   *
   * @param string $authDomain
   */
  public function setAuthDomain($authDomain)
  {
    $this->authDomain = $authDomain;
  }
  /**
   * @return string
   */
  public function getAuthDomain()
  {
    return $this->authDomain;
  }
  /**
   * **DEPRECATED.** _Instead, find the URL of the default Realtime Database
   * instance using the [list endpoint](https://firebase.google.com/docs/referen
   * ce/rest/database/database-
   * management/rest/v1beta/projects.locations.instances/list) within the
   * Firebase Realtime Database REST API. If the default instance for the
   * Project has not yet been provisioned, the return might not contain a
   * default instance. Note that the config that's generated for the Firebase
   * console or the Firebase CLI uses the Realtime Database endpoint to populate
   * this value for that config._ The URL of the default Firebase Realtime
   * Database instance.
   *
   * @deprecated
   * @param string $databaseURL
   */
  public function setDatabaseURL($databaseURL)
  {
    $this->databaseURL = $databaseURL;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getDatabaseURL()
  {
    return $this->databaseURL;
  }
  /**
   * **DEPRECATED.** _Instead, use product-specific REST APIs to find the
   * location of each resource in a Project. This field may not be populated,
   * especially for newly provisioned projects after October 30, 2024._ The ID
   * of the Project's ["location for default Google Cloud
   * resources"](https://firebase.google.com/docs/projects/locations#default-
   * cloud-location), which are resources associated with Google App Engine. The
   * location is one of the available [App Engine
   * locations](https://cloud.google.com/about/locations#region). This field is
   * omitted if the location for default Google Cloud resources has not been
   * set.
   *
   * @deprecated
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * The unique Google-assigned identifier of the Google Analytics web stream
   * associated with the `WebApp`. Firebase SDKs use this ID to interact with
   * Google Analytics APIs. This field is only present if the `WebApp` is linked
   * to a web stream in a Google Analytics App + Web property. Learn more about
   * this ID and Google Analytics web streams in the [Analytics
   * documentation](https://support.google.com/analytics/answer/9304153). To
   * generate a `measurementId` and link the `WebApp` with a Google Analytics
   * web stream, call
   * [`AddGoogleAnalytics`](../../v1beta1/projects/addGoogleAnalytics). For apps
   * using the Firebase JavaScript SDK v7.20.0 and later, Firebase dynamically
   * fetches the `measurementId` when your app initializes Analytics. Having
   * this ID in your config object is optional, but it does serve as a fallback
   * in the rare case that the dynamic fetch fails.
   *
   * @param string $measurementId
   */
  public function setMeasurementId($measurementId)
  {
    $this->measurementId = $measurementId;
  }
  /**
   * @return string
   */
  public function getMeasurementId()
  {
    return $this->measurementId;
  }
  /**
   * The sender ID for use with Firebase Cloud Messaging.
   *
   * @param string $messagingSenderId
   */
  public function setMessagingSenderId($messagingSenderId)
  {
    $this->messagingSenderId = $messagingSenderId;
  }
  /**
   * @return string
   */
  public function getMessagingSenderId()
  {
    return $this->messagingSenderId;
  }
  /**
   * Immutable. A user-assigned unique identifier for the `FirebaseProject`.
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
   * Output only. Immutable. The globally unique, Google-assigned canonical
   * identifier for the Project. Use this identifier when configuring
   * integrations and/or making API calls to Google Cloud or third-party
   * services.
   *
   * @param string $projectNumber
   */
  public function setProjectNumber($projectNumber)
  {
    $this->projectNumber = $projectNumber;
  }
  /**
   * @return string
   */
  public function getProjectNumber()
  {
    return $this->projectNumber;
  }
  /**
   * Optional. Duplicate field for the URL of the default Realtime Database
   * instances (if the default instance has been provisioned). If the request
   * asks for the V2 config format, this field will be populated instead of
   * `realtime_database_instance_uri`.
   *
   * @param string $realtimeDatabaseUrl
   */
  public function setRealtimeDatabaseUrl($realtimeDatabaseUrl)
  {
    $this->realtimeDatabaseUrl = $realtimeDatabaseUrl;
  }
  /**
   * @return string
   */
  public function getRealtimeDatabaseUrl()
  {
    return $this->realtimeDatabaseUrl;
  }
  /**
   * **DEPRECATED.** _Instead, find the name of the default Cloud Storage for
   * Firebase bucket using the [list endpoint](https://firebase.google.com/docs/
   * reference/rest/storage/rest/v1beta/projects.buckets/list) within the Cloud
   * Storage for Firebase REST API. If the default bucket for the Project has
   * not yet been provisioned, the return might not contain a default bucket.
   * Note that the config that's generated for the Firebase console or the
   * Firebase CLI uses the Cloud Storage for Firebase endpoint to populate this
   * value for that config._ The name of the default Cloud Storage for Firebase
   * bucket.
   *
   * @deprecated
   * @param string $storageBucket
   */
  public function setStorageBucket($storageBucket)
  {
    $this->storageBucket = $storageBucket;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getStorageBucket()
  {
    return $this->storageBucket;
  }
  /**
   * Version of the config specification.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WebAppConfig::class, 'Google_Service_FirebaseManagement_WebAppConfig');

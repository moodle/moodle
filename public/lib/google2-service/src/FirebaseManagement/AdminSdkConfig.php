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

class AdminSdkConfig extends \Google\Model
{
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
   * Immutable. A user-assigned unique identifier for the `FirebaseProject`.
   * This identifier may appear in URLs or names for some Firebase resources
   * associated with the Project, but it should generally be treated as a
   * convenience alias to reference the Project.
   *
   * @var string
   */
  public $projectId;
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
   * Immutable. A user-assigned unique identifier for the `FirebaseProject`.
   * This identifier may appear in URLs or names for some Firebase resources
   * associated with the Project, but it should generally be treated as a
   * convenience alias to reference the Project.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdminSdkConfig::class, 'Google_Service_FirebaseManagement_AdminSdkConfig');

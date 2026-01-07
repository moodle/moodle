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

class DefaultResources extends \Google\Model
{
  /**
   * Output only. **DEPRECATED.** _Instead, find the name of the default
   * Firebase Hosting site using [ListSites](https://firebase.google.com/docs/re
   * ference/hosting/rest/v1beta1/projects.sites/list) within the Firebase
   * Hosting REST API. If the default Hosting site for the Project has not yet
   * been provisioned, the return might not contain a default site._ The name of
   * the default Firebase Hosting site, in the format: PROJECT_ID Though rare,
   * your `projectId` might already be used as the name for an existing Hosting
   * site in another project (learn more about creating non-default, [additional
   * sites](https://firebase.google.com/docs/hosting/multisites)). In these
   * cases, your `projectId` is appended with a hyphen then five alphanumeric
   * characters to create your default Hosting site name. For example, if your
   * `projectId` is `myproject123`, your default Hosting site name might be:
   * `myproject123-a5c16`
   *
   * @deprecated
   * @var string
   */
  public $hostingSite;
  /**
   * Output only. **DEPRECATED.** _Instead, use product-specific REST APIs to
   * find the location of each resource in a Project. This field may not be
   * populated, especially for newly provisioned projects after October 30,
   * 2024._ The ID of the Project's ["location for default Google Cloud
   * resources"](https://firebase.google.com/docs/projects/locations#default-
   * cloud-location), which are resources associated with Google App Engine. The
   * location is one of the available [Google App Engine
   * locations](https://cloud.google.com/about/locations#region). This field is
   * omitted if the location for default Google Cloud resources has not been
   * set.
   *
   * @deprecated
   * @var string
   */
  public $locationId;
  /**
   * Output only. **DEPRECATED.** _Instead, find the name of the default
   * Realtime Database instance using the [list endpoint](https://firebase.googl
   * e.com/docs/reference/rest/database/database-
   * management/rest/v1beta/projects.locations.instances/list) within the
   * Firebase Realtime Database REST API. If the default Realtime Database
   * instance for a Project has not yet been provisioned, the return might not
   * contain a default instance._ The default Firebase Realtime Database
   * instance name, in the format: PROJECT_ID Though rare, your `projectId`
   * might already be used as the name for an existing Realtime Database
   * instance in another project (learn more about [database
   * sharding](https://firebase.google.com/docs/database/usage/sharding)). In
   * these cases, your `projectId` is appended with a hyphen then five
   * alphanumeric characters to create your default Realtime Database instance
   * name. For example, if your `projectId` is `myproject123`, your default
   * database instance name might be: `myproject123-a5c16`
   *
   * @deprecated
   * @var string
   */
  public $realtimeDatabaseInstance;
  /**
   * Output only. **DEPRECATED.** _Instead, find the name of the default Cloud
   * Storage for Firebase bucket using the [list endpoint](https://firebase.goog
   * le.com/docs/reference/rest/storage/rest/v1beta/projects.buckets/list)
   * within the Cloud Storage for Firebase REST API. If the default bucket for
   * the Project has not yet been provisioned, the return might not contain a
   * default bucket._ The name of the default Cloud Storage for Firebase bucket,
   * in one of the following formats: * If provisioned _before_ October 30,
   * 2024: PROJECT_ID.firebasestorage.app * If provisioned _on or after_ October
   * 30, 2024: PROJECT_ID.firebasestorage.app
   *
   * @deprecated
   * @var string
   */
  public $storageBucket;

  /**
   * Output only. **DEPRECATED.** _Instead, find the name of the default
   * Firebase Hosting site using [ListSites](https://firebase.google.com/docs/re
   * ference/hosting/rest/v1beta1/projects.sites/list) within the Firebase
   * Hosting REST API. If the default Hosting site for the Project has not yet
   * been provisioned, the return might not contain a default site._ The name of
   * the default Firebase Hosting site, in the format: PROJECT_ID Though rare,
   * your `projectId` might already be used as the name for an existing Hosting
   * site in another project (learn more about creating non-default, [additional
   * sites](https://firebase.google.com/docs/hosting/multisites)). In these
   * cases, your `projectId` is appended with a hyphen then five alphanumeric
   * characters to create your default Hosting site name. For example, if your
   * `projectId` is `myproject123`, your default Hosting site name might be:
   * `myproject123-a5c16`
   *
   * @deprecated
   * @param string $hostingSite
   */
  public function setHostingSite($hostingSite)
  {
    $this->hostingSite = $hostingSite;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getHostingSite()
  {
    return $this->hostingSite;
  }
  /**
   * Output only. **DEPRECATED.** _Instead, use product-specific REST APIs to
   * find the location of each resource in a Project. This field may not be
   * populated, especially for newly provisioned projects after October 30,
   * 2024._ The ID of the Project's ["location for default Google Cloud
   * resources"](https://firebase.google.com/docs/projects/locations#default-
   * cloud-location), which are resources associated with Google App Engine. The
   * location is one of the available [Google App Engine
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
   * Output only. **DEPRECATED.** _Instead, find the name of the default
   * Realtime Database instance using the [list endpoint](https://firebase.googl
   * e.com/docs/reference/rest/database/database-
   * management/rest/v1beta/projects.locations.instances/list) within the
   * Firebase Realtime Database REST API. If the default Realtime Database
   * instance for a Project has not yet been provisioned, the return might not
   * contain a default instance._ The default Firebase Realtime Database
   * instance name, in the format: PROJECT_ID Though rare, your `projectId`
   * might already be used as the name for an existing Realtime Database
   * instance in another project (learn more about [database
   * sharding](https://firebase.google.com/docs/database/usage/sharding)). In
   * these cases, your `projectId` is appended with a hyphen then five
   * alphanumeric characters to create your default Realtime Database instance
   * name. For example, if your `projectId` is `myproject123`, your default
   * database instance name might be: `myproject123-a5c16`
   *
   * @deprecated
   * @param string $realtimeDatabaseInstance
   */
  public function setRealtimeDatabaseInstance($realtimeDatabaseInstance)
  {
    $this->realtimeDatabaseInstance = $realtimeDatabaseInstance;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getRealtimeDatabaseInstance()
  {
    return $this->realtimeDatabaseInstance;
  }
  /**
   * Output only. **DEPRECATED.** _Instead, find the name of the default Cloud
   * Storage for Firebase bucket using the [list endpoint](https://firebase.goog
   * le.com/docs/reference/rest/storage/rest/v1beta/projects.buckets/list)
   * within the Cloud Storage for Firebase REST API. If the default bucket for
   * the Project has not yet been provisioned, the return might not contain a
   * default bucket._ The name of the default Cloud Storage for Firebase bucket,
   * in one of the following formats: * If provisioned _before_ October 30,
   * 2024: PROJECT_ID.firebasestorage.app * If provisioned _on or after_ October
   * 30, 2024: PROJECT_ID.firebasestorage.app
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
class_alias(DefaultResources::class, 'Google_Service_FirebaseManagement_DefaultResources');

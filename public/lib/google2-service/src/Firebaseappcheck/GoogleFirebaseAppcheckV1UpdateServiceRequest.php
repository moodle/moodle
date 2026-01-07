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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1UpdateServiceRequest extends \Google\Model
{
  protected $serviceType = GoogleFirebaseAppcheckV1Service::class;
  protected $serviceDataType = '';
  /**
   * Required. A comma-separated list of names of fields in the Service to
   * update. Example: `enforcement_mode`.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The Service to update. The Service's `name` field is used to
   * identify the Service to be updated, in the format: ```
   * projects/{project_number}/services/{service_id} ``` Note that the
   * `service_id` element must be a supported service ID. Currently, the
   * following service IDs are supported: * `firebasestorage.googleapis.com`
   * (Cloud Storage for Firebase) * `firebasedatabase.googleapis.com` (Firebase
   * Realtime Database) * `firestore.googleapis.com` (Cloud Firestore) *
   * `oauth2.googleapis.com` (Google Identity for iOS)
   *
   * @param GoogleFirebaseAppcheckV1Service $service
   */
  public function setService(GoogleFirebaseAppcheckV1Service $service)
  {
    $this->service = $service;
  }
  /**
   * @return GoogleFirebaseAppcheckV1Service
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Required. A comma-separated list of names of fields in the Service to
   * update. Example: `enforcement_mode`.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1UpdateServiceRequest::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1UpdateServiceRequest');

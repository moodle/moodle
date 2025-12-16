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

class AddFirebaseRequest extends \Google\Model
{
  /**
   * **DEPRECATED.** _Instead, use product-specific REST APIs to work with the
   * location of each resource in a Project. This field may be ignored,
   * especially for newly provisioned projects after October 30, 2024._ The ID
   * of the Project's ["location for default Google Cloud
   * resources"](https://firebase.google.com/docs/projects/locations#default-
   * cloud-location), which are resources associated with Google App Engine. The
   * location must be one of the available [Google App Engine
   * locations](https://cloud.google.com/about/locations#region).
   *
   * @var string
   */
  public $locationId;

  /**
   * **DEPRECATED.** _Instead, use product-specific REST APIs to work with the
   * location of each resource in a Project. This field may be ignored,
   * especially for newly provisioned projects after October 30, 2024._ The ID
   * of the Project's ["location for default Google Cloud
   * resources"](https://firebase.google.com/docs/projects/locations#default-
   * cloud-location), which are resources associated with Google App Engine. The
   * location must be one of the available [Google App Engine
   * locations](https://cloud.google.com/about/locations#region).
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddFirebaseRequest::class, 'Google_Service_FirebaseManagement_AddFirebaseRequest');

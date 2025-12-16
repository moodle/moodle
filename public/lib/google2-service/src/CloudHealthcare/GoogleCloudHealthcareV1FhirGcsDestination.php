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

namespace Google\Service\CloudHealthcare;

class GoogleCloudHealthcareV1FhirGcsDestination extends \Google\Model
{
  /**
   * URI for a Cloud Storage directory where result files should be written, in
   * the format of `gs://{bucket-id}/{path/to/destination/dir}`. If there is no
   * trailing slash, the service appends one when composing the object path. The
   * user is responsible for creating the Cloud Storage bucket referenced in
   * `uri_prefix`.
   *
   * @var string
   */
  public $uriPrefix;

  /**
   * URI for a Cloud Storage directory where result files should be written, in
   * the format of `gs://{bucket-id}/{path/to/destination/dir}`. If there is no
   * trailing slash, the service appends one when composing the object path. The
   * user is responsible for creating the Cloud Storage bucket referenced in
   * `uri_prefix`.
   *
   * @param string $uriPrefix
   */
  public function setUriPrefix($uriPrefix)
  {
    $this->uriPrefix = $uriPrefix;
  }
  /**
   * @return string
   */
  public function getUriPrefix()
  {
    return $this->uriPrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudHealthcareV1FhirGcsDestination::class, 'Google_Service_CloudHealthcare_GoogleCloudHealthcareV1FhirGcsDestination');

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

namespace Google\Service\FirebaseAppHosting;

class Error extends \Google\Model
{
  /**
   * Indicates that generic error occurred outside of the Cloud Build or Cloud
   * Run processes, such as a pre-empted or user-canceled App Hosting Build.
   */
  public const ERROR_SOURCE_ERROR_SOURCE_UNSPECIFIED = 'ERROR_SOURCE_UNSPECIFIED';
  /**
   * Indicates that the build failed during the Cloud Build process, such as a
   * build timeout.
   */
  public const ERROR_SOURCE_CLOUD_BUILD = 'CLOUD_BUILD';
  /**
   * Indicates that the build failed during the Cloud Run process, such as a
   * service creation failure.
   */
  public const ERROR_SOURCE_CLOUD_RUN = 'CLOUD_RUN';
  /**
   * Output only. Resource link
   *
   * @var string
   */
  public $cloudResource;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * Output only. The source of the error for the build, if in a `FAILED` state.
   *
   * @var string
   */
  public $errorSource;

  /**
   * Output only. Resource link
   *
   * @param string $cloudResource
   */
  public function setCloudResource($cloudResource)
  {
    $this->cloudResource = $cloudResource;
  }
  /**
   * @return string
   */
  public function getCloudResource()
  {
    return $this->cloudResource;
  }
  /**
   * Output only. A status and (human readable) error message for the build, if
   * in a `FAILED` state.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The source of the error for the build, if in a `FAILED` state.
   *
   * Accepted values: ERROR_SOURCE_UNSPECIFIED, CLOUD_BUILD, CLOUD_RUN
   *
   * @param self::ERROR_SOURCE_* $errorSource
   */
  public function setErrorSource($errorSource)
  {
    $this->errorSource = $errorSource;
  }
  /**
   * @return self::ERROR_SOURCE_*
   */
  public function getErrorSource()
  {
    return $this->errorSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Error::class, 'Google_Service_FirebaseAppHosting_Error');

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

namespace Google\Service\Compute;

class InstanceGroupManagerResizeRequestStatusErrorErrors extends \Google\Collection
{
  protected $collection_key = 'errorDetails';
  /**
   * [Output Only] The error type identifier for this error.
   *
   * @var string
   */
  public $code;
  protected $errorDetailsType = InstanceGroupManagerResizeRequestStatusErrorErrorsErrorDetails::class;
  protected $errorDetailsDataType = 'array';
  /**
   * [Output Only] Indicates the field in the request that caused the error.
   * This property is optional.
   *
   * @var string
   */
  public $location;
  /**
   * [Output Only] An optional, human-readable error message.
   *
   * @var string
   */
  public $message;

  /**
   * [Output Only] The error type identifier for this error.
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * [Output Only] An optional list of messages that contain the error details.
   * There is a set of defined message types to use for providing details.The
   * syntax depends on the error code. For example, QuotaExceededInfo will have
   * details when the error code is QUOTA_EXCEEDED.
   *
   * @param InstanceGroupManagerResizeRequestStatusErrorErrorsErrorDetails[] $errorDetails
   */
  public function setErrorDetails($errorDetails)
  {
    $this->errorDetails = $errorDetails;
  }
  /**
   * @return InstanceGroupManagerResizeRequestStatusErrorErrorsErrorDetails[]
   */
  public function getErrorDetails()
  {
    return $this->errorDetails;
  }
  /**
   * [Output Only] Indicates the field in the request that caused the error.
   * This property is optional.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * [Output Only] An optional, human-readable error message.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagerResizeRequestStatusErrorErrors::class, 'Google_Service_Compute_InstanceGroupManagerResizeRequestStatusErrorErrors');

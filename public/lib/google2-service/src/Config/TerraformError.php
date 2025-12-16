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

namespace Google\Service\Config;

class TerraformError extends \Google\Model
{
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * A human-readable error description.
   *
   * @var string
   */
  public $errorDescription;
  /**
   * HTTP response code returned from Google Cloud Platform APIs when Terraform
   * fails to provision the resource. If unset or 0, no HTTP response code was
   * returned by Terraform.
   *
   * @var int
   */
  public $httpResponseCode;
  /**
   * Address of the resource associated with the error, e.g.
   * `google_compute_network.vpc_network`.
   *
   * @var string
   */
  public $resourceAddress;

  /**
   * Output only. Original error response from underlying Google API, if
   * available.
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
   * A human-readable error description.
   *
   * @param string $errorDescription
   */
  public function setErrorDescription($errorDescription)
  {
    $this->errorDescription = $errorDescription;
  }
  /**
   * @return string
   */
  public function getErrorDescription()
  {
    return $this->errorDescription;
  }
  /**
   * HTTP response code returned from Google Cloud Platform APIs when Terraform
   * fails to provision the resource. If unset or 0, no HTTP response code was
   * returned by Terraform.
   *
   * @param int $httpResponseCode
   */
  public function setHttpResponseCode($httpResponseCode)
  {
    $this->httpResponseCode = $httpResponseCode;
  }
  /**
   * @return int
   */
  public function getHttpResponseCode()
  {
    return $this->httpResponseCode;
  }
  /**
   * Address of the resource associated with the error, e.g.
   * `google_compute_network.vpc_network`.
   *
   * @param string $resourceAddress
   */
  public function setResourceAddress($resourceAddress)
  {
    $this->resourceAddress = $resourceAddress;
  }
  /**
   * @return string
   */
  public function getResourceAddress()
  {
    return $this->resourceAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TerraformError::class, 'Google_Service_Config_TerraformError');

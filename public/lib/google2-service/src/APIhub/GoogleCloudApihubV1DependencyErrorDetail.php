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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1DependencyErrorDetail extends \Google\Model
{
  /**
   * Default value used for no error in the dependency.
   */
  public const ERROR_ERROR_UNSPECIFIED = 'ERROR_UNSPECIFIED';
  /**
   * Supplier entity has been deleted.
   */
  public const ERROR_SUPPLIER_NOT_FOUND = 'SUPPLIER_NOT_FOUND';
  /**
   * Supplier entity has been recreated.
   */
  public const ERROR_SUPPLIER_RECREATED = 'SUPPLIER_RECREATED';
  /**
   * Optional. Error in the dependency.
   *
   * @var string
   */
  public $error;
  /**
   * Optional. Timestamp at which the error was found.
   *
   * @var string
   */
  public $errorTime;

  /**
   * Optional. Error in the dependency.
   *
   * Accepted values: ERROR_UNSPECIFIED, SUPPLIER_NOT_FOUND, SUPPLIER_RECREATED
   *
   * @param self::ERROR_* $error
   */
  public function setError($error)
  {
    $this->error = $error;
  }
  /**
   * @return self::ERROR_*
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Optional. Timestamp at which the error was found.
   *
   * @param string $errorTime
   */
  public function setErrorTime($errorTime)
  {
    $this->errorTime = $errorTime;
  }
  /**
   * @return string
   */
  public function getErrorTime()
  {
    return $this->errorTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1DependencyErrorDetail::class, 'Google_Service_APIhub_GoogleCloudApihubV1DependencyErrorDetail');

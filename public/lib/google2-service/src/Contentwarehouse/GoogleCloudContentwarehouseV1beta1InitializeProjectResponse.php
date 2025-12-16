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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1beta1InitializeProjectResponse extends \Google\Model
{
  /**
   * Clients should never see this.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Finished project initialization without error.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Finished project initialization with an error.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Client canceled the LRO.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Ask the customer to check the operation for results.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The message of the project initialization process.
   *
   * @var string
   */
  public $message;
  /**
   * The state of the project initialization process.
   *
   * @var string
   */
  public $state;

  /**
   * The message of the project initialization process.
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
  /**
   * The state of the project initialization process.
   *
   * Accepted values: STATE_UNSPECIFIED, SUCCEEDED, FAILED, CANCELLED, RUNNING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1beta1InitializeProjectResponse::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1beta1InitializeProjectResponse');

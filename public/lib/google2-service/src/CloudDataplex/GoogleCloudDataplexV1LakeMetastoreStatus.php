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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1LakeMetastoreStatus extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * A Metastore service instance is not associated with the lake.
   */
  public const STATE_NONE = 'NONE';
  /**
   * A Metastore service instance is attached to the lake.
   */
  public const STATE_READY = 'READY';
  /**
   * Attach/detach is in progress.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Attach/detach could not be done due to errors.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The URI of the endpoint used to access the Metastore service.
   *
   * @var string
   */
  public $endpoint;
  /**
   * Additional information about the current status.
   *
   * @var string
   */
  public $message;
  /**
   * Current state of association.
   *
   * @var string
   */
  public $state;
  /**
   * Last update time of the metastore status of the lake.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The URI of the endpoint used to access the Metastore service.
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * Additional information about the current status.
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
   * Current state of association.
   *
   * Accepted values: STATE_UNSPECIFIED, NONE, READY, UPDATING, ERROR
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
  /**
   * Last update time of the metastore status of the lake.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1LakeMetastoreStatus::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1LakeMetastoreStatus');

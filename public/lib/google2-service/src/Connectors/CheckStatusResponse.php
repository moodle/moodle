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

namespace Google\Service\Connectors;

class CheckStatusResponse extends \Google\Model
{
  /**
   * State unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The connector is active and ready to process runtime requests. This can
   * also mean that from the connector's perspective, the connector is not in an
   * error state and should be able to process runtime requests successfully.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The connector is in an error state and cannot process runtime requests. An
   * example reason would be that the connection container has some network
   * issues that prevent outbound requests from being sent.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * This is a more specific error state that the developers can opt to use when
   * the connector is facing auth-related errors caused by auth configuration
   * not present, invalid auth credentials, etc.
   */
  public const STATE_AUTH_ERROR = 'AUTH_ERROR';
  /**
   * When the connector is not in ACTIVE state, the description must be
   * populated to specify the reason why it's not in ACTIVE state.
   *
   * @var string
   */
  public $description;
  /**
   * Metadata like service latency, etc.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * State of the connector.
   *
   * @var string
   */
  public $state;

  /**
   * When the connector is not in ACTIVE state, the description must be
   * populated to specify the reason why it's not in ACTIVE state.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Metadata like service latency, etc.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * State of the connector.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, ERROR, AUTH_ERROR
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
class_alias(CheckStatusResponse::class, 'Google_Service_Connectors_CheckStatusResponse');

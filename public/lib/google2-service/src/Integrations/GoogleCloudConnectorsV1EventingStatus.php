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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1EventingStatus extends \Google\Model
{
  /**
   * Default state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Eventing is enabled and ready to receive events.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Eventing is not active due to an error.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Ingress endpoint required.
   */
  public const STATE_INGRESS_ENDPOINT_REQUIRED = 'INGRESS_ENDPOINT_REQUIRED';
  /**
   * Output only. Description of error if State is set to "ERROR".
   *
   * @var string
   */
  public $description;
  /**
   * Output only. State.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Description of error if State is set to "ERROR".
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
   * Output only. State.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, ERROR,
   * INGRESS_ENDPOINT_REQUIRED
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
class_alias(GoogleCloudConnectorsV1EventingStatus::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1EventingStatus');

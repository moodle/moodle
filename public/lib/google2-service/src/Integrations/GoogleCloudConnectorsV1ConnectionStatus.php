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

class GoogleCloudConnectorsV1ConnectionStatus extends \Google\Model
{
  /**
   * Connection does not have a state yet.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Connection is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Connection is running and ready for requests.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Connection is stopped.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * Connection is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Connection is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Connection is not running due to an error.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Connection is not running because the authorization configuration is not
   * complete.
   */
  public const STATE_AUTHORIZATION_REQUIRED = 'AUTHORIZATION_REQUIRED';
  /**
   * Description.
   *
   * @var string
   */
  public $description;
  /**
   * State.
   *
   * @var string
   */
  public $state;
  /**
   * Status provides detailed information for the state.
   *
   * @var string
   */
  public $status;

  /**
   * Description.
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
   * State.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, INACTIVE, DELETING,
   * UPDATING, ERROR, AUTHORIZATION_REQUIRED
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
   * Status provides detailed information for the state.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1ConnectionStatus::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1ConnectionStatus');

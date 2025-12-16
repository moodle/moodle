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

namespace Google\Service\TrafficDirectorService;

class DynamicListener extends \Google\Model
{
  /**
   * Resource status is not available/unknown.
   */
  public const CLIENT_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Client requested this resource but hasn't received any update from
   * management server. The client will not fail requests, but will queue them
   * until update arrives or the client times out waiting for the resource.
   */
  public const CLIENT_STATUS_REQUESTED = 'REQUESTED';
  /**
   * This resource has been requested by the client but has either not been
   * delivered by the server or was previously delivered by the server and then
   * subsequently removed from resources provided by the server. For more
   * information, please refer to the :ref:`"Knowing When a Requested Resource
   * Does Not Exist" ` section.
   */
  public const CLIENT_STATUS_DOES_NOT_EXIST = 'DOES_NOT_EXIST';
  /**
   * Client received this resource and replied with ACK.
   */
  public const CLIENT_STATUS_ACKED = 'ACKED';
  /**
   * Client received this resource and replied with NACK.
   */
  public const CLIENT_STATUS_NACKED = 'NACKED';
  /**
   * Client received an error from the control plane. The attached config dump
   * is the most recent accepted one. If no config is accepted yet, the attached
   * config dump will be empty.
   */
  public const CLIENT_STATUS_RECEIVED_ERROR = 'RECEIVED_ERROR';
  /**
   * Client timed out waiting for the resource from the control plane.
   */
  public const CLIENT_STATUS_TIMEOUT = 'TIMEOUT';
  protected $activeStateType = DynamicListenerState::class;
  protected $activeStateDataType = '';
  /**
   * The client status of this resource. [#not-implemented-hide:]
   *
   * @var string
   */
  public $clientStatus;
  protected $drainingStateType = DynamicListenerState::class;
  protected $drainingStateDataType = '';
  protected $errorStateType = UpdateFailureState::class;
  protected $errorStateDataType = '';
  /**
   * The name or unique id of this listener, pulled from the
   * DynamicListenerState config.
   *
   * @var string
   */
  public $name;
  protected $warmingStateType = DynamicListenerState::class;
  protected $warmingStateDataType = '';

  /**
   * The listener state for any active listener by this name. These are
   * listeners that are available to service data plane traffic.
   *
   * @param DynamicListenerState $activeState
   */
  public function setActiveState(DynamicListenerState $activeState)
  {
    $this->activeState = $activeState;
  }
  /**
   * @return DynamicListenerState
   */
  public function getActiveState()
  {
    return $this->activeState;
  }
  /**
   * The client status of this resource. [#not-implemented-hide:]
   *
   * Accepted values: UNKNOWN, REQUESTED, DOES_NOT_EXIST, ACKED, NACKED,
   * RECEIVED_ERROR, TIMEOUT
   *
   * @param self::CLIENT_STATUS_* $clientStatus
   */
  public function setClientStatus($clientStatus)
  {
    $this->clientStatus = $clientStatus;
  }
  /**
   * @return self::CLIENT_STATUS_*
   */
  public function getClientStatus()
  {
    return $this->clientStatus;
  }
  /**
   * The listener state for any draining listener by this name. These are
   * listeners that are currently undergoing draining in preparation to stop
   * servicing data plane traffic. Note that if attempting to recreate an Envoy
   * configuration from a configuration dump, the draining listeners should
   * generally be discarded.
   *
   * @param DynamicListenerState $drainingState
   */
  public function setDrainingState(DynamicListenerState $drainingState)
  {
    $this->drainingState = $drainingState;
  }
  /**
   * @return DynamicListenerState
   */
  public function getDrainingState()
  {
    return $this->drainingState;
  }
  /**
   * Set if the last update failed, cleared after the next successful update.
   * The ``error_state`` field contains the rejected version of this particular
   * resource along with the reason and timestamp. For successfully updated or
   * acknowledged resource, this field should be empty.
   *
   * @param UpdateFailureState $errorState
   */
  public function setErrorState(UpdateFailureState $errorState)
  {
    $this->errorState = $errorState;
  }
  /**
   * @return UpdateFailureState
   */
  public function getErrorState()
  {
    return $this->errorState;
  }
  /**
   * The name or unique id of this listener, pulled from the
   * DynamicListenerState config.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The listener state for any warming listener by this name. These are
   * listeners that are currently undergoing warming in preparation to service
   * data plane traffic. Note that if attempting to recreate an Envoy
   * configuration from a configuration dump, the warming listeners should
   * generally be discarded.
   *
   * @param DynamicListenerState $warmingState
   */
  public function setWarmingState(DynamicListenerState $warmingState)
  {
    $this->warmingState = $warmingState;
  }
  /**
   * @return DynamicListenerState
   */
  public function getWarmingState()
  {
    return $this->warmingState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicListener::class, 'Google_Service_TrafficDirectorService_DynamicListener');

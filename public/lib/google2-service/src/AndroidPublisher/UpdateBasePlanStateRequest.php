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

namespace Google\Service\AndroidPublisher;

class UpdateBasePlanStateRequest extends \Google\Model
{
  protected $activateBasePlanRequestType = ActivateBasePlanRequest::class;
  protected $activateBasePlanRequestDataType = '';
  protected $deactivateBasePlanRequestType = DeactivateBasePlanRequest::class;
  protected $deactivateBasePlanRequestDataType = '';

  /**
   * Activates a base plan. Once activated, base plans will be available to new
   * subscribers.
   *
   * @param ActivateBasePlanRequest $activateBasePlanRequest
   */
  public function setActivateBasePlanRequest(ActivateBasePlanRequest $activateBasePlanRequest)
  {
    $this->activateBasePlanRequest = $activateBasePlanRequest;
  }
  /**
   * @return ActivateBasePlanRequest
   */
  public function getActivateBasePlanRequest()
  {
    return $this->activateBasePlanRequest;
  }
  /**
   * Deactivates a base plan. Once deactivated, the base plan will become
   * unavailable to new subscribers, but existing subscribers will maintain
   * their subscription
   *
   * @param DeactivateBasePlanRequest $deactivateBasePlanRequest
   */
  public function setDeactivateBasePlanRequest(DeactivateBasePlanRequest $deactivateBasePlanRequest)
  {
    $this->deactivateBasePlanRequest = $deactivateBasePlanRequest;
  }
  /**
   * @return DeactivateBasePlanRequest
   */
  public function getDeactivateBasePlanRequest()
  {
    return $this->deactivateBasePlanRequest;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateBasePlanStateRequest::class, 'Google_Service_AndroidPublisher_UpdateBasePlanStateRequest');

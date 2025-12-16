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

class UpdateSubscriptionOfferStateRequest extends \Google\Model
{
  protected $activateSubscriptionOfferRequestType = ActivateSubscriptionOfferRequest::class;
  protected $activateSubscriptionOfferRequestDataType = '';
  protected $deactivateSubscriptionOfferRequestType = DeactivateSubscriptionOfferRequest::class;
  protected $deactivateSubscriptionOfferRequestDataType = '';

  /**
   * Activates an offer. Once activated, the offer will be available to new
   * subscribers.
   *
   * @param ActivateSubscriptionOfferRequest $activateSubscriptionOfferRequest
   */
  public function setActivateSubscriptionOfferRequest(ActivateSubscriptionOfferRequest $activateSubscriptionOfferRequest)
  {
    $this->activateSubscriptionOfferRequest = $activateSubscriptionOfferRequest;
  }
  /**
   * @return ActivateSubscriptionOfferRequest
   */
  public function getActivateSubscriptionOfferRequest()
  {
    return $this->activateSubscriptionOfferRequest;
  }
  /**
   * Deactivates an offer. Once deactivated, the offer will become unavailable
   * to new subscribers, but existing subscribers will maintain their
   * subscription
   *
   * @param DeactivateSubscriptionOfferRequest $deactivateSubscriptionOfferRequest
   */
  public function setDeactivateSubscriptionOfferRequest(DeactivateSubscriptionOfferRequest $deactivateSubscriptionOfferRequest)
  {
    $this->deactivateSubscriptionOfferRequest = $deactivateSubscriptionOfferRequest;
  }
  /**
   * @return DeactivateSubscriptionOfferRequest
   */
  public function getDeactivateSubscriptionOfferRequest()
  {
    return $this->deactivateSubscriptionOfferRequest;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateSubscriptionOfferStateRequest::class, 'Google_Service_AndroidPublisher_UpdateSubscriptionOfferStateRequest');

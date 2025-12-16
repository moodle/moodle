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

class UpdateOneTimeProductOfferStateRequest extends \Google\Model
{
  protected $activateOneTimeProductOfferRequestType = ActivateOneTimeProductOfferRequest::class;
  protected $activateOneTimeProductOfferRequestDataType = '';
  protected $cancelOneTimeProductOfferRequestType = CancelOneTimeProductOfferRequest::class;
  protected $cancelOneTimeProductOfferRequestDataType = '';
  protected $deactivateOneTimeProductOfferRequestType = DeactivateOneTimeProductOfferRequest::class;
  protected $deactivateOneTimeProductOfferRequestDataType = '';

  /**
   * Activates an offer. Once activated, the offer is available to users, as
   * long as its conditions are met.
   *
   * @param ActivateOneTimeProductOfferRequest $activateOneTimeProductOfferRequest
   */
  public function setActivateOneTimeProductOfferRequest(ActivateOneTimeProductOfferRequest $activateOneTimeProductOfferRequest)
  {
    $this->activateOneTimeProductOfferRequest = $activateOneTimeProductOfferRequest;
  }
  /**
   * @return ActivateOneTimeProductOfferRequest
   */
  public function getActivateOneTimeProductOfferRequest()
  {
    return $this->activateOneTimeProductOfferRequest;
  }
  /**
   * Cancels an offer. Once cancelled, the offer is not available to users. Any
   * pending orders related to this offer will be cancelled. This state
   * transition is specific to pre-orders.
   *
   * @param CancelOneTimeProductOfferRequest $cancelOneTimeProductOfferRequest
   */
  public function setCancelOneTimeProductOfferRequest(CancelOneTimeProductOfferRequest $cancelOneTimeProductOfferRequest)
  {
    $this->cancelOneTimeProductOfferRequest = $cancelOneTimeProductOfferRequest;
  }
  /**
   * @return CancelOneTimeProductOfferRequest
   */
  public function getCancelOneTimeProductOfferRequest()
  {
    return $this->cancelOneTimeProductOfferRequest;
  }
  /**
   * Deactivates an offer. Once deactivated, the offer is no longer available to
   * users. This state transition is specific to discounted offers.
   *
   * @param DeactivateOneTimeProductOfferRequest $deactivateOneTimeProductOfferRequest
   */
  public function setDeactivateOneTimeProductOfferRequest(DeactivateOneTimeProductOfferRequest $deactivateOneTimeProductOfferRequest)
  {
    $this->deactivateOneTimeProductOfferRequest = $deactivateOneTimeProductOfferRequest;
  }
  /**
   * @return DeactivateOneTimeProductOfferRequest
   */
  public function getDeactivateOneTimeProductOfferRequest()
  {
    return $this->deactivateOneTimeProductOfferRequest;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateOneTimeProductOfferStateRequest::class, 'Google_Service_AndroidPublisher_UpdateOneTimeProductOfferStateRequest');

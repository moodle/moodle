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

namespace Google\Service\CloudControlsPartnerService;

class AccessApprovalRequest extends \Google\Model
{
  /**
   * Identifier. Format: `organizations/{organization}/locations/{location}/cust
   * omers/{customer}/workloads/{workload}/accessApprovalRequests/{access_approv
   * al_request}`
   *
   * @var string
   */
  public $name;
  /**
   * The time at which approval was requested.
   *
   * @var string
   */
  public $requestTime;
  /**
   * The requested expiration for the approval. If the request is approved,
   * access will be granted from the time of approval until the expiration time.
   *
   * @var string
   */
  public $requestedExpirationTime;
  protected $requestedReasonType = AccessReason::class;
  protected $requestedReasonDataType = '';

  /**
   * Identifier. Format: `organizations/{organization}/locations/{location}/cust
   * omers/{customer}/workloads/{workload}/accessApprovalRequests/{access_approv
   * al_request}`
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
   * The time at which approval was requested.
   *
   * @param string $requestTime
   */
  public function setRequestTime($requestTime)
  {
    $this->requestTime = $requestTime;
  }
  /**
   * @return string
   */
  public function getRequestTime()
  {
    return $this->requestTime;
  }
  /**
   * The requested expiration for the approval. If the request is approved,
   * access will be granted from the time of approval until the expiration time.
   *
   * @param string $requestedExpirationTime
   */
  public function setRequestedExpirationTime($requestedExpirationTime)
  {
    $this->requestedExpirationTime = $requestedExpirationTime;
  }
  /**
   * @return string
   */
  public function getRequestedExpirationTime()
  {
    return $this->requestedExpirationTime;
  }
  /**
   * The justification for which approval is being requested.
   *
   * @param AccessReason $requestedReason
   */
  public function setRequestedReason(AccessReason $requestedReason)
  {
    $this->requestedReason = $requestedReason;
  }
  /**
   * @return AccessReason
   */
  public function getRequestedReason()
  {
    return $this->requestedReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessApprovalRequest::class, 'Google_Service_CloudControlsPartnerService_AccessApprovalRequest');

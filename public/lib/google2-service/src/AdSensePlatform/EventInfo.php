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

namespace Google\Service\AdSensePlatform;

class EventInfo extends \Google\Model
{
  protected $billingAddressType = Address::class;
  protected $billingAddressDataType = '';
  /**
   * Required. The email address that is associated with the publisher when
   * performing the event.
   *
   * @var string
   */
  public $email;

  /**
   * The billing address of the publisher associated with this event, if
   * available.
   *
   * @param Address $billingAddress
   */
  public function setBillingAddress(Address $billingAddress)
  {
    $this->billingAddress = $billingAddress;
  }
  /**
   * @return Address
   */
  public function getBillingAddress()
  {
    return $this->billingAddress;
  }
  /**
   * Required. The email address that is associated with the publisher when
   * performing the event.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventInfo::class, 'Google_Service_AdSensePlatform_EventInfo');

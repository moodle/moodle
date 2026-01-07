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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1PhoneAuthenticationEvent extends \Google\Model
{
  /**
   * Optional. The time at which the multi-factor authentication event
   * (challenge or verification) occurred.
   *
   * @var string
   */
  public $eventTime;
  /**
   * Required. Phone number in E.164 format for which a multi-factor
   * authentication challenge was initiated, succeeded, or failed.
   *
   * @var string
   */
  public $phoneNumber;

  /**
   * Optional. The time at which the multi-factor authentication event
   * (challenge or verification) occurred.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * Required. Phone number in E.164 format for which a multi-factor
   * authentication challenge was initiated, succeeded, or failed.
   *
   * @param string $phoneNumber
   */
  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }
  /**
   * @return string
   */
  public function getPhoneNumber()
  {
    return $this->phoneNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1PhoneAuthenticationEvent::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1PhoneAuthenticationEvent');

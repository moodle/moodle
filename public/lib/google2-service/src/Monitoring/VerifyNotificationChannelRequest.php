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

namespace Google\Service\Monitoring;

class VerifyNotificationChannelRequest extends \Google\Model
{
  /**
   * Required. The verification code that was delivered to the channel as a
   * result of invoking the SendNotificationChannelVerificationCode API method
   * or that was retrieved from a verified channel via
   * GetNotificationChannelVerificationCode. For example, one might have
   * "G-123456" or "TKNZGhhd2EyN3I1MnRnMjRv" (in general, one is only guaranteed
   * that the code is valid UTF-8; one should not make any assumptions regarding
   * the structure or format of the code).
   *
   * @var string
   */
  public $code;

  /**
   * Required. The verification code that was delivered to the channel as a
   * result of invoking the SendNotificationChannelVerificationCode API method
   * or that was retrieved from a verified channel via
   * GetNotificationChannelVerificationCode. For example, one might have
   * "G-123456" or "TKNZGhhd2EyN3I1MnRnMjRv" (in general, one is only guaranteed
   * that the code is valid UTF-8; one should not make any assumptions regarding
   * the structure or format of the code).
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VerifyNotificationChannelRequest::class, 'Google_Service_Monitoring_VerifyNotificationChannelRequest');

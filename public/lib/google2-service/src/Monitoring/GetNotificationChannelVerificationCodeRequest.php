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

class GetNotificationChannelVerificationCodeRequest extends \Google\Model
{
  /**
   * The desired expiration time. If specified, the API will guarantee that the
   * returned code will not be valid after the specified timestamp; however, the
   * API cannot guarantee that the returned code will be valid for at least as
   * long as the requested time (the API puts an upper bound on the amount of
   * time for which a code may be valid). If omitted, a default expiration will
   * be used, which may be less than the max permissible expiration (so
   * specifying an expiration may extend the code's lifetime over omitting an
   * expiration, even though the API does impose an upper limit on the maximum
   * expiration that is permitted).
   *
   * @var string
   */
  public $expireTime;

  /**
   * The desired expiration time. If specified, the API will guarantee that the
   * returned code will not be valid after the specified timestamp; however, the
   * API cannot guarantee that the returned code will be valid for at least as
   * long as the requested time (the API puts an upper bound on the amount of
   * time for which a code may be valid). If omitted, a default expiration will
   * be used, which may be less than the max permissible expiration (so
   * specifying an expiration may extend the code's lifetime over omitting an
   * expiration, even though the API does impose an upper limit on the maximum
   * expiration that is permitted).
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetNotificationChannelVerificationCodeRequest::class, 'Google_Service_Monitoring_GetNotificationChannelVerificationCodeRequest');

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

namespace Google\Service\PaymentsResellerSubscription;

class GenerateUserSessionResponse extends \Google\Model
{
  protected $userSessionType = UserSession::class;
  protected $userSessionDataType = '';

  /**
   * The generated user session. The token size is proportional to the size of
   * the intent payload.
   *
   * @param UserSession $userSession
   */
  public function setUserSession(UserSession $userSession)
  {
    $this->userSession = $userSession;
  }
  /**
   * @return UserSession
   */
  public function getUserSession()
  {
    return $this->userSession;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateUserSessionResponse::class, 'Google_Service_PaymentsResellerSubscription_GenerateUserSessionResponse');

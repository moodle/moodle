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

namespace Google\Service\AuthorizedBuyersMarketplace;

class ClientUser extends \Google\Model
{
  /**
   * A placeholder for an undefined user state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * A user who was created but hasn't accepted the invitation yet, not allowed
   * to access the Authorized Buyers UI.
   */
  public const STATE_INVITED = 'INVITED';
  /**
   * A user that is currently active and allowed to access the Authorized Buyers
   * UI.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * A user that is currently inactive and not allowed to access the Authorized
   * Buyers UI.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * Required. The client user's email address that has to be unique across all
   * users for the same client.
   *
   * @var string
   */
  public $email;
  /**
   * Output only. The resource name of the client user. Format:
   * `buyers/{accountId}/clients/{clientAccountId}/users/{userId}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The state of the client user.
   *
   * @var string
   */
  public $state;

  /**
   * Required. The client user's email address that has to be unique across all
   * users for the same client.
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
  /**
   * Output only. The resource name of the client user. Format:
   * `buyers/{accountId}/clients/{clientAccountId}/users/{userId}`
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
   * Output only. The state of the client user.
   *
   * Accepted values: STATE_UNSPECIFIED, INVITED, ACTIVE, INACTIVE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClientUser::class, 'Google_Service_AuthorizedBuyersMarketplace_ClientUser');

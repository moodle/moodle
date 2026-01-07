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

namespace Google\Service\AdExchangeBuyerII;

class ClientUserInvitation extends \Google\Model
{
  /**
   * Numerical account ID of the client buyer that the invited user is
   * associated with. The value of this field is ignored in create operations.
   *
   * @var string
   */
  public $clientAccountId;
  /**
   * The email address to which the invitation is sent. Email addresses should
   * be unique among all client users under each sponsor buyer.
   *
   * @var string
   */
  public $email;
  /**
   * The unique numerical ID of the invitation that is sent to the user. The
   * value of this field is ignored in create operations.
   *
   * @var string
   */
  public $invitationId;

  /**
   * Numerical account ID of the client buyer that the invited user is
   * associated with. The value of this field is ignored in create operations.
   *
   * @param string $clientAccountId
   */
  public function setClientAccountId($clientAccountId)
  {
    $this->clientAccountId = $clientAccountId;
  }
  /**
   * @return string
   */
  public function getClientAccountId()
  {
    return $this->clientAccountId;
  }
  /**
   * The email address to which the invitation is sent. Email addresses should
   * be unique among all client users under each sponsor buyer.
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
   * The unique numerical ID of the invitation that is sent to the user. The
   * value of this field is ignored in create operations.
   *
   * @param string $invitationId
   */
  public function setInvitationId($invitationId)
  {
    $this->invitationId = $invitationId;
  }
  /**
   * @return string
   */
  public function getInvitationId()
  {
    return $this->invitationId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClientUserInvitation::class, 'Google_Service_AdExchangeBuyerII_ClientUserInvitation');

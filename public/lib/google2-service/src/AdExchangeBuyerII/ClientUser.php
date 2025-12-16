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

class ClientUser extends \Google\Model
{
  /**
   * A placeholder for an undefined user status.
   */
  public const STATUS_USER_STATUS_UNSPECIFIED = 'USER_STATUS_UNSPECIFIED';
  /**
   * A user who was already created but hasn't accepted the invitation yet.
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * A user that is currently active.
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  /**
   * A user that is currently disabled.
   */
  public const STATUS_DISABLED = 'DISABLED';
  /**
   * Numerical account ID of the client buyer with which the user is associated;
   * the buyer must be a client of the current sponsor buyer. The value of this
   * field is ignored in an update operation.
   *
   * @var string
   */
  public $clientAccountId;
  /**
   * User's email address. The value of this field is ignored in an update
   * operation.
   *
   * @var string
   */
  public $email;
  /**
   * The status of the client user.
   *
   * @var string
   */
  public $status;
  /**
   * The unique numerical ID of the client user that has accepted an invitation.
   * The value of this field is ignored in an update operation.
   *
   * @var string
   */
  public $userId;

  /**
   * Numerical account ID of the client buyer with which the user is associated;
   * the buyer must be a client of the current sponsor buyer. The value of this
   * field is ignored in an update operation.
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
   * User's email address. The value of this field is ignored in an update
   * operation.
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
   * The status of the client user.
   *
   * Accepted values: USER_STATUS_UNSPECIFIED, PENDING, ACTIVE, DISABLED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The unique numerical ID of the client user that has accepted an invitation.
   * The value of this field is ignored in an update operation.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClientUser::class, 'Google_Service_AdExchangeBuyerII_ClientUser');

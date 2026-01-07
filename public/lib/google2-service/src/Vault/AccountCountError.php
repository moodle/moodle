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

namespace Google\Service\Vault;

class AccountCountError extends \Google\Model
{
  /**
   * Default.
   */
  public const ERROR_TYPE_ERROR_TYPE_UNSPECIFIED = 'ERROR_TYPE_UNSPECIFIED';
  /**
   * Permanent - prefix terms expanded to too many query terms.
   */
  public const ERROR_TYPE_WILDCARD_TOO_BROAD = 'WILDCARD_TOO_BROAD';
  /**
   * Permanent - query contains too many terms.
   */
  public const ERROR_TYPE_TOO_MANY_TERMS = 'TOO_MANY_TERMS';
  /**
   * Transient - data in transit between storage replicas, temporarily
   * unavailable.
   */
  public const ERROR_TYPE_LOCATION_UNAVAILABLE = 'LOCATION_UNAVAILABLE';
  /**
   * Unrecognized error.
   */
  public const ERROR_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Deadline exceeded when querying the account.
   */
  public const ERROR_TYPE_DEADLINE_EXCEEDED = 'DEADLINE_EXCEEDED';
  protected $accountType = UserInfo::class;
  protected $accountDataType = '';
  /**
   * Account query error.
   *
   * @var string
   */
  public $errorType;

  /**
   * Account owner.
   *
   * @param UserInfo $account
   */
  public function setAccount(UserInfo $account)
  {
    $this->account = $account;
  }
  /**
   * @return UserInfo
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * Account query error.
   *
   * Accepted values: ERROR_TYPE_UNSPECIFIED, WILDCARD_TOO_BROAD,
   * TOO_MANY_TERMS, LOCATION_UNAVAILABLE, UNKNOWN, DEADLINE_EXCEEDED
   *
   * @param self::ERROR_TYPE_* $errorType
   */
  public function setErrorType($errorType)
  {
    $this->errorType = $errorType;
  }
  /**
   * @return self::ERROR_TYPE_*
   */
  public function getErrorType()
  {
    return $this->errorType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountCountError::class, 'Google_Service_Vault_AccountCountError');

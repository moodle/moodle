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

namespace Google\Service\Baremetalsolution;

class LoadInstanceAuthInfoResponse extends \Google\Collection
{
  protected $collection_key = 'sshKeys';
  protected $sshKeysType = SSHKey::class;
  protected $sshKeysDataType = 'array';
  protected $userAccountsType = UserAccount::class;
  protected $userAccountsDataType = 'map';

  /**
   * List of ssh keys.
   *
   * @param SSHKey[] $sshKeys
   */
  public function setSshKeys($sshKeys)
  {
    $this->sshKeys = $sshKeys;
  }
  /**
   * @return SSHKey[]
   */
  public function getSshKeys()
  {
    return $this->sshKeys;
  }
  /**
   * Map of username to the user account info.
   *
   * @param UserAccount[] $userAccounts
   */
  public function setUserAccounts($userAccounts)
  {
    $this->userAccounts = $userAccounts;
  }
  /**
   * @return UserAccount[]
   */
  public function getUserAccounts()
  {
    return $this->userAccounts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoadInstanceAuthInfoResponse::class, 'Google_Service_Baremetalsolution_LoadInstanceAuthInfoResponse');

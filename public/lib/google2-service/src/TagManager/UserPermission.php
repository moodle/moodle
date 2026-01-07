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

namespace Google\Service\TagManager;

class UserPermission extends \Google\Collection
{
  protected $collection_key = 'containerAccess';
  protected $accountAccessType = AccountAccess::class;
  protected $accountAccessDataType = '';
  /**
   * The Account ID uniquely identifies the GTM Account.
   *
   * @var string
   */
  public $accountId;
  protected $containerAccessType = ContainerAccess::class;
  protected $containerAccessDataType = 'array';
  /**
   * User's email address.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * GTM UserPermission's API relative path.
   *
   * @var string
   */
  public $path;

  /**
   * GTM Account access permissions.
   *
   * @param AccountAccess $accountAccess
   */
  public function setAccountAccess(AccountAccess $accountAccess)
  {
    $this->accountAccess = $accountAccess;
  }
  /**
   * @return AccountAccess
   */
  public function getAccountAccess()
  {
    return $this->accountAccess;
  }
  /**
   * The Account ID uniquely identifies the GTM Account.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * GTM Container access permissions.
   *
   * @param ContainerAccess[] $containerAccess
   */
  public function setContainerAccess($containerAccess)
  {
    $this->containerAccess = $containerAccess;
  }
  /**
   * @return ContainerAccess[]
   */
  public function getContainerAccess()
  {
    return $this->containerAccess;
  }
  /**
   * User's email address.
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  /**
   * @return string
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
  /**
   * GTM UserPermission's API relative path.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserPermission::class, 'Google_Service_TagManager_UserPermission');

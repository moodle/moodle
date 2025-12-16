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

namespace Google\Service\SecretManager;

class Replication extends \Google\Model
{
  protected $automaticType = Automatic::class;
  protected $automaticDataType = '';
  protected $userManagedType = UserManaged::class;
  protected $userManagedDataType = '';

  /**
   * The Secret will automatically be replicated without any restrictions.
   *
   * @param Automatic $automatic
   */
  public function setAutomatic(Automatic $automatic)
  {
    $this->automatic = $automatic;
  }
  /**
   * @return Automatic
   */
  public function getAutomatic()
  {
    return $this->automatic;
  }
  /**
   * The Secret will only be replicated into the locations specified.
   *
   * @param UserManaged $userManaged
   */
  public function setUserManaged(UserManaged $userManaged)
  {
    $this->userManaged = $userManaged;
  }
  /**
   * @return UserManaged
   */
  public function getUserManaged()
  {
    return $this->userManaged;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Replication::class, 'Google_Service_SecretManager_Replication');

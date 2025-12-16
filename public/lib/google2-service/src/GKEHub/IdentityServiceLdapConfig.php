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

namespace Google\Service\GKEHub;

class IdentityServiceLdapConfig extends \Google\Model
{
  protected $groupType = IdentityServiceGroupConfig::class;
  protected $groupDataType = '';
  protected $serverType = IdentityServiceServerConfig::class;
  protected $serverDataType = '';
  protected $serviceAccountType = IdentityServiceServiceAccountConfig::class;
  protected $serviceAccountDataType = '';
  protected $userType = IdentityServiceUserConfig::class;
  protected $userDataType = '';

  /**
   * Optional. Contains the properties for locating and authenticating groups in
   * the directory.
   *
   * @param IdentityServiceGroupConfig $group
   */
  public function setGroup(IdentityServiceGroupConfig $group)
  {
    $this->group = $group;
  }
  /**
   * @return IdentityServiceGroupConfig
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * Required. Server settings for the external LDAP server.
   *
   * @param IdentityServiceServerConfig $server
   */
  public function setServer(IdentityServiceServerConfig $server)
  {
    $this->server = $server;
  }
  /**
   * @return IdentityServiceServerConfig
   */
  public function getServer()
  {
    return $this->server;
  }
  /**
   * Required. Contains the credentials of the service account which is
   * authorized to perform the LDAP search in the directory. The credentials can
   * be supplied by the combination of the DN and password or the client
   * certificate.
   *
   * @param IdentityServiceServiceAccountConfig $serviceAccount
   */
  public function setServiceAccount(IdentityServiceServiceAccountConfig $serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return IdentityServiceServiceAccountConfig
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Required. Defines where users exist in the LDAP directory.
   *
   * @param IdentityServiceUserConfig $user
   */
  public function setUser(IdentityServiceUserConfig $user)
  {
    $this->user = $user;
  }
  /**
   * @return IdentityServiceUserConfig
   */
  public function getUser()
  {
    return $this->user;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentityServiceLdapConfig::class, 'Google_Service_GKEHub_IdentityServiceLdapConfig');

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

namespace Google\Service\CloudFilestore;

class LdapConfig extends \Google\Collection
{
  protected $collection_key = 'servers';
  /**
   * Required. The LDAP domain name in the format of `my-domain.com`.
   *
   * @var string
   */
  public $domain;
  /**
   * Optional. The groups Organizational Unit (OU) is optional. This parameter
   * is a hint to allow faster lookup in the LDAP namespace. In case that this
   * parameter is not provided, Filestore instance will query the whole LDAP
   * namespace.
   *
   * @var string
   */
  public $groupsOu;
  /**
   * Required. The servers names are used for specifying the LDAP servers names.
   * The LDAP servers names can come with two formats: 1. DNS name, for example:
   * `ldap.example1.com`, `ldap.example2.com`. 2. IP address, for example:
   * `10.0.0.1`, `10.0.0.2`, `10.0.0.3`. All servers names must be in the same
   * format: either all DNS names or all IP addresses.
   *
   * @var string[]
   */
  public $servers;
  /**
   * Optional. The users Organizational Unit (OU) is optional. This parameter is
   * a hint to allow faster lookup in the LDAP namespace. In case that this
   * parameter is not provided, Filestore instance will query the whole LDAP
   * namespace.
   *
   * @var string
   */
  public $usersOu;

  /**
   * Required. The LDAP domain name in the format of `my-domain.com`.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Optional. The groups Organizational Unit (OU) is optional. This parameter
   * is a hint to allow faster lookup in the LDAP namespace. In case that this
   * parameter is not provided, Filestore instance will query the whole LDAP
   * namespace.
   *
   * @param string $groupsOu
   */
  public function setGroupsOu($groupsOu)
  {
    $this->groupsOu = $groupsOu;
  }
  /**
   * @return string
   */
  public function getGroupsOu()
  {
    return $this->groupsOu;
  }
  /**
   * Required. The servers names are used for specifying the LDAP servers names.
   * The LDAP servers names can come with two formats: 1. DNS name, for example:
   * `ldap.example1.com`, `ldap.example2.com`. 2. IP address, for example:
   * `10.0.0.1`, `10.0.0.2`, `10.0.0.3`. All servers names must be in the same
   * format: either all DNS names or all IP addresses.
   *
   * @param string[] $servers
   */
  public function setServers($servers)
  {
    $this->servers = $servers;
  }
  /**
   * @return string[]
   */
  public function getServers()
  {
    return $this->servers;
  }
  /**
   * Optional. The users Organizational Unit (OU) is optional. This parameter is
   * a hint to allow faster lookup in the LDAP namespace. In case that this
   * parameter is not provided, Filestore instance will query the whole LDAP
   * namespace.
   *
   * @param string $usersOu
   */
  public function setUsersOu($usersOu)
  {
    $this->usersOu = $usersOu;
  }
  /**
   * @return string
   */
  public function getUsersOu()
  {
    return $this->usersOu;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LdapConfig::class, 'Google_Service_CloudFilestore_LdapConfig');

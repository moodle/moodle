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

namespace Google\Service\SQLAdmin;

class SqlActiveDirectoryConfig extends \Google\Collection
{
  /**
   * Unspecified mode. Will default to MANAGED_ACTIVE_DIRECTORY if the mode is
   * not specified to maintain backward compatibility.
   */
  public const MODE_ACTIVE_DIRECTORY_MODE_UNSPECIFIED = 'ACTIVE_DIRECTORY_MODE_UNSPECIFIED';
  /**
   * Managed Active Directory mode.
   */
  public const MODE_MANAGED_ACTIVE_DIRECTORY = 'MANAGED_ACTIVE_DIRECTORY';
  /**
   * Deprecated: Use CUSTOMER_MANAGED_ACTIVE_DIRECTORY instead.
   *
   * @deprecated
   */
  public const MODE_SELF_MANAGED_ACTIVE_DIRECTORY = 'SELF_MANAGED_ACTIVE_DIRECTORY';
  /**
   * Customer-managed Active Directory mode.
   */
  public const MODE_CUSTOMER_MANAGED_ACTIVE_DIRECTORY = 'CUSTOMER_MANAGED_ACTIVE_DIRECTORY';
  protected $collection_key = 'dnsServers';
  /**
   * Optional. The secret manager key storing the administrator credential.
   * (e.g., projects/{project}/secrets/{secret}).
   *
   * @var string
   */
  public $adminCredentialSecretName;
  /**
   * Optional. Domain controller IPv4 addresses used to bootstrap Active
   * Directory.
   *
   * @var string[]
   */
  public $dnsServers;
  /**
   * The name of the domain (e.g., mydomain.com).
   *
   * @var string
   */
  public $domain;
  /**
   * This is always sql#activeDirectoryConfig.
   *
   * @var string
   */
  public $kind;
  /**
   * Optional. The mode of the Active Directory configuration.
   *
   * @var string
   */
  public $mode;
  /**
   * Optional. The organizational unit distinguished name. This is the full
   * hierarchical path to the organizational unit.
   *
   * @var string
   */
  public $organizationalUnit;

  /**
   * Optional. The secret manager key storing the administrator credential.
   * (e.g., projects/{project}/secrets/{secret}).
   *
   * @param string $adminCredentialSecretName
   */
  public function setAdminCredentialSecretName($adminCredentialSecretName)
  {
    $this->adminCredentialSecretName = $adminCredentialSecretName;
  }
  /**
   * @return string
   */
  public function getAdminCredentialSecretName()
  {
    return $this->adminCredentialSecretName;
  }
  /**
   * Optional. Domain controller IPv4 addresses used to bootstrap Active
   * Directory.
   *
   * @param string[] $dnsServers
   */
  public function setDnsServers($dnsServers)
  {
    $this->dnsServers = $dnsServers;
  }
  /**
   * @return string[]
   */
  public function getDnsServers()
  {
    return $this->dnsServers;
  }
  /**
   * The name of the domain (e.g., mydomain.com).
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
   * This is always sql#activeDirectoryConfig.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Optional. The mode of the Active Directory configuration.
   *
   * Accepted values: ACTIVE_DIRECTORY_MODE_UNSPECIFIED,
   * MANAGED_ACTIVE_DIRECTORY, SELF_MANAGED_ACTIVE_DIRECTORY,
   * CUSTOMER_MANAGED_ACTIVE_DIRECTORY
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Optional. The organizational unit distinguished name. This is the full
   * hierarchical path to the organizational unit.
   *
   * @param string $organizationalUnit
   */
  public function setOrganizationalUnit($organizationalUnit)
  {
    $this->organizationalUnit = $organizationalUnit;
  }
  /**
   * @return string
   */
  public function getOrganizationalUnit()
  {
    return $this->organizationalUnit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlActiveDirectoryConfig::class, 'Google_Service_SQLAdmin_SqlActiveDirectoryConfig');

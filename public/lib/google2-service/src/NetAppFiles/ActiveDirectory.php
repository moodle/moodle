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

namespace Google\Service\NetAppFiles;

class ActiveDirectory extends \Google\Collection
{
  /**
   * Unspecified Active Directory State
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Active Directory State is Creating
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Active Directory State is Ready
   */
  public const STATE_READY = 'READY';
  /**
   * Active Directory State is Updating
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Active Directory State is In use
   */
  public const STATE_IN_USE = 'IN_USE';
  /**
   * Active Directory State is Deleting
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Active Directory State is Error
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Active Directory State is Diagnosing.
   */
  public const STATE_DIAGNOSING = 'DIAGNOSING';
  protected $collection_key = 'securityOperators';
  /**
   * Optional. Users to be added to the Built-in Admininstrators group.
   *
   * @var string[]
   */
  public $administrators;
  /**
   * If enabled, AES encryption will be enabled for SMB communication.
   *
   * @var bool
   */
  public $aesEncryption;
  /**
   * Optional. Users to be added to the Built-in Backup Operator active
   * directory group.
   *
   * @var string[]
   */
  public $backupOperators;
  /**
   * Output only. Create time of the active directory.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of the active directory.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Comma separated list of DNS server IP addresses for the Active
   * Directory domain.
   *
   * @var string
   */
  public $dns;
  /**
   * Required. Name of the Active Directory domain
   *
   * @var string
   */
  public $domain;
  /**
   * If enabled, traffic between the SMB server to Domain Controller (DC) will
   * be encrypted.
   *
   * @var bool
   */
  public $encryptDcConnections;
  /**
   * Name of the active directory machine. This optional parameter is used only
   * while creating kerberos volume
   *
   * @var string
   */
  public $kdcHostname;
  /**
   * KDC server IP address for the active directory machine.
   *
   * @var string
   */
  public $kdcIp;
  /**
   * Labels for the active directory.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Specifies whether or not the LDAP traffic needs to be signed.
   *
   * @var bool
   */
  public $ldapSigning;
  /**
   * Identifier. The resource name of the active directory. Format: `projects/{p
   * roject_number}/locations/{location_id}/activeDirectories/{active_directory_
   * id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. NetBIOSPrefix is used as a prefix for SMB server name.
   *
   * @var string
   */
  public $netBiosPrefix;
  /**
   * If enabled, will allow access to local users and LDAP users. If access is
   * needed for only LDAP users, it has to be disabled.
   *
   * @var bool
   */
  public $nfsUsersWithLdap;
  /**
   * The Organizational Unit (OU) within the Windows Active Directory the user
   * belongs to.
   *
   * @var string
   */
  public $organizationalUnit;
  /**
   * Required. Password of the Active Directory domain administrator.
   *
   * @var string
   */
  public $password;
  /**
   * Optional. Domain users to be given the SeSecurityPrivilege.
   *
   * @var string[]
   */
  public $securityOperators;
  /**
   * The Active Directory site the service will limit Domain Controller
   * discovery too.
   *
   * @var string
   */
  public $site;
  /**
   * Output only. The state of the AD.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The state details of the Active Directory.
   *
   * @var string
   */
  public $stateDetails;
  /**
   * Required. Username of the Active Directory domain administrator.
   *
   * @var string
   */
  public $username;

  /**
   * Optional. Users to be added to the Built-in Admininstrators group.
   *
   * @param string[] $administrators
   */
  public function setAdministrators($administrators)
  {
    $this->administrators = $administrators;
  }
  /**
   * @return string[]
   */
  public function getAdministrators()
  {
    return $this->administrators;
  }
  /**
   * If enabled, AES encryption will be enabled for SMB communication.
   *
   * @param bool $aesEncryption
   */
  public function setAesEncryption($aesEncryption)
  {
    $this->aesEncryption = $aesEncryption;
  }
  /**
   * @return bool
   */
  public function getAesEncryption()
  {
    return $this->aesEncryption;
  }
  /**
   * Optional. Users to be added to the Built-in Backup Operator active
   * directory group.
   *
   * @param string[] $backupOperators
   */
  public function setBackupOperators($backupOperators)
  {
    $this->backupOperators = $backupOperators;
  }
  /**
   * @return string[]
   */
  public function getBackupOperators()
  {
    return $this->backupOperators;
  }
  /**
   * Output only. Create time of the active directory.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Description of the active directory.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Comma separated list of DNS server IP addresses for the Active
   * Directory domain.
   *
   * @param string $dns
   */
  public function setDns($dns)
  {
    $this->dns = $dns;
  }
  /**
   * @return string
   */
  public function getDns()
  {
    return $this->dns;
  }
  /**
   * Required. Name of the Active Directory domain
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
   * If enabled, traffic between the SMB server to Domain Controller (DC) will
   * be encrypted.
   *
   * @param bool $encryptDcConnections
   */
  public function setEncryptDcConnections($encryptDcConnections)
  {
    $this->encryptDcConnections = $encryptDcConnections;
  }
  /**
   * @return bool
   */
  public function getEncryptDcConnections()
  {
    return $this->encryptDcConnections;
  }
  /**
   * Name of the active directory machine. This optional parameter is used only
   * while creating kerberos volume
   *
   * @param string $kdcHostname
   */
  public function setKdcHostname($kdcHostname)
  {
    $this->kdcHostname = $kdcHostname;
  }
  /**
   * @return string
   */
  public function getKdcHostname()
  {
    return $this->kdcHostname;
  }
  /**
   * KDC server IP address for the active directory machine.
   *
   * @param string $kdcIp
   */
  public function setKdcIp($kdcIp)
  {
    $this->kdcIp = $kdcIp;
  }
  /**
   * @return string
   */
  public function getKdcIp()
  {
    return $this->kdcIp;
  }
  /**
   * Labels for the active directory.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Specifies whether or not the LDAP traffic needs to be signed.
   *
   * @param bool $ldapSigning
   */
  public function setLdapSigning($ldapSigning)
  {
    $this->ldapSigning = $ldapSigning;
  }
  /**
   * @return bool
   */
  public function getLdapSigning()
  {
    return $this->ldapSigning;
  }
  /**
   * Identifier. The resource name of the active directory. Format: `projects/{p
   * roject_number}/locations/{location_id}/activeDirectories/{active_directory_
   * id}`.
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
   * Required. NetBIOSPrefix is used as a prefix for SMB server name.
   *
   * @param string $netBiosPrefix
   */
  public function setNetBiosPrefix($netBiosPrefix)
  {
    $this->netBiosPrefix = $netBiosPrefix;
  }
  /**
   * @return string
   */
  public function getNetBiosPrefix()
  {
    return $this->netBiosPrefix;
  }
  /**
   * If enabled, will allow access to local users and LDAP users. If access is
   * needed for only LDAP users, it has to be disabled.
   *
   * @param bool $nfsUsersWithLdap
   */
  public function setNfsUsersWithLdap($nfsUsersWithLdap)
  {
    $this->nfsUsersWithLdap = $nfsUsersWithLdap;
  }
  /**
   * @return bool
   */
  public function getNfsUsersWithLdap()
  {
    return $this->nfsUsersWithLdap;
  }
  /**
   * The Organizational Unit (OU) within the Windows Active Directory the user
   * belongs to.
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
  /**
   * Required. Password of the Active Directory domain administrator.
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * Optional. Domain users to be given the SeSecurityPrivilege.
   *
   * @param string[] $securityOperators
   */
  public function setSecurityOperators($securityOperators)
  {
    $this->securityOperators = $securityOperators;
  }
  /**
   * @return string[]
   */
  public function getSecurityOperators()
  {
    return $this->securityOperators;
  }
  /**
   * The Active Directory site the service will limit Domain Controller
   * discovery too.
   *
   * @param string $site
   */
  public function setSite($site)
  {
    $this->site = $site;
  }
  /**
   * @return string
   */
  public function getSite()
  {
    return $this->site;
  }
  /**
   * Output only. The state of the AD.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, UPDATING, IN_USE,
   * DELETING, ERROR, DIAGNOSING
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
  /**
   * Output only. The state details of the Active Directory.
   *
   * @param string $stateDetails
   */
  public function setStateDetails($stateDetails)
  {
    $this->stateDetails = $stateDetails;
  }
  /**
   * @return string
   */
  public function getStateDetails()
  {
    return $this->stateDetails;
  }
  /**
   * Required. Username of the Active Directory domain administrator.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActiveDirectory::class, 'Google_Service_NetAppFiles_ActiveDirectory');

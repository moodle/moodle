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

namespace Google\Service\CCAIPlatform;

class ContactCenter extends \Google\Collection
{
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * State DEPLOYING
   */
  public const STATE_STATE_DEPLOYING = 'STATE_DEPLOYING';
  /**
   * State DEPLOYED
   */
  public const STATE_STATE_DEPLOYED = 'STATE_DEPLOYED';
  /**
   * State TERMINATING
   */
  public const STATE_STATE_TERMINATING = 'STATE_TERMINATING';
  /**
   * State FAILED
   */
  public const STATE_STATE_FAILED = 'STATE_FAILED';
  /**
   * State TERMINATING_FAILED
   */
  public const STATE_STATE_TERMINATING_FAILED = 'STATE_TERMINATING_FAILED';
  /**
   * State TERMINATED
   */
  public const STATE_STATE_TERMINATED = 'STATE_TERMINATED';
  /**
   * State IN_GRACE_PERIOD
   */
  public const STATE_STATE_IN_GRACE_PERIOD = 'STATE_IN_GRACE_PERIOD';
  /**
   * State in STATE_FAILING_OVER. This State must ONLY be used by Multiregional
   * Instances when a failover was triggered. Customers are not able to update
   * instances in this state.
   */
  public const STATE_STATE_FAILING_OVER = 'STATE_FAILING_OVER';
  /**
   * State DEGRADED. This State must ONLY be used by Multiregional Instances
   * after a failover was executed successfully. Customers are not able to
   * update instances in this state.
   */
  public const STATE_STATE_DEGRADED = 'STATE_DEGRADED';
  /**
   * State REPAIRING. This State must ONLY be used by Multiregional Instances
   * after a fallback was triggered. Customers are not able to update instancs
   * in this state.
   */
  public const STATE_STATE_REPAIRING = 'STATE_REPAIRING';
  protected $collection_key = 'privateComponents';
  protected $adminUserType = AdminUser::class;
  protected $adminUserDataType = '';
  /**
   * Optional. Whether the advanced reporting feature is enabled.
   *
   * @var bool
   */
  public $advancedReportingEnabled;
  /**
   * Optional. Whether to enable users to be created in the CCAIP-instance
   * concurrently to having users in Cloud identity
   *
   * @var bool
   */
  public $ccaipManagedUsers;
  /**
   * Output only. [Output only] Create time stamp
   *
   * @var string
   */
  public $createTime;
  protected $criticalType = Critical::class;
  protected $criticalDataType = '';
  /**
   * Required. Immutable. At least 2 and max 16 char long, must conform to [RFC
   * 1035](https://www.ietf.org/rfc/rfc1035.txt).
   *
   * @var string
   */
  public $customerDomainPrefix;
  /**
   * Required. A user friendly name for the ContactCenter.
   *
   * @var string
   */
  public $displayName;
  protected $earlyType = Early::class;
  protected $earlyDataType = '';
  protected $featureConfigType = FeatureConfig::class;
  protected $featureConfigDataType = '';
  protected $instanceConfigType = InstanceConfig::class;
  protected $instanceConfigDataType = '';
  /**
   * Immutable. The KMS key name to encrypt the user input (`ContactCenter`).
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  /**
   * name of resource
   *
   * @var string
   */
  public $name;
  protected $normalType = Normal::class;
  protected $normalDataType = '';
  protected $privateAccessType = PrivateAccess::class;
  protected $privateAccessDataType = '';
  /**
   * Output only. TODO(b/283407860) Deprecate this field.
   *
   * @var string[]
   */
  public $privateComponents;
  /**
   * Output only. UJET release version, unique for each new release.
   *
   * @var string
   */
  public $releaseVersion;
  protected $samlParamsType = SAMLParams::class;
  protected $samlParamsDataType = '';
  /**
   * Output only. The state of this contact center.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. [Output only] Update time stamp
   *
   * @var string
   */
  public $updateTime;
  protected $urisType = URIs::class;
  protected $urisDataType = '';
  /**
   * Optional. Email address of the first admin user.
   *
   * @var string
   */
  public $userEmail;

  /**
   * Optional. Info about the first admin user, such as given name and family
   * name.
   *
   * @param AdminUser $adminUser
   */
  public function setAdminUser(AdminUser $adminUser)
  {
    $this->adminUser = $adminUser;
  }
  /**
   * @return AdminUser
   */
  public function getAdminUser()
  {
    return $this->adminUser;
  }
  /**
   * Optional. Whether the advanced reporting feature is enabled.
   *
   * @param bool $advancedReportingEnabled
   */
  public function setAdvancedReportingEnabled($advancedReportingEnabled)
  {
    $this->advancedReportingEnabled = $advancedReportingEnabled;
  }
  /**
   * @return bool
   */
  public function getAdvancedReportingEnabled()
  {
    return $this->advancedReportingEnabled;
  }
  /**
   * Optional. Whether to enable users to be created in the CCAIP-instance
   * concurrently to having users in Cloud identity
   *
   * @param bool $ccaipManagedUsers
   */
  public function setCcaipManagedUsers($ccaipManagedUsers)
  {
    $this->ccaipManagedUsers = $ccaipManagedUsers;
  }
  /**
   * @return bool
   */
  public function getCcaipManagedUsers()
  {
    return $this->ccaipManagedUsers;
  }
  /**
   * Output only. [Output only] Create time stamp
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
   * Optional. Critical release channel.
   *
   * @param Critical $critical
   */
  public function setCritical(Critical $critical)
  {
    $this->critical = $critical;
  }
  /**
   * @return Critical
   */
  public function getCritical()
  {
    return $this->critical;
  }
  /**
   * Required. Immutable. At least 2 and max 16 char long, must conform to [RFC
   * 1035](https://www.ietf.org/rfc/rfc1035.txt).
   *
   * @param string $customerDomainPrefix
   */
  public function setCustomerDomainPrefix($customerDomainPrefix)
  {
    $this->customerDomainPrefix = $customerDomainPrefix;
  }
  /**
   * @return string
   */
  public function getCustomerDomainPrefix()
  {
    return $this->customerDomainPrefix;
  }
  /**
   * Required. A user friendly name for the ContactCenter.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Early release channel.
   *
   * @param Early $early
   */
  public function setEarly(Early $early)
  {
    $this->early = $early;
  }
  /**
   * @return Early
   */
  public function getEarly()
  {
    return $this->early;
  }
  /**
   * Optional. Feature configuration to populate the feature flags.
   *
   * @param FeatureConfig $featureConfig
   */
  public function setFeatureConfig(FeatureConfig $featureConfig)
  {
    $this->featureConfig = $featureConfig;
  }
  /**
   * @return FeatureConfig
   */
  public function getFeatureConfig()
  {
    return $this->featureConfig;
  }
  /**
   * The configuration of this instance, it is currently immutable once created.
   *
   * @param InstanceConfig $instanceConfig
   */
  public function setInstanceConfig(InstanceConfig $instanceConfig)
  {
    $this->instanceConfig = $instanceConfig;
  }
  /**
   * @return InstanceConfig
   */
  public function getInstanceConfig()
  {
    return $this->instanceConfig;
  }
  /**
   * Immutable. The KMS key name to encrypt the user input (`ContactCenter`).
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Labels as key value pairs
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
   * name of resource
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
   * Optional. Normal release channel.
   *
   * @param Normal $normal
   */
  public function setNormal(Normal $normal)
  {
    $this->normal = $normal;
  }
  /**
   * @return Normal
   */
  public function getNormal()
  {
    return $this->normal;
  }
  /**
   * Optional. VPC-SC related networking configuration.
   *
   * @param PrivateAccess $privateAccess
   */
  public function setPrivateAccess(PrivateAccess $privateAccess)
  {
    $this->privateAccess = $privateAccess;
  }
  /**
   * @return PrivateAccess
   */
  public function getPrivateAccess()
  {
    return $this->privateAccess;
  }
  /**
   * Output only. TODO(b/283407860) Deprecate this field.
   *
   * @param string[] $privateComponents
   */
  public function setPrivateComponents($privateComponents)
  {
    $this->privateComponents = $privateComponents;
  }
  /**
   * @return string[]
   */
  public function getPrivateComponents()
  {
    return $this->privateComponents;
  }
  /**
   * Output only. UJET release version, unique for each new release.
   *
   * @param string $releaseVersion
   */
  public function setReleaseVersion($releaseVersion)
  {
    $this->releaseVersion = $releaseVersion;
  }
  /**
   * @return string
   */
  public function getReleaseVersion()
  {
    return $this->releaseVersion;
  }
  /**
   * Optional. Params that sets up Google as IdP.
   *
   * @param SAMLParams $samlParams
   */
  public function setSamlParams(SAMLParams $samlParams)
  {
    $this->samlParams = $samlParams;
  }
  /**
   * @return SAMLParams
   */
  public function getSamlParams()
  {
    return $this->samlParams;
  }
  /**
   * Output only. The state of this contact center.
   *
   * Accepted values: STATE_UNSPECIFIED, STATE_DEPLOYING, STATE_DEPLOYED,
   * STATE_TERMINATING, STATE_FAILED, STATE_TERMINATING_FAILED,
   * STATE_TERMINATED, STATE_IN_GRACE_PERIOD, STATE_FAILING_OVER,
   * STATE_DEGRADED, STATE_REPAIRING
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
   * Output only. [Output only] Update time stamp
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. URIs to access the deployed ContactCenters.
   *
   * @param URIs $uris
   */
  public function setUris(URIs $uris)
  {
    $this->uris = $uris;
  }
  /**
   * @return URIs
   */
  public function getUris()
  {
    return $this->uris;
  }
  /**
   * Optional. Email address of the first admin user.
   *
   * @param string $userEmail
   */
  public function setUserEmail($userEmail)
  {
    $this->userEmail = $userEmail;
  }
  /**
   * @return string
   */
  public function getUserEmail()
  {
    return $this->userEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContactCenter::class, 'Google_Service_CCAIPlatform_ContactCenter');

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

namespace Google\Service\Looker;

class Instance extends \Google\Model
{
  /**
   * Unspecified storage class.
   */
  public const CLASS_TYPE_CLASS_TYPE_UNSPECIFIED = 'CLASS_TYPE_UNSPECIFIED';
  /**
   * Filestore.
   */
  public const CLASS_TYPE_R1 = 'R1';
  /**
   * PD SSD.
   */
  public const CLASS_TYPE_P1 = 'P1';
  /**
   * Platform edition is unspecified.
   */
  public const PLATFORM_EDITION_PLATFORM_EDITION_UNSPECIFIED = 'PLATFORM_EDITION_UNSPECIFIED';
  /**
   * Trial.
   */
  public const PLATFORM_EDITION_LOOKER_CORE_TRIAL = 'LOOKER_CORE_TRIAL';
  /**
   * Standard.
   */
  public const PLATFORM_EDITION_LOOKER_CORE_STANDARD = 'LOOKER_CORE_STANDARD';
  /**
   * Subscription Standard.
   */
  public const PLATFORM_EDITION_LOOKER_CORE_STANDARD_ANNUAL = 'LOOKER_CORE_STANDARD_ANNUAL';
  /**
   * Subscription Enterprise.
   */
  public const PLATFORM_EDITION_LOOKER_CORE_ENTERPRISE_ANNUAL = 'LOOKER_CORE_ENTERPRISE_ANNUAL';
  /**
   * Subscription Embed.
   */
  public const PLATFORM_EDITION_LOOKER_CORE_EMBED_ANNUAL = 'LOOKER_CORE_EMBED_ANNUAL';
  /**
   * Nonprod Subscription Standard.
   */
  public const PLATFORM_EDITION_LOOKER_CORE_NONPROD_STANDARD_ANNUAL = 'LOOKER_CORE_NONPROD_STANDARD_ANNUAL';
  /**
   * Nonprod Subscription Enterprise.
   */
  public const PLATFORM_EDITION_LOOKER_CORE_NONPROD_ENTERPRISE_ANNUAL = 'LOOKER_CORE_NONPROD_ENTERPRISE_ANNUAL';
  /**
   * Nonprod Subscription Embed.
   */
  public const PLATFORM_EDITION_LOOKER_CORE_NONPROD_EMBED_ANNUAL = 'LOOKER_CORE_NONPROD_EMBED_ANNUAL';
  /**
   * Trial Standard.
   */
  public const PLATFORM_EDITION_LOOKER_CORE_TRIAL_STANDARD = 'LOOKER_CORE_TRIAL_STANDARD';
  /**
   * Trial Enterprise.
   */
  public const PLATFORM_EDITION_LOOKER_CORE_TRIAL_ENTERPRISE = 'LOOKER_CORE_TRIAL_ENTERPRISE';
  /**
   * Trial Embed.
   */
  public const PLATFORM_EDITION_LOOKER_CORE_TRIAL_EMBED = 'LOOKER_CORE_TRIAL_EMBED';
  /**
   * State is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Instance is active and ready for use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Instance provisioning is in progress.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Instance is in a failed state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Instance was suspended.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * Instance update is in progress.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Instance delete is in progress.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Instance is being exported.
   */
  public const STATE_EXPORTING = 'EXPORTING';
  /**
   * Instance is importing data.
   */
  public const STATE_IMPORTING = 'IMPORTING';
  protected $adminSettingsType = AdminSettings::class;
  protected $adminSettingsDataType = '';
  /**
   * Optional. Storage class of the instance.
   *
   * @var string
   */
  public $classType;
  /**
   * Network name in the consumer project. Format:
   * `projects/{project}/global/networks/{network}`. Note that the consumer
   * network may be in a different GCP project than the consumer project that is
   * hosting the Looker Instance.
   *
   * @var string
   */
  public $consumerNetwork;
  protected $controlledEgressConfigType = ControlledEgressConfig::class;
  protected $controlledEgressConfigDataType = '';
  /**
   * Optional. Whether controlled egress is enabled on the Looker instance.
   *
   * @var bool
   */
  public $controlledEgressEnabled;
  /**
   * Output only. The time when the Looker instance provisioning was first
   * requested.
   *
   * @var string
   */
  public $createTime;
  protected $customDomainType = CustomDomain::class;
  protected $customDomainDataType = '';
  protected $denyMaintenancePeriodType = DenyMaintenancePeriod::class;
  protected $denyMaintenancePeriodDataType = '';
  /**
   * Output only. Public Egress IP (IPv4).
   *
   * @var string
   */
  public $egressPublicIp;
  protected $encryptionConfigType = EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  /**
   * Optional. Whether FIPS is enabled on the Looker instance.
   *
   * @var bool
   */
  public $fipsEnabled;
  /**
   * Optional. Whether Gemini feature is enabled on the Looker instance or not.
   *
   * @var bool
   */
  public $geminiEnabled;
  /**
   * Output only. Private Ingress IP (IPv4).
   *
   * @var string
   */
  public $ingressPrivateIp;
  /**
   * Output only. Public Ingress IP (IPv4).
   *
   * @var string
   */
  public $ingressPublicIp;
  protected $lastDenyMaintenancePeriodType = DenyMaintenancePeriod::class;
  protected $lastDenyMaintenancePeriodDataType = '';
  /**
   * Optional. Linked Google Cloud Project Number for Looker Studio Pro.
   *
   * @var string
   */
  public $linkedLspProjectNumber;
  /**
   * Output only. Looker instance URI which can be used to access the Looker
   * Instance UI.
   *
   * @var string
   */
  public $lookerUri;
  /**
   * Output only. The Looker version that the instance is using.
   *
   * @var string
   */
  public $lookerVersion;
  protected $maintenanceScheduleType = MaintenanceSchedule::class;
  protected $maintenanceScheduleDataType = '';
  protected $maintenanceWindowType = MaintenanceWindow::class;
  protected $maintenanceWindowDataType = '';
  /**
   * Output only. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   *
   * @var string
   */
  public $name;
  protected $oauthConfigType = OAuthConfig::class;
  protected $oauthConfigDataType = '';
  protected $periodicExportConfigType = PeriodicExportConfig::class;
  protected $periodicExportConfigDataType = '';
  /**
   * Platform edition.
   *
   * @var string
   */
  public $platformEdition;
  /**
   * Whether private IP is enabled on the Looker instance.
   *
   * @var bool
   */
  public $privateIpEnabled;
  protected $pscConfigType = PscConfig::class;
  protected $pscConfigDataType = '';
  /**
   * Optional. Whether to use Private Service Connect (PSC) for private IP
   * connectivity. If true, neither `public_ip_enabled` nor `private_ip_enabled`
   * can be true.
   *
   * @var bool
   */
  public $pscEnabled;
  /**
   * Whether public IP is enabled on the Looker instance.
   *
   * @var bool
   */
  public $publicIpEnabled;
  /**
   * Name of a reserved IP address range within the Instance.consumer_network,
   * to be used for private services access connection. May or may not be
   * specified in a create request.
   *
   * @var string
   */
  public $reservedRange;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The state of the instance.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time when the Looker instance was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $userMetadataType = UserMetadata::class;
  protected $userMetadataDataType = '';

  /**
   * Looker Instance Admin settings.
   *
   * @param AdminSettings $adminSettings
   */
  public function setAdminSettings(AdminSettings $adminSettings)
  {
    $this->adminSettings = $adminSettings;
  }
  /**
   * @return AdminSettings
   */
  public function getAdminSettings()
  {
    return $this->adminSettings;
  }
  /**
   * Optional. Storage class of the instance.
   *
   * Accepted values: CLASS_TYPE_UNSPECIFIED, R1, P1
   *
   * @param self::CLASS_TYPE_* $classType
   */
  public function setClassType($classType)
  {
    $this->classType = $classType;
  }
  /**
   * @return self::CLASS_TYPE_*
   */
  public function getClassType()
  {
    return $this->classType;
  }
  /**
   * Network name in the consumer project. Format:
   * `projects/{project}/global/networks/{network}`. Note that the consumer
   * network may be in a different GCP project than the consumer project that is
   * hosting the Looker Instance.
   *
   * @param string $consumerNetwork
   */
  public function setConsumerNetwork($consumerNetwork)
  {
    $this->consumerNetwork = $consumerNetwork;
  }
  /**
   * @return string
   */
  public function getConsumerNetwork()
  {
    return $this->consumerNetwork;
  }
  /**
   * Optional. Controlled egress configuration.
   *
   * @param ControlledEgressConfig $controlledEgressConfig
   */
  public function setControlledEgressConfig(ControlledEgressConfig $controlledEgressConfig)
  {
    $this->controlledEgressConfig = $controlledEgressConfig;
  }
  /**
   * @return ControlledEgressConfig
   */
  public function getControlledEgressConfig()
  {
    return $this->controlledEgressConfig;
  }
  /**
   * Optional. Whether controlled egress is enabled on the Looker instance.
   *
   * @param bool $controlledEgressEnabled
   */
  public function setControlledEgressEnabled($controlledEgressEnabled)
  {
    $this->controlledEgressEnabled = $controlledEgressEnabled;
  }
  /**
   * @return bool
   */
  public function getControlledEgressEnabled()
  {
    return $this->controlledEgressEnabled;
  }
  /**
   * Output only. The time when the Looker instance provisioning was first
   * requested.
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
   * Custom domain configuration for the instance.
   *
   * @param CustomDomain $customDomain
   */
  public function setCustomDomain(CustomDomain $customDomain)
  {
    $this->customDomain = $customDomain;
  }
  /**
   * @return CustomDomain
   */
  public function getCustomDomain()
  {
    return $this->customDomain;
  }
  /**
   * Maintenance denial period for this instance.
   *
   * @param DenyMaintenancePeriod $denyMaintenancePeriod
   */
  public function setDenyMaintenancePeriod(DenyMaintenancePeriod $denyMaintenancePeriod)
  {
    $this->denyMaintenancePeriod = $denyMaintenancePeriod;
  }
  /**
   * @return DenyMaintenancePeriod
   */
  public function getDenyMaintenancePeriod()
  {
    return $this->denyMaintenancePeriod;
  }
  /**
   * Output only. Public Egress IP (IPv4).
   *
   * @param string $egressPublicIp
   */
  public function setEgressPublicIp($egressPublicIp)
  {
    $this->egressPublicIp = $egressPublicIp;
  }
  /**
   * @return string
   */
  public function getEgressPublicIp()
  {
    return $this->egressPublicIp;
  }
  /**
   * Encryption configuration (CMEK). Only set if CMEK has been enabled on the
   * instance.
   *
   * @param EncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(EncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return EncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * Optional. Whether FIPS is enabled on the Looker instance.
   *
   * @param bool $fipsEnabled
   */
  public function setFipsEnabled($fipsEnabled)
  {
    $this->fipsEnabled = $fipsEnabled;
  }
  /**
   * @return bool
   */
  public function getFipsEnabled()
  {
    return $this->fipsEnabled;
  }
  /**
   * Optional. Whether Gemini feature is enabled on the Looker instance or not.
   *
   * @param bool $geminiEnabled
   */
  public function setGeminiEnabled($geminiEnabled)
  {
    $this->geminiEnabled = $geminiEnabled;
  }
  /**
   * @return bool
   */
  public function getGeminiEnabled()
  {
    return $this->geminiEnabled;
  }
  /**
   * Output only. Private Ingress IP (IPv4).
   *
   * @param string $ingressPrivateIp
   */
  public function setIngressPrivateIp($ingressPrivateIp)
  {
    $this->ingressPrivateIp = $ingressPrivateIp;
  }
  /**
   * @return string
   */
  public function getIngressPrivateIp()
  {
    return $this->ingressPrivateIp;
  }
  /**
   * Output only. Public Ingress IP (IPv4).
   *
   * @param string $ingressPublicIp
   */
  public function setIngressPublicIp($ingressPublicIp)
  {
    $this->ingressPublicIp = $ingressPublicIp;
  }
  /**
   * @return string
   */
  public function getIngressPublicIp()
  {
    return $this->ingressPublicIp;
  }
  /**
   * Output only. Last computed maintenance denial period for this instance.
   *
   * @param DenyMaintenancePeriod $lastDenyMaintenancePeriod
   */
  public function setLastDenyMaintenancePeriod(DenyMaintenancePeriod $lastDenyMaintenancePeriod)
  {
    $this->lastDenyMaintenancePeriod = $lastDenyMaintenancePeriod;
  }
  /**
   * @return DenyMaintenancePeriod
   */
  public function getLastDenyMaintenancePeriod()
  {
    return $this->lastDenyMaintenancePeriod;
  }
  /**
   * Optional. Linked Google Cloud Project Number for Looker Studio Pro.
   *
   * @param string $linkedLspProjectNumber
   */
  public function setLinkedLspProjectNumber($linkedLspProjectNumber)
  {
    $this->linkedLspProjectNumber = $linkedLspProjectNumber;
  }
  /**
   * @return string
   */
  public function getLinkedLspProjectNumber()
  {
    return $this->linkedLspProjectNumber;
  }
  /**
   * Output only. Looker instance URI which can be used to access the Looker
   * Instance UI.
   *
   * @param string $lookerUri
   */
  public function setLookerUri($lookerUri)
  {
    $this->lookerUri = $lookerUri;
  }
  /**
   * @return string
   */
  public function getLookerUri()
  {
    return $this->lookerUri;
  }
  /**
   * Output only. The Looker version that the instance is using.
   *
   * @param string $lookerVersion
   */
  public function setLookerVersion($lookerVersion)
  {
    $this->lookerVersion = $lookerVersion;
  }
  /**
   * @return string
   */
  public function getLookerVersion()
  {
    return $this->lookerVersion;
  }
  /**
   * Maintenance schedule for this instance.
   *
   * @param MaintenanceSchedule $maintenanceSchedule
   */
  public function setMaintenanceSchedule(MaintenanceSchedule $maintenanceSchedule)
  {
    $this->maintenanceSchedule = $maintenanceSchedule;
  }
  /**
   * @return MaintenanceSchedule
   */
  public function getMaintenanceSchedule()
  {
    return $this->maintenanceSchedule;
  }
  /**
   * Maintenance window for this instance.
   *
   * @param MaintenanceWindow $maintenanceWindow
   */
  public function setMaintenanceWindow(MaintenanceWindow $maintenanceWindow)
  {
    $this->maintenanceWindow = $maintenanceWindow;
  }
  /**
   * @return MaintenanceWindow
   */
  public function getMaintenanceWindow()
  {
    return $this->maintenanceWindow;
  }
  /**
   * Output only. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
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
   * Looker instance OAuth login settings.
   *
   * @param OAuthConfig $oauthConfig
   */
  public function setOauthConfig(OAuthConfig $oauthConfig)
  {
    $this->oauthConfig = $oauthConfig;
  }
  /**
   * @return OAuthConfig
   */
  public function getOauthConfig()
  {
    return $this->oauthConfig;
  }
  /**
   * Optional. Configuration for periodic export.
   *
   * @param PeriodicExportConfig $periodicExportConfig
   */
  public function setPeriodicExportConfig(PeriodicExportConfig $periodicExportConfig)
  {
    $this->periodicExportConfig = $periodicExportConfig;
  }
  /**
   * @return PeriodicExportConfig
   */
  public function getPeriodicExportConfig()
  {
    return $this->periodicExportConfig;
  }
  /**
   * Platform edition.
   *
   * Accepted values: PLATFORM_EDITION_UNSPECIFIED, LOOKER_CORE_TRIAL,
   * LOOKER_CORE_STANDARD, LOOKER_CORE_STANDARD_ANNUAL,
   * LOOKER_CORE_ENTERPRISE_ANNUAL, LOOKER_CORE_EMBED_ANNUAL,
   * LOOKER_CORE_NONPROD_STANDARD_ANNUAL, LOOKER_CORE_NONPROD_ENTERPRISE_ANNUAL,
   * LOOKER_CORE_NONPROD_EMBED_ANNUAL, LOOKER_CORE_TRIAL_STANDARD,
   * LOOKER_CORE_TRIAL_ENTERPRISE, LOOKER_CORE_TRIAL_EMBED
   *
   * @param self::PLATFORM_EDITION_* $platformEdition
   */
  public function setPlatformEdition($platformEdition)
  {
    $this->platformEdition = $platformEdition;
  }
  /**
   * @return self::PLATFORM_EDITION_*
   */
  public function getPlatformEdition()
  {
    return $this->platformEdition;
  }
  /**
   * Whether private IP is enabled on the Looker instance.
   *
   * @param bool $privateIpEnabled
   */
  public function setPrivateIpEnabled($privateIpEnabled)
  {
    $this->privateIpEnabled = $privateIpEnabled;
  }
  /**
   * @return bool
   */
  public function getPrivateIpEnabled()
  {
    return $this->privateIpEnabled;
  }
  /**
   * Optional. PSC configuration. Used when `psc_enabled` is true.
   *
   * @param PscConfig $pscConfig
   */
  public function setPscConfig(PscConfig $pscConfig)
  {
    $this->pscConfig = $pscConfig;
  }
  /**
   * @return PscConfig
   */
  public function getPscConfig()
  {
    return $this->pscConfig;
  }
  /**
   * Optional. Whether to use Private Service Connect (PSC) for private IP
   * connectivity. If true, neither `public_ip_enabled` nor `private_ip_enabled`
   * can be true.
   *
   * @param bool $pscEnabled
   */
  public function setPscEnabled($pscEnabled)
  {
    $this->pscEnabled = $pscEnabled;
  }
  /**
   * @return bool
   */
  public function getPscEnabled()
  {
    return $this->pscEnabled;
  }
  /**
   * Whether public IP is enabled on the Looker instance.
   *
   * @param bool $publicIpEnabled
   */
  public function setPublicIpEnabled($publicIpEnabled)
  {
    $this->publicIpEnabled = $publicIpEnabled;
  }
  /**
   * @return bool
   */
  public function getPublicIpEnabled()
  {
    return $this->publicIpEnabled;
  }
  /**
   * Name of a reserved IP address range within the Instance.consumer_network,
   * to be used for private services access connection. May or may not be
   * specified in a create request.
   *
   * @param string $reservedRange
   */
  public function setReservedRange($reservedRange)
  {
    $this->reservedRange = $reservedRange;
  }
  /**
   * @return string
   */
  public function getReservedRange()
  {
    return $this->reservedRange;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. The state of the instance.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, FAILED, SUSPENDED,
   * UPDATING, DELETING, EXPORTING, IMPORTING
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
   * Output only. The time when the Looker instance was last updated.
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
   * Optional. User metadata.
   *
   * @param UserMetadata $userMetadata
   */
  public function setUserMetadata(UserMetadata $userMetadata)
  {
    $this->userMetadata = $userMetadata;
  }
  /**
   * @return UserMetadata
   */
  public function getUserMetadata()
  {
    return $this->userMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instance::class, 'Google_Service_Looker_Instance');

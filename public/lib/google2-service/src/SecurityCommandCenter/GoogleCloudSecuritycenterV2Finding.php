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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2Finding extends \Google\Collection
{
  /**
   * Unspecified finding class.
   */
  public const FINDING_CLASS_FINDING_CLASS_UNSPECIFIED = 'FINDING_CLASS_UNSPECIFIED';
  /**
   * Describes unwanted or malicious activity.
   */
  public const FINDING_CLASS_THREAT = 'THREAT';
  /**
   * Describes a potential weakness in software that increases risk to
   * Confidentiality & Integrity & Availability.
   */
  public const FINDING_CLASS_VULNERABILITY = 'VULNERABILITY';
  /**
   * Describes a potential weakness in cloud resource/asset configuration that
   * increases risk.
   */
  public const FINDING_CLASS_MISCONFIGURATION = 'MISCONFIGURATION';
  /**
   * Describes a security observation that is for informational purposes.
   */
  public const FINDING_CLASS_OBSERVATION = 'OBSERVATION';
  /**
   * Describes an error that prevents some SCC functionality.
   */
  public const FINDING_CLASS_SCC_ERROR = 'SCC_ERROR';
  /**
   * Describes a potential security risk due to a change in the security
   * posture.
   */
  public const FINDING_CLASS_POSTURE_VIOLATION = 'POSTURE_VIOLATION';
  /**
   * Describes a combination of security issues that represent a more severe
   * security problem when taken together.
   */
  public const FINDING_CLASS_TOXIC_COMBINATION = 'TOXIC_COMBINATION';
  /**
   * Describes a potential security risk to data assets that contain sensitive
   * data.
   */
  public const FINDING_CLASS_SENSITIVE_DATA_RISK = 'SENSITIVE_DATA_RISK';
  /**
   * Describes a resource or resource group where high risk attack paths
   * converge, based on attack path simulations (APS).
   */
  public const FINDING_CLASS_CHOKEPOINT = 'CHOKEPOINT';
  /**
   * Unspecified.
   */
  public const MUTE_MUTE_UNSPECIFIED = 'MUTE_UNSPECIFIED';
  /**
   * Finding has been muted.
   */
  public const MUTE_MUTED = 'MUTED';
  /**
   * Finding has been unmuted.
   */
  public const MUTE_UNMUTED = 'UNMUTED';
  /**
   * Finding has never been muted/unmuted.
   */
  public const MUTE_UNDEFINED = 'UNDEFINED';
  /**
   * This value is used for findings when a source doesn't write a severity
   * value.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Vulnerability: A critical vulnerability is easily discoverable by an
   * external actor, exploitable, and results in the direct ability to execute
   * arbitrary code, exfiltrate data, and otherwise gain additional access and
   * privileges to cloud resources and workloads. Examples include publicly
   * accessible unprotected user data and public SSH access with weak or no
   * passwords. Threat: Indicates a threat that is able to access, modify, or
   * delete data or execute unauthorized code within existing resources.
   */
  public const SEVERITY_CRITICAL = 'CRITICAL';
  /**
   * Vulnerability: A high risk vulnerability can be easily discovered and
   * exploited in combination with other vulnerabilities in order to gain direct
   * access and the ability to execute arbitrary code, exfiltrate data, and
   * otherwise gain additional access and privileges to cloud resources and
   * workloads. An example is a database with weak or no passwords that is only
   * accessible internally. This database could easily be compromised by an
   * actor that had access to the internal network. Threat: Indicates a threat
   * that is able to create new computational resources in an environment but
   * not able to access data or execute code in existing resources.
   */
  public const SEVERITY_HIGH = 'HIGH';
  /**
   * Vulnerability: A medium risk vulnerability could be used by an actor to
   * gain access to resources or privileges that enable them to eventually
   * (through multiple steps or a complex exploit) gain access and the ability
   * to execute arbitrary code or exfiltrate data. An example is a service
   * account with access to more projects than it should have. If an actor gains
   * access to the service account, they could potentially use that access to
   * manipulate a project the service account was not intended to. Threat:
   * Indicates a threat that is able to cause operational impact but may not
   * access data or execute unauthorized code.
   */
  public const SEVERITY_MEDIUM = 'MEDIUM';
  /**
   * Vulnerability: A low risk vulnerability hampers a security organization's
   * ability to detect vulnerabilities or active threats in their deployment, or
   * prevents the root cause investigation of security issues. An example is
   * monitoring and logs being disabled for resource configurations and access.
   * Threat: Indicates a threat that has obtained minimal access to an
   * environment but is not able to access data, execute code, or create
   * resources.
   */
  public const SEVERITY_LOW = 'LOW';
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The finding requires attention and has not been addressed yet.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The finding has been fixed, triaged as a non-issue or otherwise addressed
   * and is no longer active.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  protected $collection_key = 'processes';
  protected $accessType = GoogleCloudSecuritycenterV2Access::class;
  protected $accessDataType = '';
  protected $affectedResourcesType = GoogleCloudSecuritycenterV2AffectedResources::class;
  protected $affectedResourcesDataType = '';
  protected $aiModelType = GoogleCloudSecuritycenterV2AiModel::class;
  protected $aiModelDataType = '';
  protected $applicationType = GoogleCloudSecuritycenterV2Application::class;
  protected $applicationDataType = '';
  protected $attackExposureType = GoogleCloudSecuritycenterV2AttackExposure::class;
  protected $attackExposureDataType = '';
  protected $backupDisasterRecoveryType = GoogleCloudSecuritycenterV2BackupDisasterRecovery::class;
  protected $backupDisasterRecoveryDataType = '';
  /**
   * Output only. The canonical name of the finding. The following list shows
   * some examples: + `organizations/{organization_id}/sources/{source_id}/locat
   * ions/{location_id}/findings/{finding_id}` + `folders/{folder_id}/sources/{s
   * ource_id}/locations/{location_id}/findings/{finding_id}` + `projects/{proje
   * ct_id}/sources/{source_id}/locations/{location_id}/findings/{finding_id}`
   * The prefix is the closest CRM ancestor of the resource associated with the
   * finding.
   *
   * @var string
   */
  public $canonicalName;
  /**
   * Immutable. The additional taxonomy group within findings from a given
   * source. Example: "XSS_FLASH_INJECTION"
   *
   * @var string
   */
  public $category;
  protected $chokepointType = GoogleCloudSecuritycenterV2Chokepoint::class;
  protected $chokepointDataType = '';
  protected $cloudArmorType = GoogleCloudSecuritycenterV2CloudArmor::class;
  protected $cloudArmorDataType = '';
  protected $cloudDlpDataProfileType = GoogleCloudSecuritycenterV2CloudDlpDataProfile::class;
  protected $cloudDlpDataProfileDataType = '';
  protected $cloudDlpInspectionType = GoogleCloudSecuritycenterV2CloudDlpInspection::class;
  protected $cloudDlpInspectionDataType = '';
  protected $complianceDetailsType = GoogleCloudSecuritycenterV2ComplianceDetails::class;
  protected $complianceDetailsDataType = '';
  protected $compliancesType = GoogleCloudSecuritycenterV2Compliance::class;
  protected $compliancesDataType = 'array';
  protected $connectionsType = GoogleCloudSecuritycenterV2Connection::class;
  protected $connectionsDataType = 'array';
  protected $contactsType = GoogleCloudSecuritycenterV2ContactDetails::class;
  protected $contactsDataType = 'map';
  protected $containersType = GoogleCloudSecuritycenterV2Container::class;
  protected $containersDataType = 'array';
  /**
   * Output only. The time at which the finding was created in Security Command
   * Center.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The name of the Cloud KMS key used to encrypt this finding, if
   * any.
   *
   * @var string
   */
  public $cryptoKeyName;
  protected $dataAccessEventsType = GoogleCloudSecuritycenterV2DataAccessEvent::class;
  protected $dataAccessEventsDataType = 'array';
  protected $dataFlowEventsType = GoogleCloudSecuritycenterV2DataFlowEvent::class;
  protected $dataFlowEventsDataType = 'array';
  protected $dataRetentionDeletionEventsType = GoogleCloudSecuritycenterV2DataRetentionDeletionEvent::class;
  protected $dataRetentionDeletionEventsDataType = 'array';
  protected $databaseType = GoogleCloudSecuritycenterV2Database::class;
  protected $databaseDataType = '';
  /**
   * Contains more details about the finding.
   *
   * @var string
   */
  public $description;
  protected $diskType = GoogleCloudSecuritycenterV2Disk::class;
  protected $diskDataType = '';
  /**
   * The time the finding was first detected. If an existing finding is updated,
   * then this is the time the update occurred. For example, if the finding
   * represents an open firewall, this property captures the time the detector
   * believes the firewall became open. The accuracy is determined by the
   * detector. If the finding is later resolved, then this time reflects when
   * the finding was resolved. This must not be set to a value greater than the
   * current timestamp.
   *
   * @var string
   */
  public $eventTime;
  protected $exfiltrationType = GoogleCloudSecuritycenterV2Exfiltration::class;
  protected $exfiltrationDataType = '';
  protected $externalSystemsType = GoogleCloudSecuritycenterV2ExternalSystem::class;
  protected $externalSystemsDataType = 'map';
  /**
   * The URI that, if available, points to a web page outside of Security
   * Command Center where additional information about the finding can be found.
   * This field is guaranteed to be either empty or a well formed URL.
   *
   * @var string
   */
  public $externalUri;
  protected $filesType = GoogleCloudSecuritycenterV2File::class;
  protected $filesDataType = 'array';
  /**
   * The class of the finding.
   *
   * @var string
   */
  public $findingClass;
  protected $groupMembershipsType = GoogleCloudSecuritycenterV2GroupMembership::class;
  protected $groupMembershipsDataType = 'array';
  protected $iamBindingsType = GoogleCloudSecuritycenterV2IamBinding::class;
  protected $iamBindingsDataType = 'array';
  protected $indicatorType = GoogleCloudSecuritycenterV2Indicator::class;
  protected $indicatorDataType = '';
  protected $ipRulesType = GoogleCloudSecuritycenterV2IpRules::class;
  protected $ipRulesDataType = '';
  protected $jobType = GoogleCloudSecuritycenterV2Job::class;
  protected $jobDataType = '';
  protected $kernelRootkitType = GoogleCloudSecuritycenterV2KernelRootkit::class;
  protected $kernelRootkitDataType = '';
  protected $kubernetesType = GoogleCloudSecuritycenterV2Kubernetes::class;
  protected $kubernetesDataType = '';
  protected $loadBalancersType = GoogleCloudSecuritycenterV2LoadBalancer::class;
  protected $loadBalancersDataType = 'array';
  protected $logEntriesType = GoogleCloudSecuritycenterV2LogEntry::class;
  protected $logEntriesDataType = 'array';
  protected $mitreAttackType = GoogleCloudSecuritycenterV2MitreAttack::class;
  protected $mitreAttackDataType = '';
  /**
   * Unique identifier of the module which generated the finding. Example: folde
   * rs/598186756061/securityHealthAnalyticsSettings/customModules/5679944116188
   * 5
   *
   * @var string
   */
  public $moduleName;
  /**
   * Indicates the mute state of a finding (either muted, unmuted or undefined).
   * Unlike other attributes of a finding, a finding provider shouldn't set the
   * value of mute.
   *
   * @var string
   */
  public $mute;
  protected $muteInfoType = GoogleCloudSecuritycenterV2MuteInfo::class;
  protected $muteInfoDataType = '';
  /**
   * Records additional information about the mute operation, for example, the
   * [mute configuration](https://cloud.google.com/security-command-
   * center/docs/how-to-mute-findings) that muted the finding and the user who
   * muted the finding.
   *
   * @var string
   */
  public $muteInitiator;
  /**
   * Output only. The most recent time this finding was muted or unmuted.
   *
   * @var string
   */
  public $muteUpdateTime;
  /**
   * Identifier. The [relative resource name](https://cloud.google.com/apis/desi
   * gn/resource_names#relative_resource_name) of the finding. The following
   * list shows some examples: +
   * `organizations/{organization_id}/sources/{source_id}/findings/{finding_id}`
   * + `organizations/{organization_id}/sources/{source_id}/locations/{location_
   * id}/findings/{finding_id}` +
   * `folders/{folder_id}/sources/{source_id}/findings/{finding_id}` + `folders/
   * {folder_id}/sources/{source_id}/locations/{location_id}/findings/{finding_i
   * d}` + `projects/{project_id}/sources/{source_id}/findings/{finding_id}` + `
   * projects/{project_id}/sources/{source_id}/locations/{location_id}/findings/
   * {finding_id}`
   *
   * @var string
   */
  public $name;
  protected $networksType = GoogleCloudSecuritycenterV2Network::class;
  protected $networksDataType = 'array';
  /**
   * Steps to address the finding.
   *
   * @var string
   */
  public $nextSteps;
  protected $notebookType = GoogleCloudSecuritycenterV2Notebook::class;
  protected $notebookDataType = '';
  protected $orgPoliciesType = GoogleCloudSecuritycenterV2OrgPolicy::class;
  protected $orgPoliciesDataType = 'array';
  /**
   * The relative resource name of the source and location the finding belongs
   * to. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * This field is immutable after creation time. The following list shows some
   * examples: + `organizations/{organization_id}/sources/{source_id}` +
   * `folders/{folders_id}/sources/{source_id}` +
   * `projects/{projects_id}/sources/{source_id}` + `organizations/{organization
   * _id}/sources/{source_id}/locations/{location_id}` +
   * `folders/{folders_id}/sources/{source_id}/locations/{location_id}` +
   * `projects/{projects_id}/sources/{source_id}/locations/{location_id}`
   *
   * @var string
   */
  public $parent;
  /**
   * Output only. The human readable display name of the finding source such as
   * "Event Threat Detection" or "Security Health Analytics".
   *
   * @var string
   */
  public $parentDisplayName;
  protected $processesType = GoogleCloudSecuritycenterV2Process::class;
  protected $processesDataType = 'array';
  /**
   * Immutable. For findings on Google Cloud resources, the full resource name
   * of the Google Cloud resource this finding is for. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name When
   * the finding is for a non-Google Cloud resource, the resourceName can be a
   * customer or partner defined string.
   *
   * @var string
   */
  public $resourceName;
  protected $securityMarksType = GoogleCloudSecuritycenterV2SecurityMarks::class;
  protected $securityMarksDataType = '';
  protected $securityPostureType = GoogleCloudSecuritycenterV2SecurityPosture::class;
  protected $securityPostureDataType = '';
  /**
   * The severity of the finding. This field is managed by the source that
   * writes the finding.
   *
   * @var string
   */
  public $severity;
  /**
   * Source specific properties. These properties are managed by the source that
   * writes the finding. The key names in the source_properties map must be
   * between 1 and 255 characters, and must start with a letter and contain
   * alphanumeric characters or underscores only.
   *
   * @var array[]
   */
  public $sourceProperties;
  /**
   * Output only. The state of the finding.
   *
   * @var string
   */
  public $state;
  protected $toxicCombinationType = GoogleCloudSecuritycenterV2ToxicCombination::class;
  protected $toxicCombinationDataType = '';
  protected $vertexAiType = GoogleCloudSecuritycenterV2VertexAi::class;
  protected $vertexAiDataType = '';
  protected $vulnerabilityType = GoogleCloudSecuritycenterV2Vulnerability::class;
  protected $vulnerabilityDataType = '';

  /**
   * Access details associated with the finding, such as more information on the
   * caller, which method was accessed, and from where.
   *
   * @param GoogleCloudSecuritycenterV2Access $access
   */
  public function setAccess(GoogleCloudSecuritycenterV2Access $access)
  {
    $this->access = $access;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Access
   */
  public function getAccess()
  {
    return $this->access;
  }
  /**
   * AffectedResources associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2AffectedResources $affectedResources
   */
  public function setAffectedResources(GoogleCloudSecuritycenterV2AffectedResources $affectedResources)
  {
    $this->affectedResources = $affectedResources;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AffectedResources
   */
  public function getAffectedResources()
  {
    return $this->affectedResources;
  }
  /**
   * The AI model associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2AiModel $aiModel
   */
  public function setAiModel(GoogleCloudSecuritycenterV2AiModel $aiModel)
  {
    $this->aiModel = $aiModel;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AiModel
   */
  public function getAiModel()
  {
    return $this->aiModel;
  }
  /**
   * Represents an application associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2Application $application
   */
  public function setApplication(GoogleCloudSecuritycenterV2Application $application)
  {
    $this->application = $application;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Application
   */
  public function getApplication()
  {
    return $this->application;
  }
  /**
   * The results of an attack path simulation relevant to this finding.
   *
   * @param GoogleCloudSecuritycenterV2AttackExposure $attackExposure
   */
  public function setAttackExposure(GoogleCloudSecuritycenterV2AttackExposure $attackExposure)
  {
    $this->attackExposure = $attackExposure;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AttackExposure
   */
  public function getAttackExposure()
  {
    return $this->attackExposure;
  }
  /**
   * Fields related to Backup and DR findings.
   *
   * @param GoogleCloudSecuritycenterV2BackupDisasterRecovery $backupDisasterRecovery
   */
  public function setBackupDisasterRecovery(GoogleCloudSecuritycenterV2BackupDisasterRecovery $backupDisasterRecovery)
  {
    $this->backupDisasterRecovery = $backupDisasterRecovery;
  }
  /**
   * @return GoogleCloudSecuritycenterV2BackupDisasterRecovery
   */
  public function getBackupDisasterRecovery()
  {
    return $this->backupDisasterRecovery;
  }
  /**
   * Output only. The canonical name of the finding. The following list shows
   * some examples: + `organizations/{organization_id}/sources/{source_id}/locat
   * ions/{location_id}/findings/{finding_id}` + `folders/{folder_id}/sources/{s
   * ource_id}/locations/{location_id}/findings/{finding_id}` + `projects/{proje
   * ct_id}/sources/{source_id}/locations/{location_id}/findings/{finding_id}`
   * The prefix is the closest CRM ancestor of the resource associated with the
   * finding.
   *
   * @param string $canonicalName
   */
  public function setCanonicalName($canonicalName)
  {
    $this->canonicalName = $canonicalName;
  }
  /**
   * @return string
   */
  public function getCanonicalName()
  {
    return $this->canonicalName;
  }
  /**
   * Immutable. The additional taxonomy group within findings from a given
   * source. Example: "XSS_FLASH_INJECTION"
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Contains details about a chokepoint, which is a resource or resource group
   * where high-risk attack paths converge, based on [attack path simulations]
   * (https://cloud.google.com/security-command-center/docs/attack-exposure-
   * learn#attack_path_simulations). This field cannot be updated. Its value is
   * ignored in all update requests.
   *
   * @param GoogleCloudSecuritycenterV2Chokepoint $chokepoint
   */
  public function setChokepoint(GoogleCloudSecuritycenterV2Chokepoint $chokepoint)
  {
    $this->chokepoint = $chokepoint;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Chokepoint
   */
  public function getChokepoint()
  {
    return $this->chokepoint;
  }
  /**
   * Fields related to Cloud Armor findings.
   *
   * @param GoogleCloudSecuritycenterV2CloudArmor $cloudArmor
   */
  public function setCloudArmor(GoogleCloudSecuritycenterV2CloudArmor $cloudArmor)
  {
    $this->cloudArmor = $cloudArmor;
  }
  /**
   * @return GoogleCloudSecuritycenterV2CloudArmor
   */
  public function getCloudArmor()
  {
    return $this->cloudArmor;
  }
  /**
   * Cloud DLP data profile that is associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2CloudDlpDataProfile $cloudDlpDataProfile
   */
  public function setCloudDlpDataProfile(GoogleCloudSecuritycenterV2CloudDlpDataProfile $cloudDlpDataProfile)
  {
    $this->cloudDlpDataProfile = $cloudDlpDataProfile;
  }
  /**
   * @return GoogleCloudSecuritycenterV2CloudDlpDataProfile
   */
  public function getCloudDlpDataProfile()
  {
    return $this->cloudDlpDataProfile;
  }
  /**
   * Cloud Data Loss Prevention (Cloud DLP) inspection results that are
   * associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2CloudDlpInspection $cloudDlpInspection
   */
  public function setCloudDlpInspection(GoogleCloudSecuritycenterV2CloudDlpInspection $cloudDlpInspection)
  {
    $this->cloudDlpInspection = $cloudDlpInspection;
  }
  /**
   * @return GoogleCloudSecuritycenterV2CloudDlpInspection
   */
  public function getCloudDlpInspection()
  {
    return $this->cloudDlpInspection;
  }
  /**
   * Details about the compliance implications of the finding.
   *
   * @param GoogleCloudSecuritycenterV2ComplianceDetails $complianceDetails
   */
  public function setComplianceDetails(GoogleCloudSecuritycenterV2ComplianceDetails $complianceDetails)
  {
    $this->complianceDetails = $complianceDetails;
  }
  /**
   * @return GoogleCloudSecuritycenterV2ComplianceDetails
   */
  public function getComplianceDetails()
  {
    return $this->complianceDetails;
  }
  /**
   * Contains compliance information for security standards associated to the
   * finding.
   *
   * @param GoogleCloudSecuritycenterV2Compliance[] $compliances
   */
  public function setCompliances($compliances)
  {
    $this->compliances = $compliances;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Compliance[]
   */
  public function getCompliances()
  {
    return $this->compliances;
  }
  /**
   * Contains information about the IP connection associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2Connection[] $connections
   */
  public function setConnections($connections)
  {
    $this->connections = $connections;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Connection[]
   */
  public function getConnections()
  {
    return $this->connections;
  }
  /**
   * Output only. Map containing the points of contact for the given finding.
   * The key represents the type of contact, while the value contains a list of
   * all the contacts that pertain. Please refer to:
   * https://cloud.google.com/resource-manager/docs/managing-notification-
   * contacts#notification-categories { "security": { "contacts": [ { "email":
   * "person1@company.com" }, { "email": "person2@company.com" } ] } }
   *
   * @param GoogleCloudSecuritycenterV2ContactDetails[] $contacts
   */
  public function setContacts($contacts)
  {
    $this->contacts = $contacts;
  }
  /**
   * @return GoogleCloudSecuritycenterV2ContactDetails[]
   */
  public function getContacts()
  {
    return $this->contacts;
  }
  /**
   * Containers associated with the finding. This field provides information for
   * both Kubernetes and non-Kubernetes containers.
   *
   * @param GoogleCloudSecuritycenterV2Container[] $containers
   */
  public function setContainers($containers)
  {
    $this->containers = $containers;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Container[]
   */
  public function getContainers()
  {
    return $this->containers;
  }
  /**
   * Output only. The time at which the finding was created in Security Command
   * Center.
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
   * Output only. The name of the Cloud KMS key used to encrypt this finding, if
   * any.
   *
   * @param string $cryptoKeyName
   */
  public function setCryptoKeyName($cryptoKeyName)
  {
    $this->cryptoKeyName = $cryptoKeyName;
  }
  /**
   * @return string
   */
  public function getCryptoKeyName()
  {
    return $this->cryptoKeyName;
  }
  /**
   * Data access events associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2DataAccessEvent[] $dataAccessEvents
   */
  public function setDataAccessEvents($dataAccessEvents)
  {
    $this->dataAccessEvents = $dataAccessEvents;
  }
  /**
   * @return GoogleCloudSecuritycenterV2DataAccessEvent[]
   */
  public function getDataAccessEvents()
  {
    return $this->dataAccessEvents;
  }
  /**
   * Data flow events associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2DataFlowEvent[] $dataFlowEvents
   */
  public function setDataFlowEvents($dataFlowEvents)
  {
    $this->dataFlowEvents = $dataFlowEvents;
  }
  /**
   * @return GoogleCloudSecuritycenterV2DataFlowEvent[]
   */
  public function getDataFlowEvents()
  {
    return $this->dataFlowEvents;
  }
  /**
   * Data retention deletion events associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2DataRetentionDeletionEvent[] $dataRetentionDeletionEvents
   */
  public function setDataRetentionDeletionEvents($dataRetentionDeletionEvents)
  {
    $this->dataRetentionDeletionEvents = $dataRetentionDeletionEvents;
  }
  /**
   * @return GoogleCloudSecuritycenterV2DataRetentionDeletionEvent[]
   */
  public function getDataRetentionDeletionEvents()
  {
    return $this->dataRetentionDeletionEvents;
  }
  /**
   * Database associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2Database $database
   */
  public function setDatabase(GoogleCloudSecuritycenterV2Database $database)
  {
    $this->database = $database;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Database
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Contains more details about the finding.
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
   * Disk associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2Disk $disk
   */
  public function setDisk(GoogleCloudSecuritycenterV2Disk $disk)
  {
    $this->disk = $disk;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Disk
   */
  public function getDisk()
  {
    return $this->disk;
  }
  /**
   * The time the finding was first detected. If an existing finding is updated,
   * then this is the time the update occurred. For example, if the finding
   * represents an open firewall, this property captures the time the detector
   * believes the firewall became open. The accuracy is determined by the
   * detector. If the finding is later resolved, then this time reflects when
   * the finding was resolved. This must not be set to a value greater than the
   * current timestamp.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * Represents exfiltrations associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2Exfiltration $exfiltration
   */
  public function setExfiltration(GoogleCloudSecuritycenterV2Exfiltration $exfiltration)
  {
    $this->exfiltration = $exfiltration;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Exfiltration
   */
  public function getExfiltration()
  {
    return $this->exfiltration;
  }
  /**
   * Output only. Third party SIEM/SOAR fields within SCC, contains external
   * system information and external system finding fields.
   *
   * @param GoogleCloudSecuritycenterV2ExternalSystem[] $externalSystems
   */
  public function setExternalSystems($externalSystems)
  {
    $this->externalSystems = $externalSystems;
  }
  /**
   * @return GoogleCloudSecuritycenterV2ExternalSystem[]
   */
  public function getExternalSystems()
  {
    return $this->externalSystems;
  }
  /**
   * The URI that, if available, points to a web page outside of Security
   * Command Center where additional information about the finding can be found.
   * This field is guaranteed to be either empty or a well formed URL.
   *
   * @param string $externalUri
   */
  public function setExternalUri($externalUri)
  {
    $this->externalUri = $externalUri;
  }
  /**
   * @return string
   */
  public function getExternalUri()
  {
    return $this->externalUri;
  }
  /**
   * File associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2File[] $files
   */
  public function setFiles($files)
  {
    $this->files = $files;
  }
  /**
   * @return GoogleCloudSecuritycenterV2File[]
   */
  public function getFiles()
  {
    return $this->files;
  }
  /**
   * The class of the finding.
   *
   * Accepted values: FINDING_CLASS_UNSPECIFIED, THREAT, VULNERABILITY,
   * MISCONFIGURATION, OBSERVATION, SCC_ERROR, POSTURE_VIOLATION,
   * TOXIC_COMBINATION, SENSITIVE_DATA_RISK, CHOKEPOINT
   *
   * @param self::FINDING_CLASS_* $findingClass
   */
  public function setFindingClass($findingClass)
  {
    $this->findingClass = $findingClass;
  }
  /**
   * @return self::FINDING_CLASS_*
   */
  public function getFindingClass()
  {
    return $this->findingClass;
  }
  /**
   * Contains details about groups of which this finding is a member. A group is
   * a collection of findings that are related in some way. This field cannot be
   * updated. Its value is ignored in all update requests.
   *
   * @param GoogleCloudSecuritycenterV2GroupMembership[] $groupMemberships
   */
  public function setGroupMemberships($groupMemberships)
  {
    $this->groupMemberships = $groupMemberships;
  }
  /**
   * @return GoogleCloudSecuritycenterV2GroupMembership[]
   */
  public function getGroupMemberships()
  {
    return $this->groupMemberships;
  }
  /**
   * Represents IAM bindings associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2IamBinding[] $iamBindings
   */
  public function setIamBindings($iamBindings)
  {
    $this->iamBindings = $iamBindings;
  }
  /**
   * @return GoogleCloudSecuritycenterV2IamBinding[]
   */
  public function getIamBindings()
  {
    return $this->iamBindings;
  }
  /**
   * Represents what's commonly known as an *indicator of compromise* (IoC) in
   * computer forensics. This is an artifact observed on a network or in an
   * operating system that, with high confidence, indicates a computer
   * intrusion. For more information, see [Indicator of
   * compromise](https://en.wikipedia.org/wiki/Indicator_of_compromise).
   *
   * @param GoogleCloudSecuritycenterV2Indicator $indicator
   */
  public function setIndicator(GoogleCloudSecuritycenterV2Indicator $indicator)
  {
    $this->indicator = $indicator;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Indicator
   */
  public function getIndicator()
  {
    return $this->indicator;
  }
  /**
   * IP rules associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2IpRules $ipRules
   */
  public function setIpRules(GoogleCloudSecuritycenterV2IpRules $ipRules)
  {
    $this->ipRules = $ipRules;
  }
  /**
   * @return GoogleCloudSecuritycenterV2IpRules
   */
  public function getIpRules()
  {
    return $this->ipRules;
  }
  /**
   * Job associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2Job $job
   */
  public function setJob(GoogleCloudSecuritycenterV2Job $job)
  {
    $this->job = $job;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Job
   */
  public function getJob()
  {
    return $this->job;
  }
  /**
   * Signature of the kernel rootkit.
   *
   * @param GoogleCloudSecuritycenterV2KernelRootkit $kernelRootkit
   */
  public function setKernelRootkit(GoogleCloudSecuritycenterV2KernelRootkit $kernelRootkit)
  {
    $this->kernelRootkit = $kernelRootkit;
  }
  /**
   * @return GoogleCloudSecuritycenterV2KernelRootkit
   */
  public function getKernelRootkit()
  {
    return $this->kernelRootkit;
  }
  /**
   * Kubernetes resources associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2Kubernetes $kubernetes
   */
  public function setKubernetes(GoogleCloudSecuritycenterV2Kubernetes $kubernetes)
  {
    $this->kubernetes = $kubernetes;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Kubernetes
   */
  public function getKubernetes()
  {
    return $this->kubernetes;
  }
  /**
   * The load balancers associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2LoadBalancer[] $loadBalancers
   */
  public function setLoadBalancers($loadBalancers)
  {
    $this->loadBalancers = $loadBalancers;
  }
  /**
   * @return GoogleCloudSecuritycenterV2LoadBalancer[]
   */
  public function getLoadBalancers()
  {
    return $this->loadBalancers;
  }
  /**
   * Log entries that are relevant to the finding.
   *
   * @param GoogleCloudSecuritycenterV2LogEntry[] $logEntries
   */
  public function setLogEntries($logEntries)
  {
    $this->logEntries = $logEntries;
  }
  /**
   * @return GoogleCloudSecuritycenterV2LogEntry[]
   */
  public function getLogEntries()
  {
    return $this->logEntries;
  }
  /**
   * MITRE ATT&CK tactics and techniques related to this finding. See:
   * https://attack.mitre.org
   *
   * @param GoogleCloudSecuritycenterV2MitreAttack $mitreAttack
   */
  public function setMitreAttack(GoogleCloudSecuritycenterV2MitreAttack $mitreAttack)
  {
    $this->mitreAttack = $mitreAttack;
  }
  /**
   * @return GoogleCloudSecuritycenterV2MitreAttack
   */
  public function getMitreAttack()
  {
    return $this->mitreAttack;
  }
  /**
   * Unique identifier of the module which generated the finding. Example: folde
   * rs/598186756061/securityHealthAnalyticsSettings/customModules/5679944116188
   * 5
   *
   * @param string $moduleName
   */
  public function setModuleName($moduleName)
  {
    $this->moduleName = $moduleName;
  }
  /**
   * @return string
   */
  public function getModuleName()
  {
    return $this->moduleName;
  }
  /**
   * Indicates the mute state of a finding (either muted, unmuted or undefined).
   * Unlike other attributes of a finding, a finding provider shouldn't set the
   * value of mute.
   *
   * Accepted values: MUTE_UNSPECIFIED, MUTED, UNMUTED, UNDEFINED
   *
   * @param self::MUTE_* $mute
   */
  public function setMute($mute)
  {
    $this->mute = $mute;
  }
  /**
   * @return self::MUTE_*
   */
  public function getMute()
  {
    return $this->mute;
  }
  /**
   * Output only. The mute information regarding this finding.
   *
   * @param GoogleCloudSecuritycenterV2MuteInfo $muteInfo
   */
  public function setMuteInfo(GoogleCloudSecuritycenterV2MuteInfo $muteInfo)
  {
    $this->muteInfo = $muteInfo;
  }
  /**
   * @return GoogleCloudSecuritycenterV2MuteInfo
   */
  public function getMuteInfo()
  {
    return $this->muteInfo;
  }
  /**
   * Records additional information about the mute operation, for example, the
   * [mute configuration](https://cloud.google.com/security-command-
   * center/docs/how-to-mute-findings) that muted the finding and the user who
   * muted the finding.
   *
   * @param string $muteInitiator
   */
  public function setMuteInitiator($muteInitiator)
  {
    $this->muteInitiator = $muteInitiator;
  }
  /**
   * @return string
   */
  public function getMuteInitiator()
  {
    return $this->muteInitiator;
  }
  /**
   * Output only. The most recent time this finding was muted or unmuted.
   *
   * @param string $muteUpdateTime
   */
  public function setMuteUpdateTime($muteUpdateTime)
  {
    $this->muteUpdateTime = $muteUpdateTime;
  }
  /**
   * @return string
   */
  public function getMuteUpdateTime()
  {
    return $this->muteUpdateTime;
  }
  /**
   * Identifier. The [relative resource name](https://cloud.google.com/apis/desi
   * gn/resource_names#relative_resource_name) of the finding. The following
   * list shows some examples: +
   * `organizations/{organization_id}/sources/{source_id}/findings/{finding_id}`
   * + `organizations/{organization_id}/sources/{source_id}/locations/{location_
   * id}/findings/{finding_id}` +
   * `folders/{folder_id}/sources/{source_id}/findings/{finding_id}` + `folders/
   * {folder_id}/sources/{source_id}/locations/{location_id}/findings/{finding_i
   * d}` + `projects/{project_id}/sources/{source_id}/findings/{finding_id}` + `
   * projects/{project_id}/sources/{source_id}/locations/{location_id}/findings/
   * {finding_id}`
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
   * Represents the VPC networks that the resource is attached to.
   *
   * @param GoogleCloudSecuritycenterV2Network[] $networks
   */
  public function setNetworks($networks)
  {
    $this->networks = $networks;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Network[]
   */
  public function getNetworks()
  {
    return $this->networks;
  }
  /**
   * Steps to address the finding.
   *
   * @param string $nextSteps
   */
  public function setNextSteps($nextSteps)
  {
    $this->nextSteps = $nextSteps;
  }
  /**
   * @return string
   */
  public function getNextSteps()
  {
    return $this->nextSteps;
  }
  /**
   * Notebook associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2Notebook $notebook
   */
  public function setNotebook(GoogleCloudSecuritycenterV2Notebook $notebook)
  {
    $this->notebook = $notebook;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Notebook
   */
  public function getNotebook()
  {
    return $this->notebook;
  }
  /**
   * Contains information about the org policies associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2OrgPolicy[] $orgPolicies
   */
  public function setOrgPolicies($orgPolicies)
  {
    $this->orgPolicies = $orgPolicies;
  }
  /**
   * @return GoogleCloudSecuritycenterV2OrgPolicy[]
   */
  public function getOrgPolicies()
  {
    return $this->orgPolicies;
  }
  /**
   * The relative resource name of the source and location the finding belongs
   * to. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * This field is immutable after creation time. The following list shows some
   * examples: + `organizations/{organization_id}/sources/{source_id}` +
   * `folders/{folders_id}/sources/{source_id}` +
   * `projects/{projects_id}/sources/{source_id}` + `organizations/{organization
   * _id}/sources/{source_id}/locations/{location_id}` +
   * `folders/{folders_id}/sources/{source_id}/locations/{location_id}` +
   * `projects/{projects_id}/sources/{source_id}/locations/{location_id}`
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Output only. The human readable display name of the finding source such as
   * "Event Threat Detection" or "Security Health Analytics".
   *
   * @param string $parentDisplayName
   */
  public function setParentDisplayName($parentDisplayName)
  {
    $this->parentDisplayName = $parentDisplayName;
  }
  /**
   * @return string
   */
  public function getParentDisplayName()
  {
    return $this->parentDisplayName;
  }
  /**
   * Represents operating system processes associated with the Finding.
   *
   * @param GoogleCloudSecuritycenterV2Process[] $processes
   */
  public function setProcesses($processes)
  {
    $this->processes = $processes;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Process[]
   */
  public function getProcesses()
  {
    return $this->processes;
  }
  /**
   * Immutable. For findings on Google Cloud resources, the full resource name
   * of the Google Cloud resource this finding is for. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name When
   * the finding is for a non-Google Cloud resource, the resourceName can be a
   * customer or partner defined string.
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. User specified security marks. These marks are entirely
   * managed by the user and come from the SecurityMarks resource that belongs
   * to the finding.
   *
   * @param GoogleCloudSecuritycenterV2SecurityMarks $securityMarks
   */
  public function setSecurityMarks(GoogleCloudSecuritycenterV2SecurityMarks $securityMarks)
  {
    $this->securityMarks = $securityMarks;
  }
  /**
   * @return GoogleCloudSecuritycenterV2SecurityMarks
   */
  public function getSecurityMarks()
  {
    return $this->securityMarks;
  }
  /**
   * The security posture associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2SecurityPosture $securityPosture
   */
  public function setSecurityPosture(GoogleCloudSecuritycenterV2SecurityPosture $securityPosture)
  {
    $this->securityPosture = $securityPosture;
  }
  /**
   * @return GoogleCloudSecuritycenterV2SecurityPosture
   */
  public function getSecurityPosture()
  {
    return $this->securityPosture;
  }
  /**
   * The severity of the finding. This field is managed by the source that
   * writes the finding.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, CRITICAL, HIGH, MEDIUM, LOW
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * Source specific properties. These properties are managed by the source that
   * writes the finding. The key names in the source_properties map must be
   * between 1 and 255 characters, and must start with a letter and contain
   * alphanumeric characters or underscores only.
   *
   * @param array[] $sourceProperties
   */
  public function setSourceProperties($sourceProperties)
  {
    $this->sourceProperties = $sourceProperties;
  }
  /**
   * @return array[]
   */
  public function getSourceProperties()
  {
    return $this->sourceProperties;
  }
  /**
   * Output only. The state of the finding.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, INACTIVE
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
   * Contains details about a group of security issues that, when the issues
   * occur together, represent a greater risk than when the issues occur
   * independently. A group of such issues is referred to as a toxic
   * combination. This field cannot be updated. Its value is ignored in all
   * update requests.
   *
   * @param GoogleCloudSecuritycenterV2ToxicCombination $toxicCombination
   */
  public function setToxicCombination(GoogleCloudSecuritycenterV2ToxicCombination $toxicCombination)
  {
    $this->toxicCombination = $toxicCombination;
  }
  /**
   * @return GoogleCloudSecuritycenterV2ToxicCombination
   */
  public function getToxicCombination()
  {
    return $this->toxicCombination;
  }
  /**
   * VertexAi associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2VertexAi $vertexAi
   */
  public function setVertexAi(GoogleCloudSecuritycenterV2VertexAi $vertexAi)
  {
    $this->vertexAi = $vertexAi;
  }
  /**
   * @return GoogleCloudSecuritycenterV2VertexAi
   */
  public function getVertexAi()
  {
    return $this->vertexAi;
  }
  /**
   * Represents vulnerability-specific fields like CVE and CVSS scores. CVE
   * stands for Common Vulnerabilities and Exposures
   * (https://cve.mitre.org/about/)
   *
   * @param GoogleCloudSecuritycenterV2Vulnerability $vulnerability
   */
  public function setVulnerability(GoogleCloudSecuritycenterV2Vulnerability $vulnerability)
  {
    $this->vulnerability = $vulnerability;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Vulnerability
   */
  public function getVulnerability()
  {
    return $this->vulnerability;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2Finding::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2Finding');

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

class Finding extends \Google\Collection
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
   * Describes a group of security issues that, when the issues occur together,
   * represent a greater risk than when the issues occur independently. A group
   * of such issues is referred to as a toxic combination.
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
  protected $accessType = Access::class;
  protected $accessDataType = '';
  protected $affectedResourcesType = AffectedResources::class;
  protected $affectedResourcesDataType = '';
  protected $aiModelType = AiModel::class;
  protected $aiModelDataType = '';
  protected $applicationType = Application::class;
  protected $applicationDataType = '';
  protected $attackExposureType = AttackExposure::class;
  protected $attackExposureDataType = '';
  protected $backupDisasterRecoveryType = BackupDisasterRecovery::class;
  protected $backupDisasterRecoveryDataType = '';
  /**
   * The canonical name of the finding. It's either "organizations/{organization
   * _id}/sources/{source_id}/findings/{finding_id}",
   * "folders/{folder_id}/sources/{source_id}/findings/{finding_id}" or
   * "projects/{project_number}/sources/{source_id}/findings/{finding_id}",
   * depending on the closest CRM ancestor of the resource associated with the
   * finding.
   *
   * @var string
   */
  public $canonicalName;
  /**
   * The additional taxonomy group within findings from a given source. This
   * field is immutable after creation time. Example: "XSS_FLASH_INJECTION"
   *
   * @var string
   */
  public $category;
  protected $chokepointType = Chokepoint::class;
  protected $chokepointDataType = '';
  protected $cloudArmorType = CloudArmor::class;
  protected $cloudArmorDataType = '';
  protected $cloudDlpDataProfileType = CloudDlpDataProfile::class;
  protected $cloudDlpDataProfileDataType = '';
  protected $cloudDlpInspectionType = CloudDlpInspection::class;
  protected $cloudDlpInspectionDataType = '';
  protected $complianceDetailsType = ComplianceDetails::class;
  protected $complianceDetailsDataType = '';
  protected $compliancesType = Compliance::class;
  protected $compliancesDataType = 'array';
  protected $connectionsType = Connection::class;
  protected $connectionsDataType = 'array';
  protected $contactsType = ContactDetails::class;
  protected $contactsDataType = 'map';
  protected $containersType = Container::class;
  protected $containersDataType = 'array';
  /**
   * The time at which the finding was created in Security Command Center.
   *
   * @var string
   */
  public $createTime;
  protected $dataAccessEventsType = DataAccessEvent::class;
  protected $dataAccessEventsDataType = 'array';
  protected $dataFlowEventsType = DataFlowEvent::class;
  protected $dataFlowEventsDataType = 'array';
  protected $dataRetentionDeletionEventsType = DataRetentionDeletionEvent::class;
  protected $dataRetentionDeletionEventsDataType = 'array';
  protected $databaseType = Database::class;
  protected $databaseDataType = '';
  /**
   * Contains more details about the finding.
   *
   * @var string
   */
  public $description;
  protected $diskType = Disk::class;
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
  protected $exfiltrationType = Exfiltration::class;
  protected $exfiltrationDataType = '';
  protected $externalSystemsType = GoogleCloudSecuritycenterV1ExternalSystem::class;
  protected $externalSystemsDataType = 'map';
  /**
   * The URI that, if available, points to a web page outside of Security
   * Command Center where additional information about the finding can be found.
   * This field is guaranteed to be either empty or a well formed URL.
   *
   * @var string
   */
  public $externalUri;
  protected $filesType = SecuritycenterFile::class;
  protected $filesDataType = 'array';
  /**
   * The class of the finding.
   *
   * @var string
   */
  public $findingClass;
  protected $groupMembershipsType = GroupMembership::class;
  protected $groupMembershipsDataType = 'array';
  protected $iamBindingsType = IamBinding::class;
  protected $iamBindingsDataType = 'array';
  protected $indicatorType = Indicator::class;
  protected $indicatorDataType = '';
  protected $ipRulesType = IpRules::class;
  protected $ipRulesDataType = '';
  protected $jobType = Job::class;
  protected $jobDataType = '';
  protected $kernelRootkitType = KernelRootkit::class;
  protected $kernelRootkitDataType = '';
  protected $kubernetesType = Kubernetes::class;
  protected $kubernetesDataType = '';
  protected $loadBalancersType = LoadBalancer::class;
  protected $loadBalancersDataType = 'array';
  protected $logEntriesType = LogEntry::class;
  protected $logEntriesDataType = 'array';
  protected $mitreAttackType = MitreAttack::class;
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
  protected $muteInfoType = MuteInfo::class;
  protected $muteInfoDataType = '';
  /**
   * Records additional information about the mute operation, for example, the
   * [mute configuration](/security-command-center/docs/how-to-mute-findings)
   * that muted the finding and the user who muted the finding.
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
   * The [relative resource name](https://cloud.google.com/apis/design/resource_
   * names#relative_resource_name) of the finding. Example: "organizations/{orga
   * nization_id}/sources/{source_id}/findings/{finding_id}",
   * "folders/{folder_id}/sources/{source_id}/findings/{finding_id}",
   * "projects/{project_id}/sources/{source_id}/findings/{finding_id}".
   *
   * @var string
   */
  public $name;
  protected $networksType = Network::class;
  protected $networksDataType = 'array';
  /**
   * Steps to address the finding.
   *
   * @var string
   */
  public $nextSteps;
  protected $notebookType = Notebook::class;
  protected $notebookDataType = '';
  protected $orgPoliciesType = OrgPolicy::class;
  protected $orgPoliciesDataType = 'array';
  /**
   * The relative resource name of the source the finding belongs to. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * This field is immutable after creation time. For example:
   * "organizations/{organization_id}/sources/{source_id}"
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
  protected $processesType = Process::class;
  protected $processesDataType = 'array';
  /**
   * For findings on Google Cloud resources, the full resource name of the
   * Google Cloud resource this finding is for. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name When
   * the finding is for a non-Google Cloud resource, the resourceName can be a
   * customer or partner defined string. This field is immutable after creation
   * time.
   *
   * @var string
   */
  public $resourceName;
  protected $securityMarksType = SecurityMarks::class;
  protected $securityMarksDataType = '';
  protected $securityPostureType = SecurityPosture::class;
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
   * The state of the finding.
   *
   * @var string
   */
  public $state;
  protected $toxicCombinationType = ToxicCombination::class;
  protected $toxicCombinationDataType = '';
  protected $vertexAiType = VertexAi::class;
  protected $vertexAiDataType = '';
  protected $vulnerabilityType = Vulnerability::class;
  protected $vulnerabilityDataType = '';

  /**
   * Access details associated with the finding, such as more information on the
   * caller, which method was accessed, and from where.
   *
   * @param Access $access
   */
  public function setAccess(Access $access)
  {
    $this->access = $access;
  }
  /**
   * @return Access
   */
  public function getAccess()
  {
    return $this->access;
  }
  /**
   * AffectedResources associated with the finding.
   *
   * @param AffectedResources $affectedResources
   */
  public function setAffectedResources(AffectedResources $affectedResources)
  {
    $this->affectedResources = $affectedResources;
  }
  /**
   * @return AffectedResources
   */
  public function getAffectedResources()
  {
    return $this->affectedResources;
  }
  /**
   * The AI model associated with the finding.
   *
   * @param AiModel $aiModel
   */
  public function setAiModel(AiModel $aiModel)
  {
    $this->aiModel = $aiModel;
  }
  /**
   * @return AiModel
   */
  public function getAiModel()
  {
    return $this->aiModel;
  }
  /**
   * Represents an application associated with the finding.
   *
   * @param Application $application
   */
  public function setApplication(Application $application)
  {
    $this->application = $application;
  }
  /**
   * @return Application
   */
  public function getApplication()
  {
    return $this->application;
  }
  /**
   * The results of an attack path simulation relevant to this finding.
   *
   * @param AttackExposure $attackExposure
   */
  public function setAttackExposure(AttackExposure $attackExposure)
  {
    $this->attackExposure = $attackExposure;
  }
  /**
   * @return AttackExposure
   */
  public function getAttackExposure()
  {
    return $this->attackExposure;
  }
  /**
   * Fields related to Backup and DR findings.
   *
   * @param BackupDisasterRecovery $backupDisasterRecovery
   */
  public function setBackupDisasterRecovery(BackupDisasterRecovery $backupDisasterRecovery)
  {
    $this->backupDisasterRecovery = $backupDisasterRecovery;
  }
  /**
   * @return BackupDisasterRecovery
   */
  public function getBackupDisasterRecovery()
  {
    return $this->backupDisasterRecovery;
  }
  /**
   * The canonical name of the finding. It's either "organizations/{organization
   * _id}/sources/{source_id}/findings/{finding_id}",
   * "folders/{folder_id}/sources/{source_id}/findings/{finding_id}" or
   * "projects/{project_number}/sources/{source_id}/findings/{finding_id}",
   * depending on the closest CRM ancestor of the resource associated with the
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
   * The additional taxonomy group within findings from a given source. This
   * field is immutable after creation time. Example: "XSS_FLASH_INJECTION"
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
   * @param Chokepoint $chokepoint
   */
  public function setChokepoint(Chokepoint $chokepoint)
  {
    $this->chokepoint = $chokepoint;
  }
  /**
   * @return Chokepoint
   */
  public function getChokepoint()
  {
    return $this->chokepoint;
  }
  /**
   * Fields related to Cloud Armor findings.
   *
   * @param CloudArmor $cloudArmor
   */
  public function setCloudArmor(CloudArmor $cloudArmor)
  {
    $this->cloudArmor = $cloudArmor;
  }
  /**
   * @return CloudArmor
   */
  public function getCloudArmor()
  {
    return $this->cloudArmor;
  }
  /**
   * Cloud DLP data profile that is associated with the finding.
   *
   * @param CloudDlpDataProfile $cloudDlpDataProfile
   */
  public function setCloudDlpDataProfile(CloudDlpDataProfile $cloudDlpDataProfile)
  {
    $this->cloudDlpDataProfile = $cloudDlpDataProfile;
  }
  /**
   * @return CloudDlpDataProfile
   */
  public function getCloudDlpDataProfile()
  {
    return $this->cloudDlpDataProfile;
  }
  /**
   * Cloud Data Loss Prevention (Cloud DLP) inspection results that are
   * associated with the finding.
   *
   * @param CloudDlpInspection $cloudDlpInspection
   */
  public function setCloudDlpInspection(CloudDlpInspection $cloudDlpInspection)
  {
    $this->cloudDlpInspection = $cloudDlpInspection;
  }
  /**
   * @return CloudDlpInspection
   */
  public function getCloudDlpInspection()
  {
    return $this->cloudDlpInspection;
  }
  /**
   * Details about the compliance implications of the finding.
   *
   * @param ComplianceDetails $complianceDetails
   */
  public function setComplianceDetails(ComplianceDetails $complianceDetails)
  {
    $this->complianceDetails = $complianceDetails;
  }
  /**
   * @return ComplianceDetails
   */
  public function getComplianceDetails()
  {
    return $this->complianceDetails;
  }
  /**
   * Contains compliance information for security standards associated to the
   * finding.
   *
   * @param Compliance[] $compliances
   */
  public function setCompliances($compliances)
  {
    $this->compliances = $compliances;
  }
  /**
   * @return Compliance[]
   */
  public function getCompliances()
  {
    return $this->compliances;
  }
  /**
   * Contains information about the IP connection associated with the finding.
   *
   * @param Connection[] $connections
   */
  public function setConnections($connections)
  {
    $this->connections = $connections;
  }
  /**
   * @return Connection[]
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
   * @param ContactDetails[] $contacts
   */
  public function setContacts($contacts)
  {
    $this->contacts = $contacts;
  }
  /**
   * @return ContactDetails[]
   */
  public function getContacts()
  {
    return $this->contacts;
  }
  /**
   * Containers associated with the finding. This field provides information for
   * both Kubernetes and non-Kubernetes containers.
   *
   * @param Container[] $containers
   */
  public function setContainers($containers)
  {
    $this->containers = $containers;
  }
  /**
   * @return Container[]
   */
  public function getContainers()
  {
    return $this->containers;
  }
  /**
   * The time at which the finding was created in Security Command Center.
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
   * Data access events associated with the finding.
   *
   * @param DataAccessEvent[] $dataAccessEvents
   */
  public function setDataAccessEvents($dataAccessEvents)
  {
    $this->dataAccessEvents = $dataAccessEvents;
  }
  /**
   * @return DataAccessEvent[]
   */
  public function getDataAccessEvents()
  {
    return $this->dataAccessEvents;
  }
  /**
   * Data flow events associated with the finding.
   *
   * @param DataFlowEvent[] $dataFlowEvents
   */
  public function setDataFlowEvents($dataFlowEvents)
  {
    $this->dataFlowEvents = $dataFlowEvents;
  }
  /**
   * @return DataFlowEvent[]
   */
  public function getDataFlowEvents()
  {
    return $this->dataFlowEvents;
  }
  /**
   * Data retention deletion events associated with the finding.
   *
   * @param DataRetentionDeletionEvent[] $dataRetentionDeletionEvents
   */
  public function setDataRetentionDeletionEvents($dataRetentionDeletionEvents)
  {
    $this->dataRetentionDeletionEvents = $dataRetentionDeletionEvents;
  }
  /**
   * @return DataRetentionDeletionEvent[]
   */
  public function getDataRetentionDeletionEvents()
  {
    return $this->dataRetentionDeletionEvents;
  }
  /**
   * Database associated with the finding.
   *
   * @param Database $database
   */
  public function setDatabase(Database $database)
  {
    $this->database = $database;
  }
  /**
   * @return Database
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
   * @param Disk $disk
   */
  public function setDisk(Disk $disk)
  {
    $this->disk = $disk;
  }
  /**
   * @return Disk
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
   * @param Exfiltration $exfiltration
   */
  public function setExfiltration(Exfiltration $exfiltration)
  {
    $this->exfiltration = $exfiltration;
  }
  /**
   * @return Exfiltration
   */
  public function getExfiltration()
  {
    return $this->exfiltration;
  }
  /**
   * Output only. Third party SIEM/SOAR fields within SCC, contains external
   * system information and external system finding fields.
   *
   * @param GoogleCloudSecuritycenterV1ExternalSystem[] $externalSystems
   */
  public function setExternalSystems($externalSystems)
  {
    $this->externalSystems = $externalSystems;
  }
  /**
   * @return GoogleCloudSecuritycenterV1ExternalSystem[]
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
   * @param SecuritycenterFile[] $files
   */
  public function setFiles($files)
  {
    $this->files = $files;
  }
  /**
   * @return SecuritycenterFile[]
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
   * @param GroupMembership[] $groupMemberships
   */
  public function setGroupMemberships($groupMemberships)
  {
    $this->groupMemberships = $groupMemberships;
  }
  /**
   * @return GroupMembership[]
   */
  public function getGroupMemberships()
  {
    return $this->groupMemberships;
  }
  /**
   * Represents IAM bindings associated with the finding.
   *
   * @param IamBinding[] $iamBindings
   */
  public function setIamBindings($iamBindings)
  {
    $this->iamBindings = $iamBindings;
  }
  /**
   * @return IamBinding[]
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
   * @param Indicator $indicator
   */
  public function setIndicator(Indicator $indicator)
  {
    $this->indicator = $indicator;
  }
  /**
   * @return Indicator
   */
  public function getIndicator()
  {
    return $this->indicator;
  }
  /**
   * IP rules associated with the finding.
   *
   * @param IpRules $ipRules
   */
  public function setIpRules(IpRules $ipRules)
  {
    $this->ipRules = $ipRules;
  }
  /**
   * @return IpRules
   */
  public function getIpRules()
  {
    return $this->ipRules;
  }
  /**
   * Job associated with the finding.
   *
   * @param Job $job
   */
  public function setJob(Job $job)
  {
    $this->job = $job;
  }
  /**
   * @return Job
   */
  public function getJob()
  {
    return $this->job;
  }
  /**
   * Signature of the kernel rootkit.
   *
   * @param KernelRootkit $kernelRootkit
   */
  public function setKernelRootkit(KernelRootkit $kernelRootkit)
  {
    $this->kernelRootkit = $kernelRootkit;
  }
  /**
   * @return KernelRootkit
   */
  public function getKernelRootkit()
  {
    return $this->kernelRootkit;
  }
  /**
   * Kubernetes resources associated with the finding.
   *
   * @param Kubernetes $kubernetes
   */
  public function setKubernetes(Kubernetes $kubernetes)
  {
    $this->kubernetes = $kubernetes;
  }
  /**
   * @return Kubernetes
   */
  public function getKubernetes()
  {
    return $this->kubernetes;
  }
  /**
   * The load balancers associated with the finding.
   *
   * @param LoadBalancer[] $loadBalancers
   */
  public function setLoadBalancers($loadBalancers)
  {
    $this->loadBalancers = $loadBalancers;
  }
  /**
   * @return LoadBalancer[]
   */
  public function getLoadBalancers()
  {
    return $this->loadBalancers;
  }
  /**
   * Log entries that are relevant to the finding.
   *
   * @param LogEntry[] $logEntries
   */
  public function setLogEntries($logEntries)
  {
    $this->logEntries = $logEntries;
  }
  /**
   * @return LogEntry[]
   */
  public function getLogEntries()
  {
    return $this->logEntries;
  }
  /**
   * MITRE ATT&CK tactics and techniques related to this finding. See:
   * https://attack.mitre.org
   *
   * @param MitreAttack $mitreAttack
   */
  public function setMitreAttack(MitreAttack $mitreAttack)
  {
    $this->mitreAttack = $mitreAttack;
  }
  /**
   * @return MitreAttack
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
   * @param MuteInfo $muteInfo
   */
  public function setMuteInfo(MuteInfo $muteInfo)
  {
    $this->muteInfo = $muteInfo;
  }
  /**
   * @return MuteInfo
   */
  public function getMuteInfo()
  {
    return $this->muteInfo;
  }
  /**
   * Records additional information about the mute operation, for example, the
   * [mute configuration](/security-command-center/docs/how-to-mute-findings)
   * that muted the finding and the user who muted the finding.
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
   * The [relative resource name](https://cloud.google.com/apis/design/resource_
   * names#relative_resource_name) of the finding. Example: "organizations/{orga
   * nization_id}/sources/{source_id}/findings/{finding_id}",
   * "folders/{folder_id}/sources/{source_id}/findings/{finding_id}",
   * "projects/{project_id}/sources/{source_id}/findings/{finding_id}".
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
   * @param Network[] $networks
   */
  public function setNetworks($networks)
  {
    $this->networks = $networks;
  }
  /**
   * @return Network[]
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
   * @param Notebook $notebook
   */
  public function setNotebook(Notebook $notebook)
  {
    $this->notebook = $notebook;
  }
  /**
   * @return Notebook
   */
  public function getNotebook()
  {
    return $this->notebook;
  }
  /**
   * Contains information about the org policies associated with the finding.
   *
   * @param OrgPolicy[] $orgPolicies
   */
  public function setOrgPolicies($orgPolicies)
  {
    $this->orgPolicies = $orgPolicies;
  }
  /**
   * @return OrgPolicy[]
   */
  public function getOrgPolicies()
  {
    return $this->orgPolicies;
  }
  /**
   * The relative resource name of the source the finding belongs to. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * This field is immutable after creation time. For example:
   * "organizations/{organization_id}/sources/{source_id}"
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
   * @param Process[] $processes
   */
  public function setProcesses($processes)
  {
    $this->processes = $processes;
  }
  /**
   * @return Process[]
   */
  public function getProcesses()
  {
    return $this->processes;
  }
  /**
   * For findings on Google Cloud resources, the full resource name of the
   * Google Cloud resource this finding is for. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name When
   * the finding is for a non-Google Cloud resource, the resourceName can be a
   * customer or partner defined string. This field is immutable after creation
   * time.
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
   * @param SecurityMarks $securityMarks
   */
  public function setSecurityMarks(SecurityMarks $securityMarks)
  {
    $this->securityMarks = $securityMarks;
  }
  /**
   * @return SecurityMarks
   */
  public function getSecurityMarks()
  {
    return $this->securityMarks;
  }
  /**
   * The security posture associated with the finding.
   *
   * @param SecurityPosture $securityPosture
   */
  public function setSecurityPosture(SecurityPosture $securityPosture)
  {
    $this->securityPosture = $securityPosture;
  }
  /**
   * @return SecurityPosture
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
   * The state of the finding.
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
   * @param ToxicCombination $toxicCombination
   */
  public function setToxicCombination(ToxicCombination $toxicCombination)
  {
    $this->toxicCombination = $toxicCombination;
  }
  /**
   * @return ToxicCombination
   */
  public function getToxicCombination()
  {
    return $this->toxicCombination;
  }
  /**
   * VertexAi associated with the finding.
   *
   * @param VertexAi $vertexAi
   */
  public function setVertexAi(VertexAi $vertexAi)
  {
    $this->vertexAi = $vertexAi;
  }
  /**
   * @return VertexAi
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
   * @param Vulnerability $vulnerability
   */
  public function setVulnerability(Vulnerability $vulnerability)
  {
    $this->vulnerability = $vulnerability;
  }
  /**
   * @return Vulnerability
   */
  public function getVulnerability()
  {
    return $this->vulnerability;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Finding::class, 'Google_Service_SecurityCommandCenter_Finding');

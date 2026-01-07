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

namespace Google\Service\Connectors;

class Instance extends \Google\Collection
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Instance is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Instance has been created and is ready to use.
   */
  public const STATE_READY = 'READY';
  /**
   * Instance is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Instance is unheathy and under repair.
   */
  public const STATE_REPAIRING = 'REPAIRING';
  /**
   * Instance is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Instance encountered an error and is in indeterministic state.
   */
  public const STATE_ERROR = 'ERROR';
  protected $collection_key = 'provisionedResources';
  /**
   * consumer_defined_name is the name of the instance set by the service
   * consumers. Generally this is different from the `name` field which
   * reperesents the system-assigned id of the instance which the service
   * consumers do not recognize. This is a required field for tenants onboarding
   * to Maintenance Window notifications (go/slm-rollout-maintenance-
   * policies#prerequisites).
   *
   * @var string
   */
  public $consumerDefinedName;
  /**
   * Optional. The consumer_project_number associated with this Apigee instance.
   * This field is added specifically to support Apigee integration with SLM
   * Rollout and UMM. It represents the numerical project ID of the GCP project
   * that consumes this Apigee instance. It is used for SLM rollout
   * notifications and UMM integration, enabling proper mapping to customer
   * projects and log delivery for Apigee instances. This field complements
   * consumer_project_id and may be used for specific Apigee scenarios where the
   * numerical ID is required.
   *
   * @var string
   */
  public $consumerProjectNumber;
  /**
   * Output only. Timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The instance_type of this instance of format: projects/{project_n
   * umber}/locations/{location_id}/instanceTypes/{instance_type_id}. Instance
   * Type represents a high-level tier or SKU of the service that this instance
   * belong to. When enabled(eg: Maintenance Rollout), Rollout uses
   * 'instance_type' along with 'software_versions' to determine whether
   * instance needs an update or not.
   *
   * @var string
   */
  public $instanceType;
  /**
   * Optional. Resource labels to represent user provided metadata. Each label
   * is a key-value pair, where both the key and the value are arbitrary strings
   * provided by the user.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. The MaintenancePolicies that have been attached to the instance.
   * The key must be of the type name of the oneof policy name defined in
   * MaintenancePolicy, and the referenced policy must define the same policy
   * type. For details, please refer to go/mr-user-guide. Should not be set if
   * maintenance_settings.maintenance_policies is set.
   *
   * @var string[]
   */
  public $maintenancePolicyNames;
  protected $maintenanceSchedulesType = MaintenanceSchedule::class;
  protected $maintenanceSchedulesDataType = 'map';
  protected $maintenanceSettingsType = MaintenanceSettings::class;
  protected $maintenanceSettingsDataType = '';
  /**
   * Unique name of the resource. It uses the form:
   * `projects/{project_number}/locations/{location_id}/instances/{instance_id}`
   * Note: This name is passed, stored and logged across the rollout system. So
   * use of consumer project_id or any other consumer PII in the name is
   * strongly discouraged for wipeout (go/wipeout) compliance. See
   * go/elysium/project_ids#storage-guidance for more details.
   *
   * @var string
   */
  public $name;
  protected $notificationParametersType = NotificationParameter::class;
  protected $notificationParametersDataType = 'map';
  /**
   * Output only. Custom string attributes used primarily to expose producer-
   * specific information in monitoring dashboards. See go/get-instance-
   * metadata.
   *
   * @var string[]
   */
  public $producerMetadata;
  protected $provisionedResourcesType = ProvisionedResource::class;
  protected $provisionedResourcesDataType = 'array';
  /**
   * Link to the SLM instance template. Only populated when updating SLM
   * instances via SSA's Actuation service adaptor. Service producers with
   * custom control plane (e.g. Cloud SQL) doesn't need to populate this field.
   * Instead they should use software_versions.
   *
   * @var string
   */
  public $slmInstanceTemplate;
  protected $sloMetadataType = SloMetadata::class;
  protected $sloMetadataDataType = '';
  /**
   * Software versions that are used to deploy this instance. This can be
   * mutated by rollout services.
   *
   * @var string[]
   */
  public $softwareVersions;
  /**
   * Output only. Current lifecycle state of the resource (e.g. if it's being
   * created or ready to use).
   *
   * @var string
   */
  public $state;
  /**
   * Output only. ID of the associated GCP tenant project. See go/get-instance-
   * metadata.
   *
   * @var string
   */
  public $tenantProjectId;
  /**
   * Output only. Timestamp when the resource was last modified.
   *
   * @var string
   */
  public $updateTime;

  /**
   * consumer_defined_name is the name of the instance set by the service
   * consumers. Generally this is different from the `name` field which
   * reperesents the system-assigned id of the instance which the service
   * consumers do not recognize. This is a required field for tenants onboarding
   * to Maintenance Window notifications (go/slm-rollout-maintenance-
   * policies#prerequisites).
   *
   * @param string $consumerDefinedName
   */
  public function setConsumerDefinedName($consumerDefinedName)
  {
    $this->consumerDefinedName = $consumerDefinedName;
  }
  /**
   * @return string
   */
  public function getConsumerDefinedName()
  {
    return $this->consumerDefinedName;
  }
  /**
   * Optional. The consumer_project_number associated with this Apigee instance.
   * This field is added specifically to support Apigee integration with SLM
   * Rollout and UMM. It represents the numerical project ID of the GCP project
   * that consumes this Apigee instance. It is used for SLM rollout
   * notifications and UMM integration, enabling proper mapping to customer
   * projects and log delivery for Apigee instances. This field complements
   * consumer_project_id and may be used for specific Apigee scenarios where the
   * numerical ID is required.
   *
   * @param string $consumerProjectNumber
   */
  public function setConsumerProjectNumber($consumerProjectNumber)
  {
    $this->consumerProjectNumber = $consumerProjectNumber;
  }
  /**
   * @return string
   */
  public function getConsumerProjectNumber()
  {
    return $this->consumerProjectNumber;
  }
  /**
   * Output only. Timestamp when the resource was created.
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
   * Optional. The instance_type of this instance of format: projects/{project_n
   * umber}/locations/{location_id}/instanceTypes/{instance_type_id}. Instance
   * Type represents a high-level tier or SKU of the service that this instance
   * belong to. When enabled(eg: Maintenance Rollout), Rollout uses
   * 'instance_type' along with 'software_versions' to determine whether
   * instance needs an update or not.
   *
   * @param string $instanceType
   */
  public function setInstanceType($instanceType)
  {
    $this->instanceType = $instanceType;
  }
  /**
   * @return string
   */
  public function getInstanceType()
  {
    return $this->instanceType;
  }
  /**
   * Optional. Resource labels to represent user provided metadata. Each label
   * is a key-value pair, where both the key and the value are arbitrary strings
   * provided by the user.
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
   * Optional. The MaintenancePolicies that have been attached to the instance.
   * The key must be of the type name of the oneof policy name defined in
   * MaintenancePolicy, and the referenced policy must define the same policy
   * type. For details, please refer to go/mr-user-guide. Should not be set if
   * maintenance_settings.maintenance_policies is set.
   *
   * @param string[] $maintenancePolicyNames
   */
  public function setMaintenancePolicyNames($maintenancePolicyNames)
  {
    $this->maintenancePolicyNames = $maintenancePolicyNames;
  }
  /**
   * @return string[]
   */
  public function getMaintenancePolicyNames()
  {
    return $this->maintenancePolicyNames;
  }
  /**
   * The MaintenanceSchedule contains the scheduling information of published
   * maintenance schedule with same key as software_versions.
   *
   * @param MaintenanceSchedule[] $maintenanceSchedules
   */
  public function setMaintenanceSchedules($maintenanceSchedules)
  {
    $this->maintenanceSchedules = $maintenanceSchedules;
  }
  /**
   * @return MaintenanceSchedule[]
   */
  public function getMaintenanceSchedules()
  {
    return $this->maintenanceSchedules;
  }
  /**
   * Optional. The MaintenanceSettings associated with instance.
   *
   * @param MaintenanceSettings $maintenanceSettings
   */
  public function setMaintenanceSettings(MaintenanceSettings $maintenanceSettings)
  {
    $this->maintenanceSettings = $maintenanceSettings;
  }
  /**
   * @return MaintenanceSettings
   */
  public function getMaintenanceSettings()
  {
    return $this->maintenanceSettings;
  }
  /**
   * Unique name of the resource. It uses the form:
   * `projects/{project_number}/locations/{location_id}/instances/{instance_id}`
   * Note: This name is passed, stored and logged across the rollout system. So
   * use of consumer project_id or any other consumer PII in the name is
   * strongly discouraged for wipeout (go/wipeout) compliance. See
   * go/elysium/project_ids#storage-guidance for more details.
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
   * Optional. notification_parameter are information that service producers may
   * like to include that is not relevant to Rollout. This parameter will only
   * be passed to Gamma and Cloud Logging for notification/logging purpose.
   *
   * @param NotificationParameter[] $notificationParameters
   */
  public function setNotificationParameters($notificationParameters)
  {
    $this->notificationParameters = $notificationParameters;
  }
  /**
   * @return NotificationParameter[]
   */
  public function getNotificationParameters()
  {
    return $this->notificationParameters;
  }
  /**
   * Output only. Custom string attributes used primarily to expose producer-
   * specific information in monitoring dashboards. See go/get-instance-
   * metadata.
   *
   * @param string[] $producerMetadata
   */
  public function setProducerMetadata($producerMetadata)
  {
    $this->producerMetadata = $producerMetadata;
  }
  /**
   * @return string[]
   */
  public function getProducerMetadata()
  {
    return $this->producerMetadata;
  }
  /**
   * Output only. The list of data plane resources provisioned for this
   * instance, e.g. compute VMs. See go/get-instance-metadata.
   *
   * @param ProvisionedResource[] $provisionedResources
   */
  public function setProvisionedResources($provisionedResources)
  {
    $this->provisionedResources = $provisionedResources;
  }
  /**
   * @return ProvisionedResource[]
   */
  public function getProvisionedResources()
  {
    return $this->provisionedResources;
  }
  /**
   * Link to the SLM instance template. Only populated when updating SLM
   * instances via SSA's Actuation service adaptor. Service producers with
   * custom control plane (e.g. Cloud SQL) doesn't need to populate this field.
   * Instead they should use software_versions.
   *
   * @param string $slmInstanceTemplate
   */
  public function setSlmInstanceTemplate($slmInstanceTemplate)
  {
    $this->slmInstanceTemplate = $slmInstanceTemplate;
  }
  /**
   * @return string
   */
  public function getSlmInstanceTemplate()
  {
    return $this->slmInstanceTemplate;
  }
  /**
   * Output only. SLO metadata for instance classification in the Standardized
   * dataplane SLO platform. See go/cloud-ssa-standard-slo for feature
   * description.
   *
   * @param SloMetadata $sloMetadata
   */
  public function setSloMetadata(SloMetadata $sloMetadata)
  {
    $this->sloMetadata = $sloMetadata;
  }
  /**
   * @return SloMetadata
   */
  public function getSloMetadata()
  {
    return $this->sloMetadata;
  }
  /**
   * Software versions that are used to deploy this instance. This can be
   * mutated by rollout services.
   *
   * @param string[] $softwareVersions
   */
  public function setSoftwareVersions($softwareVersions)
  {
    $this->softwareVersions = $softwareVersions;
  }
  /**
   * @return string[]
   */
  public function getSoftwareVersions()
  {
    return $this->softwareVersions;
  }
  /**
   * Output only. Current lifecycle state of the resource (e.g. if it's being
   * created or ready to use).
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, UPDATING, REPAIRING,
   * DELETING, ERROR
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
   * Output only. ID of the associated GCP tenant project. See go/get-instance-
   * metadata.
   *
   * @param string $tenantProjectId
   */
  public function setTenantProjectId($tenantProjectId)
  {
    $this->tenantProjectId = $tenantProjectId;
  }
  /**
   * @return string
   */
  public function getTenantProjectId()
  {
    return $this->tenantProjectId;
  }
  /**
   * Output only. Timestamp when the resource was last modified.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instance::class, 'Google_Service_Connectors_Instance');

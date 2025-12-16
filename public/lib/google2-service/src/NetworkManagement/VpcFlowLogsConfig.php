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

namespace Google\Service\NetworkManagement;

class VpcFlowLogsConfig extends \Google\Collection
{
  /**
   * If not specified, will default to INTERVAL_5_SEC.
   */
  public const AGGREGATION_INTERVAL_AGGREGATION_INTERVAL_UNSPECIFIED = 'AGGREGATION_INTERVAL_UNSPECIFIED';
  /**
   * Aggregate logs in 5s intervals.
   */
  public const AGGREGATION_INTERVAL_INTERVAL_5_SEC = 'INTERVAL_5_SEC';
  /**
   * Aggregate logs in 30s intervals.
   */
  public const AGGREGATION_INTERVAL_INTERVAL_30_SEC = 'INTERVAL_30_SEC';
  /**
   * Aggregate logs in 1m intervals.
   */
  public const AGGREGATION_INTERVAL_INTERVAL_1_MIN = 'INTERVAL_1_MIN';
  /**
   * Aggregate logs in 5m intervals.
   */
  public const AGGREGATION_INTERVAL_INTERVAL_5_MIN = 'INTERVAL_5_MIN';
  /**
   * Aggregate logs in 10m intervals.
   */
  public const AGGREGATION_INTERVAL_INTERVAL_10_MIN = 'INTERVAL_10_MIN';
  /**
   * Aggregate logs in 15m intervals.
   */
  public const AGGREGATION_INTERVAL_INTERVAL_15_MIN = 'INTERVAL_15_MIN';
  /**
   * If not specified, the default is CROSS_PROJECT_METADATA_ENABLED.
   */
  public const CROSS_PROJECT_METADATA_CROSS_PROJECT_METADATA_UNSPECIFIED = 'CROSS_PROJECT_METADATA_UNSPECIFIED';
  /**
   * When CROSS_PROJECT_METADATA_ENABLED, metadata from other projects will be
   * included in the logs.
   */
  public const CROSS_PROJECT_METADATA_CROSS_PROJECT_METADATA_ENABLED = 'CROSS_PROJECT_METADATA_ENABLED';
  /**
   * When CROSS_PROJECT_METADATA_DISABLED, metadata from other projects will not
   * be included in the logs.
   */
  public const CROSS_PROJECT_METADATA_CROSS_PROJECT_METADATA_DISABLED = 'CROSS_PROJECT_METADATA_DISABLED';
  /**
   * If not specified, will default to INCLUDE_ALL_METADATA.
   */
  public const METADATA_METADATA_UNSPECIFIED = 'METADATA_UNSPECIFIED';
  /**
   * Include all metadata fields.
   */
  public const METADATA_INCLUDE_ALL_METADATA = 'INCLUDE_ALL_METADATA';
  /**
   * Exclude all metadata fields.
   */
  public const METADATA_EXCLUDE_ALL_METADATA = 'EXCLUDE_ALL_METADATA';
  /**
   * Include only custom fields (specified in metadata_fields).
   */
  public const METADATA_CUSTOM_METADATA = 'CUSTOM_METADATA';
  /**
   * If not specified, will default to ENABLED.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * When ENABLED, this configuration will generate logs.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * When DISABLED, this configuration will not generate logs.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Unspecified target resource state.
   */
  public const TARGET_RESOURCE_STATE_TARGET_RESOURCE_STATE_UNSPECIFIED = 'TARGET_RESOURCE_STATE_UNSPECIFIED';
  /**
   * Indicates that the target resource exists.
   */
  public const TARGET_RESOURCE_STATE_TARGET_RESOURCE_EXISTS = 'TARGET_RESOURCE_EXISTS';
  /**
   * Indicates that the target resource does not exist.
   */
  public const TARGET_RESOURCE_STATE_TARGET_RESOURCE_DOES_NOT_EXIST = 'TARGET_RESOURCE_DOES_NOT_EXIST';
  protected $collection_key = 'metadataFields';
  /**
   * Optional. The aggregation interval for the logs. Default value is
   * INTERVAL_5_SEC.
   *
   * @var string
   */
  public $aggregationInterval;
  /**
   * Output only. The time the config was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Determines whether to include cross project annotations in the
   * logs. This field is available only for organization configurations. If not
   * specified in org configs will be set to CROSS_PROJECT_METADATA_ENABLED.
   *
   * @var string
   */
  public $crossProjectMetadata;
  /**
   * Optional. The user-supplied description of the VPC Flow Logs configuration.
   * Maximum of 512 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Export filter used to define which VPC Flow Logs should be
   * logged.
   *
   * @var string
   */
  public $filterExpr;
  /**
   * Optional. The value of the field must be in (0, 1]. The sampling rate of
   * VPC Flow Logs where 1.0 means all collected logs are reported. Setting the
   * sampling rate to 0.0 is not allowed. If you want to disable VPC Flow Logs,
   * use the state field instead. Default value is 1.0.
   *
   * @var float
   */
  public $flowSampling;
  /**
   * Traffic will be logged from the Interconnect Attachment. Format:
   * projects/{project_id}/regions/{region}/interconnectAttachments/{name}
   *
   * @var string
   */
  public $interconnectAttachment;
  /**
   * Optional. Resource labels to represent user-provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Configures whether all, none or a subset of metadata fields
   * should be added to the reported VPC flow logs. Default value is
   * INCLUDE_ALL_METADATA.
   *
   * @var string
   */
  public $metadata;
  /**
   * Optional. Custom metadata fields to include in the reported VPC flow logs.
   * Can only be specified if "metadata" was set to CUSTOM_METADATA.
   *
   * @var string[]
   */
  public $metadataFields;
  /**
   * Identifier. Unique name of the configuration. The name can have one of the
   * following forms: - For project-level configurations: `projects/{project_id}
   * /locations/global/vpcFlowLogsConfigs/{vpc_flow_logs_config_id}` - For
   * organization-level configurations: `organizations/{organization_id}/locatio
   * ns/global/vpcFlowLogsConfigs/{vpc_flow_logs_config_id}`
   *
   * @var string
   */
  public $name;
  /**
   * Traffic will be logged from VMs, VPN tunnels and Interconnect Attachments
   * within the network. Format: projects/{project_id}/global/networks/{name}
   *
   * @var string
   */
  public $network;
  /**
   * Optional. The state of the VPC Flow Log configuration. Default value is
   * ENABLED. When creating a new configuration, it must be enabled. Setting
   * state=DISABLED will pause the log generation for this config.
   *
   * @var string
   */
  public $state;
  /**
   * Traffic will be logged from VMs within the subnetwork. Format:
   * projects/{project_id}/regions/{region}/subnetworks/{name}
   *
   * @var string
   */
  public $subnet;
  /**
   * Output only. Describes the state of the configured target resource for
   * diagnostic purposes.
   *
   * @var string
   */
  public $targetResourceState;
  /**
   * Output only. The time the config was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Traffic will be logged from the VPN Tunnel. Format:
   * projects/{project_id}/regions/{region}/vpnTunnels/{name}
   *
   * @var string
   */
  public $vpnTunnel;

  /**
   * Optional. The aggregation interval for the logs. Default value is
   * INTERVAL_5_SEC.
   *
   * Accepted values: AGGREGATION_INTERVAL_UNSPECIFIED, INTERVAL_5_SEC,
   * INTERVAL_30_SEC, INTERVAL_1_MIN, INTERVAL_5_MIN, INTERVAL_10_MIN,
   * INTERVAL_15_MIN
   *
   * @param self::AGGREGATION_INTERVAL_* $aggregationInterval
   */
  public function setAggregationInterval($aggregationInterval)
  {
    $this->aggregationInterval = $aggregationInterval;
  }
  /**
   * @return self::AGGREGATION_INTERVAL_*
   */
  public function getAggregationInterval()
  {
    return $this->aggregationInterval;
  }
  /**
   * Output only. The time the config was created.
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
   * Optional. Determines whether to include cross project annotations in the
   * logs. This field is available only for organization configurations. If not
   * specified in org configs will be set to CROSS_PROJECT_METADATA_ENABLED.
   *
   * Accepted values: CROSS_PROJECT_METADATA_UNSPECIFIED,
   * CROSS_PROJECT_METADATA_ENABLED, CROSS_PROJECT_METADATA_DISABLED
   *
   * @param self::CROSS_PROJECT_METADATA_* $crossProjectMetadata
   */
  public function setCrossProjectMetadata($crossProjectMetadata)
  {
    $this->crossProjectMetadata = $crossProjectMetadata;
  }
  /**
   * @return self::CROSS_PROJECT_METADATA_*
   */
  public function getCrossProjectMetadata()
  {
    return $this->crossProjectMetadata;
  }
  /**
   * Optional. The user-supplied description of the VPC Flow Logs configuration.
   * Maximum of 512 characters.
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
   * Optional. Export filter used to define which VPC Flow Logs should be
   * logged.
   *
   * @param string $filterExpr
   */
  public function setFilterExpr($filterExpr)
  {
    $this->filterExpr = $filterExpr;
  }
  /**
   * @return string
   */
  public function getFilterExpr()
  {
    return $this->filterExpr;
  }
  /**
   * Optional. The value of the field must be in (0, 1]. The sampling rate of
   * VPC Flow Logs where 1.0 means all collected logs are reported. Setting the
   * sampling rate to 0.0 is not allowed. If you want to disable VPC Flow Logs,
   * use the state field instead. Default value is 1.0.
   *
   * @param float $flowSampling
   */
  public function setFlowSampling($flowSampling)
  {
    $this->flowSampling = $flowSampling;
  }
  /**
   * @return float
   */
  public function getFlowSampling()
  {
    return $this->flowSampling;
  }
  /**
   * Traffic will be logged from the Interconnect Attachment. Format:
   * projects/{project_id}/regions/{region}/interconnectAttachments/{name}
   *
   * @param string $interconnectAttachment
   */
  public function setInterconnectAttachment($interconnectAttachment)
  {
    $this->interconnectAttachment = $interconnectAttachment;
  }
  /**
   * @return string
   */
  public function getInterconnectAttachment()
  {
    return $this->interconnectAttachment;
  }
  /**
   * Optional. Resource labels to represent user-provided metadata.
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
   * Optional. Configures whether all, none or a subset of metadata fields
   * should be added to the reported VPC flow logs. Default value is
   * INCLUDE_ALL_METADATA.
   *
   * Accepted values: METADATA_UNSPECIFIED, INCLUDE_ALL_METADATA,
   * EXCLUDE_ALL_METADATA, CUSTOM_METADATA
   *
   * @param self::METADATA_* $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return self::METADATA_*
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Optional. Custom metadata fields to include in the reported VPC flow logs.
   * Can only be specified if "metadata" was set to CUSTOM_METADATA.
   *
   * @param string[] $metadataFields
   */
  public function setMetadataFields($metadataFields)
  {
    $this->metadataFields = $metadataFields;
  }
  /**
   * @return string[]
   */
  public function getMetadataFields()
  {
    return $this->metadataFields;
  }
  /**
   * Identifier. Unique name of the configuration. The name can have one of the
   * following forms: - For project-level configurations: `projects/{project_id}
   * /locations/global/vpcFlowLogsConfigs/{vpc_flow_logs_config_id}` - For
   * organization-level configurations: `organizations/{organization_id}/locatio
   * ns/global/vpcFlowLogsConfigs/{vpc_flow_logs_config_id}`
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
   * Traffic will be logged from VMs, VPN tunnels and Interconnect Attachments
   * within the network. Format: projects/{project_id}/global/networks/{name}
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. The state of the VPC Flow Log configuration. Default value is
   * ENABLED. When creating a new configuration, it must be enabled. Setting
   * state=DISABLED will pause the log generation for this config.
   *
   * Accepted values: STATE_UNSPECIFIED, ENABLED, DISABLED
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
   * Traffic will be logged from VMs within the subnetwork. Format:
   * projects/{project_id}/regions/{region}/subnetworks/{name}
   *
   * @param string $subnet
   */
  public function setSubnet($subnet)
  {
    $this->subnet = $subnet;
  }
  /**
   * @return string
   */
  public function getSubnet()
  {
    return $this->subnet;
  }
  /**
   * Output only. Describes the state of the configured target resource for
   * diagnostic purposes.
   *
   * Accepted values: TARGET_RESOURCE_STATE_UNSPECIFIED, TARGET_RESOURCE_EXISTS,
   * TARGET_RESOURCE_DOES_NOT_EXIST
   *
   * @param self::TARGET_RESOURCE_STATE_* $targetResourceState
   */
  public function setTargetResourceState($targetResourceState)
  {
    $this->targetResourceState = $targetResourceState;
  }
  /**
   * @return self::TARGET_RESOURCE_STATE_*
   */
  public function getTargetResourceState()
  {
    return $this->targetResourceState;
  }
  /**
   * Output only. The time the config was updated.
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
   * Traffic will be logged from the VPN Tunnel. Format:
   * projects/{project_id}/regions/{region}/vpnTunnels/{name}
   *
   * @param string $vpnTunnel
   */
  public function setVpnTunnel($vpnTunnel)
  {
    $this->vpnTunnel = $vpnTunnel;
  }
  /**
   * @return string
   */
  public function getVpnTunnel()
  {
    return $this->vpnTunnel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpcFlowLogsConfig::class, 'Google_Service_NetworkManagement_VpcFlowLogsConfig');

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

class EffectiveVpcFlowLogsConfig extends \Google\Collection
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
   * Scope is unspecified.
   */
  public const SCOPE_SCOPE_UNSPECIFIED = 'SCOPE_UNSPECIFIED';
  /**
   * Target resource is a subnet (Network Management API).
   */
  public const SCOPE_SUBNET = 'SUBNET';
  /**
   * Target resource is a subnet, and the config originates from the Compute
   * API.
   */
  public const SCOPE_COMPUTE_API_SUBNET = 'COMPUTE_API_SUBNET';
  /**
   * Target resource is a network.
   */
  public const SCOPE_NETWORK = 'NETWORK';
  /**
   * Target resource is a VPN tunnel.
   */
  public const SCOPE_VPN_TUNNEL = 'VPN_TUNNEL';
  /**
   * Target resource is an interconnect attachment.
   */
  public const SCOPE_INTERCONNECT_ATTACHMENT = 'INTERCONNECT_ATTACHMENT';
  /**
   * Configuration applies to an entire organization.
   */
  public const SCOPE_ORGANIZATION = 'ORGANIZATION';
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
  protected $collection_key = 'metadataFields';
  /**
   * The aggregation interval for the logs. Default value is INTERVAL_5_SEC.
   *
   * @var string
   */
  public $aggregationInterval;
  /**
   * Determines whether to include cross project annotations in the logs. This
   * field is available only for organization configurations. If not specified
   * in org configs will be set to CROSS_PROJECT_METADATA_ENABLED.
   *
   * @var string
   */
  public $crossProjectMetadata;
  /**
   * Export filter used to define which VPC Flow Logs should be logged.
   *
   * @var string
   */
  public $filterExpr;
  /**
   * The value of the field must be in (0, 1]. The sampling rate of VPC Flow
   * Logs where 1.0 means all collected logs are reported. Setting the sampling
   * rate to 0.0 is not allowed. If you want to disable VPC Flow Logs, use the
   * state field instead. Default value is 1.0.
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
   * Configures whether all, none or a subset of metadata fields should be added
   * to the reported VPC flow logs. Default value is INCLUDE_ALL_METADATA.
   *
   * @var string
   */
  public $metadata;
  /**
   * Custom metadata fields to include in the reported VPC flow logs. Can only
   * be specified if "metadata" was set to CUSTOM_METADATA.
   *
   * @var string[]
   */
  public $metadataFields;
  /**
   * Unique name of the configuration. The name can have one of the following
   * forms: - For project-level configurations: `projects/{project_id}/locations
   * /global/vpcFlowLogsConfigs/{vpc_flow_logs_config_id}` - For organization-
   * level configurations: `organizations/{organization_id}/locations/global/vpc
   * FlowLogsConfigs/{vpc_flow_logs_config_id}` - For a Compute config, the name
   * will be the path of the subnet:
   * `projects/{project_id}/regions/{region}/subnetworks/{subnet_id}`
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
   * Specifies the scope of the config (e.g., SUBNET, NETWORK, ORGANIZATION..).
   *
   * @var string
   */
  public $scope;
  /**
   * The state of the VPC Flow Log configuration. Default value is ENABLED. When
   * creating a new configuration, it must be enabled. Setting state=DISABLED
   * will pause the log generation for this config.
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
   * Traffic will be logged from the VPN Tunnel. Format:
   * projects/{project_id}/regions/{region}/vpnTunnels/{name}
   *
   * @var string
   */
  public $vpnTunnel;

  /**
   * The aggregation interval for the logs. Default value is INTERVAL_5_SEC.
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
   * Determines whether to include cross project annotations in the logs. This
   * field is available only for organization configurations. If not specified
   * in org configs will be set to CROSS_PROJECT_METADATA_ENABLED.
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
   * Export filter used to define which VPC Flow Logs should be logged.
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
   * The value of the field must be in (0, 1]. The sampling rate of VPC Flow
   * Logs where 1.0 means all collected logs are reported. Setting the sampling
   * rate to 0.0 is not allowed. If you want to disable VPC Flow Logs, use the
   * state field instead. Default value is 1.0.
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
   * Configures whether all, none or a subset of metadata fields should be added
   * to the reported VPC flow logs. Default value is INCLUDE_ALL_METADATA.
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
   * Custom metadata fields to include in the reported VPC flow logs. Can only
   * be specified if "metadata" was set to CUSTOM_METADATA.
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
   * Unique name of the configuration. The name can have one of the following
   * forms: - For project-level configurations: `projects/{project_id}/locations
   * /global/vpcFlowLogsConfigs/{vpc_flow_logs_config_id}` - For organization-
   * level configurations: `organizations/{organization_id}/locations/global/vpc
   * FlowLogsConfigs/{vpc_flow_logs_config_id}` - For a Compute config, the name
   * will be the path of the subnet:
   * `projects/{project_id}/regions/{region}/subnetworks/{subnet_id}`
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
   * Specifies the scope of the config (e.g., SUBNET, NETWORK, ORGANIZATION..).
   *
   * Accepted values: SCOPE_UNSPECIFIED, SUBNET, COMPUTE_API_SUBNET, NETWORK,
   * VPN_TUNNEL, INTERCONNECT_ATTACHMENT, ORGANIZATION
   *
   * @param self::SCOPE_* $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return self::SCOPE_*
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * The state of the VPC Flow Log configuration. Default value is ENABLED. When
   * creating a new configuration, it must be enabled. Setting state=DISABLED
   * will pause the log generation for this config.
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
class_alias(EffectiveVpcFlowLogsConfig::class, 'Google_Service_NetworkManagement_EffectiveVpcFlowLogsConfig');

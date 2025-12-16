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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1DeployedIndex extends \Google\Collection
{
  /**
   * Default deployment tier.
   */
  public const DEPLOYMENT_TIER_DEPLOYMENT_TIER_UNSPECIFIED = 'DEPLOYMENT_TIER_UNSPECIFIED';
  /**
   * Optimized for costs.
   */
  public const DEPLOYMENT_TIER_STORAGE = 'STORAGE';
  protected $collection_key = 'reservedIpRanges';
  protected $automaticResourcesType = GoogleCloudAiplatformV1AutomaticResources::class;
  protected $automaticResourcesDataType = '';
  /**
   * Output only. Timestamp when the DeployedIndex was created.
   *
   * @var string
   */
  public $createTime;
  protected $dedicatedResourcesType = GoogleCloudAiplatformV1DedicatedResources::class;
  protected $dedicatedResourcesDataType = '';
  protected $deployedIndexAuthConfigType = GoogleCloudAiplatformV1DeployedIndexAuthConfig::class;
  protected $deployedIndexAuthConfigDataType = '';
  /**
   * Optional. The deployment group can be no longer than 64 characters (eg:
   * 'test', 'prod'). If not set, we will use the 'default' deployment group.
   * Creating `deployment_groups` with `reserved_ip_ranges` is a recommended
   * practice when the peered network has multiple peering ranges. This creates
   * your deployments from predictable IP spaces for easier traffic
   * administration. Also, one deployment_group (except 'default') can only be
   * used with the same reserved_ip_ranges which means if the deployment_group
   * has been used with reserved_ip_ranges: [a, b, c], using it with [a, b] or
   * [d, e] is disallowed. Note: we only support up to 5 deployment groups(not
   * including 'default').
   *
   * @var string
   */
  public $deploymentGroup;
  /**
   * Optional. The deployment tier that the index is deployed to.
   * DEPLOYMENT_TIER_UNSPECIFIED will use a system-chosen default tier.
   *
   * @var string
   */
  public $deploymentTier;
  /**
   * The display name of the DeployedIndex. If not provided upon creation, the
   * Index's display_name is used.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. If true, private endpoint's access logs are sent to Cloud
   * Logging. These logs are like standard server access logs, containing
   * information like timestamp and latency for each MatchRequest. Note that
   * logs may incur a cost, especially if the deployed index receives a high
   * queries per second rate (QPS). Estimate your costs before enabling this
   * option.
   *
   * @var bool
   */
  public $enableAccessLogging;
  /**
   * Optional. If true, logs to Cloud Logging errors relating to datapoint
   * upserts. Under normal operation conditions, these log entries should be
   * very rare. However, if incompatible datapoint updates are being uploaded to
   * an index, a high volume of log entries may be generated in a short period
   * of time. Note that logs may incur a cost, especially if the deployed index
   * receives a high volume of datapoint upserts. Estimate your costs before
   * enabling this option.
   *
   * @var bool
   */
  public $enableDatapointUpsertLogging;
  /**
   * Required. The user specified ID of the DeployedIndex. The ID can be up to
   * 128 characters long and must start with a letter and only contain letters,
   * numbers, and underscores. The ID must be unique within the project it is
   * created in.
   *
   * @var string
   */
  public $id;
  /**
   * Required. The name of the Index this is the deployment of. We may refer to
   * this Index as the DeployedIndex's "original" Index.
   *
   * @var string
   */
  public $index;
  /**
   * Output only. The DeployedIndex may depend on various data on its original
   * Index. Additionally when certain changes to the original Index are being
   * done (e.g. when what the Index contains is being changed) the DeployedIndex
   * may be asynchronously updated in the background to reflect these changes.
   * If this timestamp's value is at least the Index.update_time of the original
   * Index, it means that this DeployedIndex and the original Index are in sync.
   * If this timestamp is older, then to see which updates this DeployedIndex
   * already contains (and which it does not), one must list the operations that
   * are running on the original Index. Only the successfully completed
   * Operations with update_time equal or before this sync time are contained in
   * this DeployedIndex.
   *
   * @var string
   */
  public $indexSyncTime;
  protected $privateEndpointsType = GoogleCloudAiplatformV1IndexPrivateEndpoints::class;
  protected $privateEndpointsDataType = '';
  protected $pscAutomationConfigsType = GoogleCloudAiplatformV1PSCAutomationConfig::class;
  protected $pscAutomationConfigsDataType = 'array';
  /**
   * Optional. A list of reserved ip ranges under the VPC network that can be
   * used for this DeployedIndex. If set, we will deploy the index within the
   * provided ip ranges. Otherwise, the index might be deployed to any ip ranges
   * under the provided VPC network. The value should be the name of the address
   * (https://cloud.google.com/compute/docs/reference/rest/v1/addresses)
   * Example: ['vertex-ai-ip-range']. For more information about subnets and
   * network IP ranges, please see https://cloud.google.com/vpc/docs/subnets#man
   * ually_created_subnet_ip_ranges.
   *
   * @var string[]
   */
  public $reservedIpRanges;

  /**
   * Optional. A description of resources that the DeployedIndex uses, which to
   * large degree are decided by Vertex AI, and optionally allows only a modest
   * additional configuration. If min_replica_count is not set, the default
   * value is 2 (we don't provide SLA when min_replica_count=1). If
   * max_replica_count is not set, the default value is min_replica_count. The
   * max allowed replica count is 1000.
   *
   * @param GoogleCloudAiplatformV1AutomaticResources $automaticResources
   */
  public function setAutomaticResources(GoogleCloudAiplatformV1AutomaticResources $automaticResources)
  {
    $this->automaticResources = $automaticResources;
  }
  /**
   * @return GoogleCloudAiplatformV1AutomaticResources
   */
  public function getAutomaticResources()
  {
    return $this->automaticResources;
  }
  /**
   * Output only. Timestamp when the DeployedIndex was created.
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
   * Optional. A description of resources that are dedicated to the
   * DeployedIndex, and that need a higher degree of manual configuration. The
   * field min_replica_count must be set to a value strictly greater than 0, or
   * else validation will fail. We don't provide SLA when min_replica_count=1.
   * If max_replica_count is not set, the default value is min_replica_count.
   * The max allowed replica count is 1000. Available machine types for SMALL
   * shard: e2-standard-2 and all machine types available for MEDIUM and LARGE
   * shard. Available machine types for MEDIUM shard: e2-standard-16 and all
   * machine types available for LARGE shard. Available machine types for LARGE
   * shard: e2-highmem-16, n2d-standard-32. n1-standard-16 and n1-standard-32
   * are still available, but we recommend e2-standard-16 and e2-highmem-16 for
   * cost efficiency.
   *
   * @param GoogleCloudAiplatformV1DedicatedResources $dedicatedResources
   */
  public function setDedicatedResources(GoogleCloudAiplatformV1DedicatedResources $dedicatedResources)
  {
    $this->dedicatedResources = $dedicatedResources;
  }
  /**
   * @return GoogleCloudAiplatformV1DedicatedResources
   */
  public function getDedicatedResources()
  {
    return $this->dedicatedResources;
  }
  /**
   * Optional. If set, the authentication is enabled for the private endpoint.
   *
   * @param GoogleCloudAiplatformV1DeployedIndexAuthConfig $deployedIndexAuthConfig
   */
  public function setDeployedIndexAuthConfig(GoogleCloudAiplatformV1DeployedIndexAuthConfig $deployedIndexAuthConfig)
  {
    $this->deployedIndexAuthConfig = $deployedIndexAuthConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1DeployedIndexAuthConfig
   */
  public function getDeployedIndexAuthConfig()
  {
    return $this->deployedIndexAuthConfig;
  }
  /**
   * Optional. The deployment group can be no longer than 64 characters (eg:
   * 'test', 'prod'). If not set, we will use the 'default' deployment group.
   * Creating `deployment_groups` with `reserved_ip_ranges` is a recommended
   * practice when the peered network has multiple peering ranges. This creates
   * your deployments from predictable IP spaces for easier traffic
   * administration. Also, one deployment_group (except 'default') can only be
   * used with the same reserved_ip_ranges which means if the deployment_group
   * has been used with reserved_ip_ranges: [a, b, c], using it with [a, b] or
   * [d, e] is disallowed. Note: we only support up to 5 deployment groups(not
   * including 'default').
   *
   * @param string $deploymentGroup
   */
  public function setDeploymentGroup($deploymentGroup)
  {
    $this->deploymentGroup = $deploymentGroup;
  }
  /**
   * @return string
   */
  public function getDeploymentGroup()
  {
    return $this->deploymentGroup;
  }
  /**
   * Optional. The deployment tier that the index is deployed to.
   * DEPLOYMENT_TIER_UNSPECIFIED will use a system-chosen default tier.
   *
   * Accepted values: DEPLOYMENT_TIER_UNSPECIFIED, STORAGE
   *
   * @param self::DEPLOYMENT_TIER_* $deploymentTier
   */
  public function setDeploymentTier($deploymentTier)
  {
    $this->deploymentTier = $deploymentTier;
  }
  /**
   * @return self::DEPLOYMENT_TIER_*
   */
  public function getDeploymentTier()
  {
    return $this->deploymentTier;
  }
  /**
   * The display name of the DeployedIndex. If not provided upon creation, the
   * Index's display_name is used.
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
   * Optional. If true, private endpoint's access logs are sent to Cloud
   * Logging. These logs are like standard server access logs, containing
   * information like timestamp and latency for each MatchRequest. Note that
   * logs may incur a cost, especially if the deployed index receives a high
   * queries per second rate (QPS). Estimate your costs before enabling this
   * option.
   *
   * @param bool $enableAccessLogging
   */
  public function setEnableAccessLogging($enableAccessLogging)
  {
    $this->enableAccessLogging = $enableAccessLogging;
  }
  /**
   * @return bool
   */
  public function getEnableAccessLogging()
  {
    return $this->enableAccessLogging;
  }
  /**
   * Optional. If true, logs to Cloud Logging errors relating to datapoint
   * upserts. Under normal operation conditions, these log entries should be
   * very rare. However, if incompatible datapoint updates are being uploaded to
   * an index, a high volume of log entries may be generated in a short period
   * of time. Note that logs may incur a cost, especially if the deployed index
   * receives a high volume of datapoint upserts. Estimate your costs before
   * enabling this option.
   *
   * @param bool $enableDatapointUpsertLogging
   */
  public function setEnableDatapointUpsertLogging($enableDatapointUpsertLogging)
  {
    $this->enableDatapointUpsertLogging = $enableDatapointUpsertLogging;
  }
  /**
   * @return bool
   */
  public function getEnableDatapointUpsertLogging()
  {
    return $this->enableDatapointUpsertLogging;
  }
  /**
   * Required. The user specified ID of the DeployedIndex. The ID can be up to
   * 128 characters long and must start with a letter and only contain letters,
   * numbers, and underscores. The ID must be unique within the project it is
   * created in.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Required. The name of the Index this is the deployment of. We may refer to
   * this Index as the DeployedIndex's "original" Index.
   *
   * @param string $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return string
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Output only. The DeployedIndex may depend on various data on its original
   * Index. Additionally when certain changes to the original Index are being
   * done (e.g. when what the Index contains is being changed) the DeployedIndex
   * may be asynchronously updated in the background to reflect these changes.
   * If this timestamp's value is at least the Index.update_time of the original
   * Index, it means that this DeployedIndex and the original Index are in sync.
   * If this timestamp is older, then to see which updates this DeployedIndex
   * already contains (and which it does not), one must list the operations that
   * are running on the original Index. Only the successfully completed
   * Operations with update_time equal or before this sync time are contained in
   * this DeployedIndex.
   *
   * @param string $indexSyncTime
   */
  public function setIndexSyncTime($indexSyncTime)
  {
    $this->indexSyncTime = $indexSyncTime;
  }
  /**
   * @return string
   */
  public function getIndexSyncTime()
  {
    return $this->indexSyncTime;
  }
  /**
   * Output only. Provides paths for users to send requests directly to the
   * deployed index services running on Cloud via private services access. This
   * field is populated if network is configured.
   *
   * @param GoogleCloudAiplatformV1IndexPrivateEndpoints $privateEndpoints
   */
  public function setPrivateEndpoints(GoogleCloudAiplatformV1IndexPrivateEndpoints $privateEndpoints)
  {
    $this->privateEndpoints = $privateEndpoints;
  }
  /**
   * @return GoogleCloudAiplatformV1IndexPrivateEndpoints
   */
  public function getPrivateEndpoints()
  {
    return $this->privateEndpoints;
  }
  /**
   * Optional. If set for PSC deployed index, PSC connection will be
   * automatically created after deployment is done and the endpoint information
   * is populated in private_endpoints.psc_automated_endpoints.
   *
   * @param GoogleCloudAiplatformV1PSCAutomationConfig[] $pscAutomationConfigs
   */
  public function setPscAutomationConfigs($pscAutomationConfigs)
  {
    $this->pscAutomationConfigs = $pscAutomationConfigs;
  }
  /**
   * @return GoogleCloudAiplatformV1PSCAutomationConfig[]
   */
  public function getPscAutomationConfigs()
  {
    return $this->pscAutomationConfigs;
  }
  /**
   * Optional. A list of reserved ip ranges under the VPC network that can be
   * used for this DeployedIndex. If set, we will deploy the index within the
   * provided ip ranges. Otherwise, the index might be deployed to any ip ranges
   * under the provided VPC network. The value should be the name of the address
   * (https://cloud.google.com/compute/docs/reference/rest/v1/addresses)
   * Example: ['vertex-ai-ip-range']. For more information about subnets and
   * network IP ranges, please see https://cloud.google.com/vpc/docs/subnets#man
   * ually_created_subnet_ip_ranges.
   *
   * @param string[] $reservedIpRanges
   */
  public function setReservedIpRanges($reservedIpRanges)
  {
    $this->reservedIpRanges = $reservedIpRanges;
  }
  /**
   * @return string[]
   */
  public function getReservedIpRanges()
  {
    return $this->reservedIpRanges;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeployedIndex::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeployedIndex');

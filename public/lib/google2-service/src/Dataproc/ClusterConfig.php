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

namespace Google\Service\Dataproc;

class ClusterConfig extends \Google\Collection
{
  /**
   * Not set. Works the same as CLUSTER_TIER_STANDARD.
   */
  public const CLUSTER_TIER_CLUSTER_TIER_UNSPECIFIED = 'CLUSTER_TIER_UNSPECIFIED';
  /**
   * Standard Dataproc cluster.
   */
  public const CLUSTER_TIER_CLUSTER_TIER_STANDARD = 'CLUSTER_TIER_STANDARD';
  /**
   * Premium Dataproc cluster.
   */
  public const CLUSTER_TIER_CLUSTER_TIER_PREMIUM = 'CLUSTER_TIER_PREMIUM';
  /**
   * Not set.
   */
  public const CLUSTER_TYPE_CLUSTER_TYPE_UNSPECIFIED = 'CLUSTER_TYPE_UNSPECIFIED';
  /**
   * Standard dataproc cluster with a minimum of two primary workers.
   */
  public const CLUSTER_TYPE_STANDARD = 'STANDARD';
  /**
   * https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/single-node-clusters
   */
  public const CLUSTER_TYPE_SINGLE_NODE = 'SINGLE_NODE';
  /**
   * Clusters that can use only secondary workers and be scaled down to zero
   * secondary worker nodes.
   */
  public const CLUSTER_TYPE_ZERO_SCALE = 'ZERO_SCALE';
  protected $collection_key = 'initializationActions';
  protected $autoscalingConfigType = AutoscalingConfig::class;
  protected $autoscalingConfigDataType = '';
  protected $auxiliaryNodeGroupsType = AuxiliaryNodeGroup::class;
  protected $auxiliaryNodeGroupsDataType = 'array';
  /**
   * Optional. The cluster tier.
   *
   * @var string
   */
  public $clusterTier;
  /**
   * Optional. The type of the cluster.
   *
   * @var string
   */
  public $clusterType;
  /**
   * Optional. A Cloud Storage bucket used to stage job dependencies, config
   * files, and job driver console output. If you do not specify a staging
   * bucket, Cloud Dataproc will determine a Cloud Storage location (US, ASIA,
   * or EU) for your cluster's staging bucket according to the Compute Engine
   * zone where your cluster is deployed, and then create and manage this
   * project-level, per-location bucket (see Dataproc staging and temp buckets
   * (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/staging-bucket)). This field requires a Cloud Storage bucket name,
   * not a gs://... URI to a Cloud Storage bucket.
   *
   * @var string
   */
  public $configBucket;
  protected $dataprocMetricConfigType = DataprocMetricConfig::class;
  protected $dataprocMetricConfigDataType = '';
  /**
   * Optional. A Cloud Storage bucket used to collect checkpoint diagnostic data
   * (https://cloud.google.com/dataproc/docs/support/diagnose-
   * clusters#checkpoint_diagnostic_data). If you do not specify a diagnostic
   * bucket, Cloud Dataproc will use the Dataproc temp bucket to collect the
   * checkpoint diagnostic data. This field requires a Cloud Storage bucket
   * name, not a gs://... URI to a Cloud Storage bucket.
   *
   * @var string
   */
  public $diagnosticBucket;
  protected $encryptionConfigType = EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  protected $endpointConfigType = EndpointConfig::class;
  protected $endpointConfigDataType = '';
  protected $gceClusterConfigType = GceClusterConfig::class;
  protected $gceClusterConfigDataType = '';
  protected $gkeClusterConfigType = GkeClusterConfig::class;
  protected $gkeClusterConfigDataType = '';
  protected $initializationActionsType = NodeInitializationAction::class;
  protected $initializationActionsDataType = 'array';
  protected $lifecycleConfigType = LifecycleConfig::class;
  protected $lifecycleConfigDataType = '';
  protected $masterConfigType = InstanceGroupConfig::class;
  protected $masterConfigDataType = '';
  protected $metastoreConfigType = MetastoreConfig::class;
  protected $metastoreConfigDataType = '';
  protected $secondaryWorkerConfigType = InstanceGroupConfig::class;
  protected $secondaryWorkerConfigDataType = '';
  protected $securityConfigType = SecurityConfig::class;
  protected $securityConfigDataType = '';
  protected $softwareConfigType = SoftwareConfig::class;
  protected $softwareConfigDataType = '';
  /**
   * Optional. A Cloud Storage bucket used to store ephemeral cluster and jobs
   * data, such as Spark and MapReduce history files. If you do not specify a
   * temp bucket, Dataproc will determine a Cloud Storage location (US, ASIA, or
   * EU) for your cluster's temp bucket according to the Compute Engine zone
   * where your cluster is deployed, and then create and manage this project-
   * level, per-location bucket. The default bucket has a TTL of 90 days, but
   * you can use any TTL (or none) if you specify a bucket (see Dataproc staging
   * and temp buckets
   * (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/staging-bucket)). This field requires a Cloud Storage bucket name,
   * not a gs://... URI to a Cloud Storage bucket.
   *
   * @var string
   */
  public $tempBucket;
  protected $workerConfigType = InstanceGroupConfig::class;
  protected $workerConfigDataType = '';

  /**
   * Optional. Autoscaling config for the policy associated with the cluster.
   * Cluster does not autoscale if this field is unset.
   *
   * @param AutoscalingConfig $autoscalingConfig
   */
  public function setAutoscalingConfig(AutoscalingConfig $autoscalingConfig)
  {
    $this->autoscalingConfig = $autoscalingConfig;
  }
  /**
   * @return AutoscalingConfig
   */
  public function getAutoscalingConfig()
  {
    return $this->autoscalingConfig;
  }
  /**
   * Optional. The node group settings.
   *
   * @param AuxiliaryNodeGroup[] $auxiliaryNodeGroups
   */
  public function setAuxiliaryNodeGroups($auxiliaryNodeGroups)
  {
    $this->auxiliaryNodeGroups = $auxiliaryNodeGroups;
  }
  /**
   * @return AuxiliaryNodeGroup[]
   */
  public function getAuxiliaryNodeGroups()
  {
    return $this->auxiliaryNodeGroups;
  }
  /**
   * Optional. The cluster tier.
   *
   * Accepted values: CLUSTER_TIER_UNSPECIFIED, CLUSTER_TIER_STANDARD,
   * CLUSTER_TIER_PREMIUM
   *
   * @param self::CLUSTER_TIER_* $clusterTier
   */
  public function setClusterTier($clusterTier)
  {
    $this->clusterTier = $clusterTier;
  }
  /**
   * @return self::CLUSTER_TIER_*
   */
  public function getClusterTier()
  {
    return $this->clusterTier;
  }
  /**
   * Optional. The type of the cluster.
   *
   * Accepted values: CLUSTER_TYPE_UNSPECIFIED, STANDARD, SINGLE_NODE,
   * ZERO_SCALE
   *
   * @param self::CLUSTER_TYPE_* $clusterType
   */
  public function setClusterType($clusterType)
  {
    $this->clusterType = $clusterType;
  }
  /**
   * @return self::CLUSTER_TYPE_*
   */
  public function getClusterType()
  {
    return $this->clusterType;
  }
  /**
   * Optional. A Cloud Storage bucket used to stage job dependencies, config
   * files, and job driver console output. If you do not specify a staging
   * bucket, Cloud Dataproc will determine a Cloud Storage location (US, ASIA,
   * or EU) for your cluster's staging bucket according to the Compute Engine
   * zone where your cluster is deployed, and then create and manage this
   * project-level, per-location bucket (see Dataproc staging and temp buckets
   * (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/staging-bucket)). This field requires a Cloud Storage bucket name,
   * not a gs://... URI to a Cloud Storage bucket.
   *
   * @param string $configBucket
   */
  public function setConfigBucket($configBucket)
  {
    $this->configBucket = $configBucket;
  }
  /**
   * @return string
   */
  public function getConfigBucket()
  {
    return $this->configBucket;
  }
  /**
   * Optional. The config for Dataproc metrics.
   *
   * @param DataprocMetricConfig $dataprocMetricConfig
   */
  public function setDataprocMetricConfig(DataprocMetricConfig $dataprocMetricConfig)
  {
    $this->dataprocMetricConfig = $dataprocMetricConfig;
  }
  /**
   * @return DataprocMetricConfig
   */
  public function getDataprocMetricConfig()
  {
    return $this->dataprocMetricConfig;
  }
  /**
   * Optional. A Cloud Storage bucket used to collect checkpoint diagnostic data
   * (https://cloud.google.com/dataproc/docs/support/diagnose-
   * clusters#checkpoint_diagnostic_data). If you do not specify a diagnostic
   * bucket, Cloud Dataproc will use the Dataproc temp bucket to collect the
   * checkpoint diagnostic data. This field requires a Cloud Storage bucket
   * name, not a gs://... URI to a Cloud Storage bucket.
   *
   * @param string $diagnosticBucket
   */
  public function setDiagnosticBucket($diagnosticBucket)
  {
    $this->diagnosticBucket = $diagnosticBucket;
  }
  /**
   * @return string
   */
  public function getDiagnosticBucket()
  {
    return $this->diagnosticBucket;
  }
  /**
   * Optional. Encryption settings for the cluster.
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
   * Optional. Port/endpoint configuration for this cluster
   *
   * @param EndpointConfig $endpointConfig
   */
  public function setEndpointConfig(EndpointConfig $endpointConfig)
  {
    $this->endpointConfig = $endpointConfig;
  }
  /**
   * @return EndpointConfig
   */
  public function getEndpointConfig()
  {
    return $this->endpointConfig;
  }
  /**
   * Optional. The shared Compute Engine config settings for all instances in a
   * cluster.
   *
   * @param GceClusterConfig $gceClusterConfig
   */
  public function setGceClusterConfig(GceClusterConfig $gceClusterConfig)
  {
    $this->gceClusterConfig = $gceClusterConfig;
  }
  /**
   * @return GceClusterConfig
   */
  public function getGceClusterConfig()
  {
    return $this->gceClusterConfig;
  }
  /**
   * Optional. BETA. The Kubernetes Engine config for Dataproc clusters deployed
   * to The Kubernetes Engine config for Dataproc clusters deployed to
   * Kubernetes. These config settings are mutually exclusive with Compute
   * Engine-based options, such as gce_cluster_config, master_config,
   * worker_config, secondary_worker_config, and autoscaling_config.
   *
   * @deprecated
   * @param GkeClusterConfig $gkeClusterConfig
   */
  public function setGkeClusterConfig(GkeClusterConfig $gkeClusterConfig)
  {
    $this->gkeClusterConfig = $gkeClusterConfig;
  }
  /**
   * @deprecated
   * @return GkeClusterConfig
   */
  public function getGkeClusterConfig()
  {
    return $this->gkeClusterConfig;
  }
  /**
   * Optional. Commands to execute on each node after config is completed. By
   * default, executables are run on master and all worker nodes. You can test a
   * node's role metadata to run an executable on a master or worker node, as
   * shown below using curl (you can also use wget): ROLE=$(curl -H Metadata-
   * Flavor:Google
   * http://metadata/computeMetadata/v1/instance/attributes/dataproc-role) if [[
   * "${ROLE}" == 'Master' ]]; then ... master specific actions ... else ...
   * worker specific actions ... fi
   *
   * @param NodeInitializationAction[] $initializationActions
   */
  public function setInitializationActions($initializationActions)
  {
    $this->initializationActions = $initializationActions;
  }
  /**
   * @return NodeInitializationAction[]
   */
  public function getInitializationActions()
  {
    return $this->initializationActions;
  }
  /**
   * Optional. Lifecycle setting for the cluster.
   *
   * @param LifecycleConfig $lifecycleConfig
   */
  public function setLifecycleConfig(LifecycleConfig $lifecycleConfig)
  {
    $this->lifecycleConfig = $lifecycleConfig;
  }
  /**
   * @return LifecycleConfig
   */
  public function getLifecycleConfig()
  {
    return $this->lifecycleConfig;
  }
  /**
   * Optional. The Compute Engine config settings for the cluster's master
   * instance.
   *
   * @param InstanceGroupConfig $masterConfig
   */
  public function setMasterConfig(InstanceGroupConfig $masterConfig)
  {
    $this->masterConfig = $masterConfig;
  }
  /**
   * @return InstanceGroupConfig
   */
  public function getMasterConfig()
  {
    return $this->masterConfig;
  }
  /**
   * Optional. Metastore configuration.
   *
   * @param MetastoreConfig $metastoreConfig
   */
  public function setMetastoreConfig(MetastoreConfig $metastoreConfig)
  {
    $this->metastoreConfig = $metastoreConfig;
  }
  /**
   * @return MetastoreConfig
   */
  public function getMetastoreConfig()
  {
    return $this->metastoreConfig;
  }
  /**
   * Optional. The Compute Engine config settings for a cluster's secondary
   * worker instances
   *
   * @param InstanceGroupConfig $secondaryWorkerConfig
   */
  public function setSecondaryWorkerConfig(InstanceGroupConfig $secondaryWorkerConfig)
  {
    $this->secondaryWorkerConfig = $secondaryWorkerConfig;
  }
  /**
   * @return InstanceGroupConfig
   */
  public function getSecondaryWorkerConfig()
  {
    return $this->secondaryWorkerConfig;
  }
  /**
   * Optional. Security settings for the cluster.
   *
   * @param SecurityConfig $securityConfig
   */
  public function setSecurityConfig(SecurityConfig $securityConfig)
  {
    $this->securityConfig = $securityConfig;
  }
  /**
   * @return SecurityConfig
   */
  public function getSecurityConfig()
  {
    return $this->securityConfig;
  }
  /**
   * Optional. The config settings for cluster software.
   *
   * @param SoftwareConfig $softwareConfig
   */
  public function setSoftwareConfig(SoftwareConfig $softwareConfig)
  {
    $this->softwareConfig = $softwareConfig;
  }
  /**
   * @return SoftwareConfig
   */
  public function getSoftwareConfig()
  {
    return $this->softwareConfig;
  }
  /**
   * Optional. A Cloud Storage bucket used to store ephemeral cluster and jobs
   * data, such as Spark and MapReduce history files. If you do not specify a
   * temp bucket, Dataproc will determine a Cloud Storage location (US, ASIA, or
   * EU) for your cluster's temp bucket according to the Compute Engine zone
   * where your cluster is deployed, and then create and manage this project-
   * level, per-location bucket. The default bucket has a TTL of 90 days, but
   * you can use any TTL (or none) if you specify a bucket (see Dataproc staging
   * and temp buckets
   * (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/staging-bucket)). This field requires a Cloud Storage bucket name,
   * not a gs://... URI to a Cloud Storage bucket.
   *
   * @param string $tempBucket
   */
  public function setTempBucket($tempBucket)
  {
    $this->tempBucket = $tempBucket;
  }
  /**
   * @return string
   */
  public function getTempBucket()
  {
    return $this->tempBucket;
  }
  /**
   * Optional. The Compute Engine config settings for the cluster's worker
   * instances.
   *
   * @param InstanceGroupConfig $workerConfig
   */
  public function setWorkerConfig(InstanceGroupConfig $workerConfig)
  {
    $this->workerConfig = $workerConfig;
  }
  /**
   * @return InstanceGroupConfig
   */
  public function getWorkerConfig()
  {
    return $this->workerConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterConfig::class, 'Google_Service_Dataproc_ClusterConfig');

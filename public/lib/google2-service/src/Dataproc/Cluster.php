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

class Cluster extends \Google\Collection
{
  protected $collection_key = 'statusHistory';
  /**
   * Required. The cluster name, which must be unique within a project. The name
   * must start with a lowercase letter, and can contain up to 51 lowercase
   * letters, numbers, and hyphens. It cannot end with a hyphen. The name of a
   * deleted cluster can be reused.
   *
   * @var string
   */
  public $clusterName;
  /**
   * Output only. A cluster UUID (Unique Universal Identifier). Dataproc
   * generates this value when it creates the cluster.
   *
   * @var string
   */
  public $clusterUuid;
  protected $configType = ClusterConfig::class;
  protected $configDataType = '';
  /**
   * Optional. The labels to associate with this cluster. Label keys must
   * contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). Label values may be empty, but, if
   * present, must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). No more than 32 labels can be
   * associated with a cluster.
   *
   * @var string[]
   */
  public $labels;
  protected $metricsType = ClusterMetrics::class;
  protected $metricsDataType = '';
  /**
   * Required. The Google Cloud Platform project ID that the cluster belongs to.
   *
   * @var string
   */
  public $projectId;
  protected $statusType = ClusterStatus::class;
  protected $statusDataType = '';
  protected $statusHistoryType = ClusterStatus::class;
  protected $statusHistoryDataType = 'array';
  protected $virtualClusterConfigType = VirtualClusterConfig::class;
  protected $virtualClusterConfigDataType = '';

  /**
   * Required. The cluster name, which must be unique within a project. The name
   * must start with a lowercase letter, and can contain up to 51 lowercase
   * letters, numbers, and hyphens. It cannot end with a hyphen. The name of a
   * deleted cluster can be reused.
   *
   * @param string $clusterName
   */
  public function setClusterName($clusterName)
  {
    $this->clusterName = $clusterName;
  }
  /**
   * @return string
   */
  public function getClusterName()
  {
    return $this->clusterName;
  }
  /**
   * Output only. A cluster UUID (Unique Universal Identifier). Dataproc
   * generates this value when it creates the cluster.
   *
   * @param string $clusterUuid
   */
  public function setClusterUuid($clusterUuid)
  {
    $this->clusterUuid = $clusterUuid;
  }
  /**
   * @return string
   */
  public function getClusterUuid()
  {
    return $this->clusterUuid;
  }
  /**
   * Optional. The cluster config for a cluster of Compute Engine Instances.
   * Note that Dataproc may set default values, and values may change when
   * clusters are updated.Exactly one of ClusterConfig or VirtualClusterConfig
   * must be specified.
   *
   * @param ClusterConfig $config
   */
  public function setConfig(ClusterConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return ClusterConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Optional. The labels to associate with this cluster. Label keys must
   * contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). Label values may be empty, but, if
   * present, must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). No more than 32 labels can be
   * associated with a cluster.
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
   * Output only. Contains cluster daemon metrics such as HDFS and YARN
   * stats.Beta Feature: This report is available for testing purposes only. It
   * may be changed before final release.
   *
   * @param ClusterMetrics $metrics
   */
  public function setMetrics(ClusterMetrics $metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return ClusterMetrics
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Required. The Google Cloud Platform project ID that the cluster belongs to.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Output only. Cluster status.
   *
   * @param ClusterStatus $status
   */
  public function setStatus(ClusterStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return ClusterStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. The previous cluster status.
   *
   * @param ClusterStatus[] $statusHistory
   */
  public function setStatusHistory($statusHistory)
  {
    $this->statusHistory = $statusHistory;
  }
  /**
   * @return ClusterStatus[]
   */
  public function getStatusHistory()
  {
    return $this->statusHistory;
  }
  /**
   * Optional. The virtual cluster config is used when creating a Dataproc
   * cluster that does not directly control the underlying compute resources,
   * for example, when creating a Dataproc-on-GKE cluster
   * (https://cloud.google.com/dataproc/docs/guides/dpgke/dataproc-gke-
   * overview). Dataproc may set default values, and values may change when
   * clusters are updated. Exactly one of config or virtual_cluster_config must
   * be specified.
   *
   * @param VirtualClusterConfig $virtualClusterConfig
   */
  public function setVirtualClusterConfig(VirtualClusterConfig $virtualClusterConfig)
  {
    $this->virtualClusterConfig = $virtualClusterConfig;
  }
  /**
   * @return VirtualClusterConfig
   */
  public function getVirtualClusterConfig()
  {
    return $this->virtualClusterConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Cluster::class, 'Google_Service_Dataproc_Cluster');

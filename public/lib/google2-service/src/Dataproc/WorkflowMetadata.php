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

class WorkflowMetadata extends \Google\Model
{
  /**
   * Unused.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * The operation has been created.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The operation is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The operation is done; either cancelled or completed.
   */
  public const STATE_DONE = 'DONE';
  /**
   * Output only. The name of the target cluster.
   *
   * @var string
   */
  public $clusterName;
  /**
   * Output only. The UUID of target cluster.
   *
   * @var string
   */
  public $clusterUuid;
  protected $createClusterType = ClusterOperation::class;
  protected $createClusterDataType = '';
  /**
   * Output only. DAG end time, only set for workflows with dag_timeout when DAG
   * ends.
   *
   * @var string
   */
  public $dagEndTime;
  /**
   * Output only. DAG start time, only set for workflows with dag_timeout when
   * DAG begins.
   *
   * @var string
   */
  public $dagStartTime;
  /**
   * Output only. The timeout duration for the DAG of jobs, expressed in seconds
   * (see JSON representation of duration
   * (https://developers.google.com/protocol-buffers/docs/proto3#json)).
   *
   * @var string
   */
  public $dagTimeout;
  protected $deleteClusterType = ClusterOperation::class;
  protected $deleteClusterDataType = '';
  /**
   * Output only. Workflow end time.
   *
   * @var string
   */
  public $endTime;
  protected $graphType = WorkflowGraph::class;
  protected $graphDataType = '';
  /**
   * Map from parameter names to values that were used for those parameters.
   *
   * @var string[]
   */
  public $parameters;
  /**
   * Output only. Workflow start time.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The workflow state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The resource name of the workflow template as described in
   * https://cloud.google.com/apis/design/resource_names. For
   * projects.regions.workflowTemplates, the resource name of the template has
   * the following format:
   * projects/{project_id}/regions/{region}/workflowTemplates/{template_id} For
   * projects.locations.workflowTemplates, the resource name of the template has
   * the following format:
   * projects/{project_id}/locations/{location}/workflowTemplates/{template_id}
   *
   * @var string
   */
  public $template;
  /**
   * Output only. The version of template at the time of workflow instantiation.
   *
   * @var int
   */
  public $version;

  /**
   * Output only. The name of the target cluster.
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
   * Output only. The UUID of target cluster.
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
   * Output only. The create cluster operation metadata.
   *
   * @param ClusterOperation $createCluster
   */
  public function setCreateCluster(ClusterOperation $createCluster)
  {
    $this->createCluster = $createCluster;
  }
  /**
   * @return ClusterOperation
   */
  public function getCreateCluster()
  {
    return $this->createCluster;
  }
  /**
   * Output only. DAG end time, only set for workflows with dag_timeout when DAG
   * ends.
   *
   * @param string $dagEndTime
   */
  public function setDagEndTime($dagEndTime)
  {
    $this->dagEndTime = $dagEndTime;
  }
  /**
   * @return string
   */
  public function getDagEndTime()
  {
    return $this->dagEndTime;
  }
  /**
   * Output only. DAG start time, only set for workflows with dag_timeout when
   * DAG begins.
   *
   * @param string $dagStartTime
   */
  public function setDagStartTime($dagStartTime)
  {
    $this->dagStartTime = $dagStartTime;
  }
  /**
   * @return string
   */
  public function getDagStartTime()
  {
    return $this->dagStartTime;
  }
  /**
   * Output only. The timeout duration for the DAG of jobs, expressed in seconds
   * (see JSON representation of duration
   * (https://developers.google.com/protocol-buffers/docs/proto3#json)).
   *
   * @param string $dagTimeout
   */
  public function setDagTimeout($dagTimeout)
  {
    $this->dagTimeout = $dagTimeout;
  }
  /**
   * @return string
   */
  public function getDagTimeout()
  {
    return $this->dagTimeout;
  }
  /**
   * Output only. The delete cluster operation metadata.
   *
   * @param ClusterOperation $deleteCluster
   */
  public function setDeleteCluster(ClusterOperation $deleteCluster)
  {
    $this->deleteCluster = $deleteCluster;
  }
  /**
   * @return ClusterOperation
   */
  public function getDeleteCluster()
  {
    return $this->deleteCluster;
  }
  /**
   * Output only. Workflow end time.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. The workflow graph.
   *
   * @param WorkflowGraph $graph
   */
  public function setGraph(WorkflowGraph $graph)
  {
    $this->graph = $graph;
  }
  /**
   * @return WorkflowGraph
   */
  public function getGraph()
  {
    return $this->graph;
  }
  /**
   * Map from parameter names to values that were used for those parameters.
   *
   * @param string[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return string[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Output only. Workflow start time.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The workflow state.
   *
   * Accepted values: UNKNOWN, PENDING, RUNNING, DONE
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
   * Output only. The resource name of the workflow template as described in
   * https://cloud.google.com/apis/design/resource_names. For
   * projects.regions.workflowTemplates, the resource name of the template has
   * the following format:
   * projects/{project_id}/regions/{region}/workflowTemplates/{template_id} For
   * projects.locations.workflowTemplates, the resource name of the template has
   * the following format:
   * projects/{project_id}/locations/{location}/workflowTemplates/{template_id}
   *
   * @param string $template
   */
  public function setTemplate($template)
  {
    $this->template = $template;
  }
  /**
   * @return string
   */
  public function getTemplate()
  {
    return $this->template;
  }
  /**
   * Output only. The version of template at the time of workflow instantiation.
   *
   * @param int $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return int
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkflowMetadata::class, 'Google_Service_Dataproc_WorkflowMetadata');

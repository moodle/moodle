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

class WorkflowTemplate extends \Google\Collection
{
  protected $collection_key = 'parameters';
  /**
   * Output only. The time template was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Timeout duration for the DAG of jobs, expressed in seconds (see
   * JSON representation of duration (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)). The timeout duration must be from 10 minutes
   * ("600s") to 24 hours ("86400s"). The timer begins when the first job is
   * submitted. If the workflow is running at the end of the timeout period, any
   * remaining jobs are cancelled, the workflow is ended, and if the workflow
   * was running on a managed cluster, the cluster is deleted.
   *
   * @var string
   */
  public $dagTimeout;
  protected $encryptionConfigType = GoogleCloudDataprocV1WorkflowTemplateEncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  /**
   * @var string
   */
  public $id;
  protected $jobsType = OrderedJob::class;
  protected $jobsDataType = 'array';
  /**
   * Optional. The labels to associate with this template. These labels will be
   * propagated to all jobs and clusters created by the workflow instance.Label
   * keys must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt).Label values may be empty, but, if
   * present, must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt).No more than 32 labels can be
   * associated with a template.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The resource name of the workflow template, as described in
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
  public $name;
  protected $parametersType = TemplateParameter::class;
  protected $parametersDataType = 'array';
  protected $placementType = WorkflowTemplatePlacement::class;
  protected $placementDataType = '';
  /**
   * Output only. The time template was last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Optional. Used to perform a consistent read-modify-write.This field should
   * be left blank for a CreateWorkflowTemplate request. It is required for an
   * UpdateWorkflowTemplate request, and must match the current server version.
   * A typical update template flow would fetch the current template with a
   * GetWorkflowTemplate request, which will return the current template with
   * the version field filled in with the current server version. The user
   * updates other fields in the template, then returns it as part of the
   * UpdateWorkflowTemplate request.
   *
   * @var int
   */
  public $version;

  /**
   * Output only. The time template was created.
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
   * Optional. Timeout duration for the DAG of jobs, expressed in seconds (see
   * JSON representation of duration (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)). The timeout duration must be from 10 minutes
   * ("600s") to 24 hours ("86400s"). The timer begins when the first job is
   * submitted. If the workflow is running at the end of the timeout period, any
   * remaining jobs are cancelled, the workflow is ended, and if the workflow
   * was running on a managed cluster, the cluster is deleted.
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
   * Optional. Encryption settings for encrypting workflow template job
   * arguments.
   *
   * @param GoogleCloudDataprocV1WorkflowTemplateEncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(GoogleCloudDataprocV1WorkflowTemplateEncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return GoogleCloudDataprocV1WorkflowTemplateEncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
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
   * Required. The Directed Acyclic Graph of Jobs to submit.
   *
   * @param OrderedJob[] $jobs
   */
  public function setJobs($jobs)
  {
    $this->jobs = $jobs;
  }
  /**
   * @return OrderedJob[]
   */
  public function getJobs()
  {
    return $this->jobs;
  }
  /**
   * Optional. The labels to associate with this template. These labels will be
   * propagated to all jobs and clusters created by the workflow instance.Label
   * keys must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt).Label values may be empty, but, if
   * present, must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt).No more than 32 labels can be
   * associated with a template.
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
   * Output only. The resource name of the workflow template, as described in
   * https://cloud.google.com/apis/design/resource_names. For
   * projects.regions.workflowTemplates, the resource name of the template has
   * the following format:
   * projects/{project_id}/regions/{region}/workflowTemplates/{template_id} For
   * projects.locations.workflowTemplates, the resource name of the template has
   * the following format:
   * projects/{project_id}/locations/{location}/workflowTemplates/{template_id}
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
   * Optional. Template parameters whose values are substituted into the
   * template. Values for parameters must be provided when the template is
   * instantiated.
   *
   * @param TemplateParameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return TemplateParameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Required. WorkflowTemplate scheduling information.
   *
   * @param WorkflowTemplatePlacement $placement
   */
  public function setPlacement(WorkflowTemplatePlacement $placement)
  {
    $this->placement = $placement;
  }
  /**
   * @return WorkflowTemplatePlacement
   */
  public function getPlacement()
  {
    return $this->placement;
  }
  /**
   * Output only. The time template was last updated.
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
   * Optional. Used to perform a consistent read-modify-write.This field should
   * be left blank for a CreateWorkflowTemplate request. It is required for an
   * UpdateWorkflowTemplate request, and must match the current server version.
   * A typical update template flow would fetch the current template with a
   * GetWorkflowTemplate request, which will return the current template with
   * the version field filled in with the current server version. The user
   * updates other fields in the template, then returns it as part of the
   * UpdateWorkflowTemplate request.
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
class_alias(WorkflowTemplate::class, 'Google_Service_Dataproc_WorkflowTemplate');

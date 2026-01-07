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

class GoogleCloudAiplatformV1PipelineJob extends \Google\Collection
{
  /**
   * The pipeline state is unspecified.
   */
  public const STATE_PIPELINE_STATE_UNSPECIFIED = 'PIPELINE_STATE_UNSPECIFIED';
  /**
   * The pipeline has been created or resumed, and processing has not yet begun.
   */
  public const STATE_PIPELINE_STATE_QUEUED = 'PIPELINE_STATE_QUEUED';
  /**
   * The service is preparing to run the pipeline.
   */
  public const STATE_PIPELINE_STATE_PENDING = 'PIPELINE_STATE_PENDING';
  /**
   * The pipeline is in progress.
   */
  public const STATE_PIPELINE_STATE_RUNNING = 'PIPELINE_STATE_RUNNING';
  /**
   * The pipeline completed successfully.
   */
  public const STATE_PIPELINE_STATE_SUCCEEDED = 'PIPELINE_STATE_SUCCEEDED';
  /**
   * The pipeline failed.
   */
  public const STATE_PIPELINE_STATE_FAILED = 'PIPELINE_STATE_FAILED';
  /**
   * The pipeline is being cancelled. From this state, the pipeline may only go
   * to either PIPELINE_STATE_SUCCEEDED, PIPELINE_STATE_FAILED or
   * PIPELINE_STATE_CANCELLED.
   */
  public const STATE_PIPELINE_STATE_CANCELLING = 'PIPELINE_STATE_CANCELLING';
  /**
   * The pipeline has been cancelled.
   */
  public const STATE_PIPELINE_STATE_CANCELLED = 'PIPELINE_STATE_CANCELLED';
  /**
   * The pipeline has been stopped, and can be resumed.
   */
  public const STATE_PIPELINE_STATE_PAUSED = 'PIPELINE_STATE_PAUSED';
  protected $collection_key = 'reservedIpRanges';
  /**
   * Output only. Pipeline creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * The display name of the Pipeline. The name can be up to 128 characters long
   * and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Output only. Pipeline end time.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $jobDetailType = GoogleCloudAiplatformV1PipelineJobDetail::class;
  protected $jobDetailDataType = '';
  /**
   * The labels with user-defined metadata to organize PipelineJob. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. See https://goo.gl/xmQnxf for more
   * information and examples of labels. Note there is some reserved label key
   * for Vertex AI Pipelines. - `vertex-ai-pipelines-run-billing-id`, user set
   * value will get overrided.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The resource name of the PipelineJob.
   *
   * @var string
   */
  public $name;
  /**
   * The full name of the Compute Engine [network](/compute/docs/networks-and-
   * firewalls#networks) to which the Pipeline Job's workload should be peered.
   * For example, `projects/12345/global/networks/myVPC`.
   * [Format](/compute/docs/reference/rest/v1/networks/insert) is of the form
   * `projects/{project}/global/networks/{network}`. Where {project} is a
   * project number, as in `12345`, and {network} is a network name. Private
   * services access must already be configured for the network. Pipeline job
   * will apply the network configuration to the Google Cloud resources being
   * launched, if applied, such as Vertex AI Training or Dataflow job. If left
   * unspecified, the workload is not peered with any network.
   *
   * @var string
   */
  public $network;
  /**
   * The spec of the pipeline.
   *
   * @var array[]
   */
  public $pipelineSpec;
  /**
   * Optional. Whether to do component level validations before job creation.
   *
   * @var bool
   */
  public $preflightValidations;
  protected $pscInterfaceConfigType = GoogleCloudAiplatformV1PscInterfaceConfig::class;
  protected $pscInterfaceConfigDataType = '';
  /**
   * A list of names for the reserved ip ranges under the VPC network that can
   * be used for this Pipeline Job's workload. If set, we will deploy the
   * Pipeline Job's workload within the provided ip ranges. Otherwise, the job
   * will be deployed to any ip ranges under the provided VPC network. Example:
   * ['vertex-ai-ip-range'].
   *
   * @var string[]
   */
  public $reservedIpRanges;
  protected $runtimeConfigType = GoogleCloudAiplatformV1PipelineJobRuntimeConfig::class;
  protected $runtimeConfigDataType = '';
  /**
   * Output only. The schedule resource name. Only returned if the Pipeline is
   * created by Schedule API.
   *
   * @var string
   */
  public $scheduleName;
  /**
   * The service account that the pipeline workload runs as. If not specified,
   * the Compute Engine default service account in the project will be used. See
   * https://cloud.google.com/compute/docs/access/service-
   * accounts#default_service_account Users starting the pipeline must have the
   * `iam.serviceAccounts.actAs` permission on this service account.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. Pipeline start time.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The detailed state of the job.
   *
   * @var string
   */
  public $state;
  protected $templateMetadataType = GoogleCloudAiplatformV1PipelineTemplateMetadata::class;
  protected $templateMetadataDataType = '';
  /**
   * A template uri from where the PipelineJob.pipeline_spec, if empty, will be
   * downloaded. Currently, only uri from Vertex Template Registry & Gallery is
   * supported. Reference to https://cloud.google.com/vertex-
   * ai/docs/pipelines/create-pipeline-template.
   *
   * @var string
   */
  public $templateUri;
  /**
   * Output only. Timestamp when this PipelineJob was most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Pipeline creation time.
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
   * The display name of the Pipeline. The name can be up to 128 characters long
   * and can consist of any UTF-8 characters.
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
   * Customer-managed encryption key spec for a pipelineJob. If set, this
   * PipelineJob and all of its sub-resources will be secured by this key.
   *
   * @param GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
  /**
   * Output only. Pipeline end time.
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
   * Output only. The error that occurred during pipeline execution. Only
   * populated when the pipeline's state is FAILED or CANCELLED.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The details of pipeline run. Not available in the list view.
   *
   * @param GoogleCloudAiplatformV1PipelineJobDetail $jobDetail
   */
  public function setJobDetail(GoogleCloudAiplatformV1PipelineJobDetail $jobDetail)
  {
    $this->jobDetail = $jobDetail;
  }
  /**
   * @return GoogleCloudAiplatformV1PipelineJobDetail
   */
  public function getJobDetail()
  {
    return $this->jobDetail;
  }
  /**
   * The labels with user-defined metadata to organize PipelineJob. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. See https://goo.gl/xmQnxf for more
   * information and examples of labels. Note there is some reserved label key
   * for Vertex AI Pipelines. - `vertex-ai-pipelines-run-billing-id`, user set
   * value will get overrided.
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
   * Output only. The resource name of the PipelineJob.
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
   * The full name of the Compute Engine [network](/compute/docs/networks-and-
   * firewalls#networks) to which the Pipeline Job's workload should be peered.
   * For example, `projects/12345/global/networks/myVPC`.
   * [Format](/compute/docs/reference/rest/v1/networks/insert) is of the form
   * `projects/{project}/global/networks/{network}`. Where {project} is a
   * project number, as in `12345`, and {network} is a network name. Private
   * services access must already be configured for the network. Pipeline job
   * will apply the network configuration to the Google Cloud resources being
   * launched, if applied, such as Vertex AI Training or Dataflow job. If left
   * unspecified, the workload is not peered with any network.
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
   * The spec of the pipeline.
   *
   * @param array[] $pipelineSpec
   */
  public function setPipelineSpec($pipelineSpec)
  {
    $this->pipelineSpec = $pipelineSpec;
  }
  /**
   * @return array[]
   */
  public function getPipelineSpec()
  {
    return $this->pipelineSpec;
  }
  /**
   * Optional. Whether to do component level validations before job creation.
   *
   * @param bool $preflightValidations
   */
  public function setPreflightValidations($preflightValidations)
  {
    $this->preflightValidations = $preflightValidations;
  }
  /**
   * @return bool
   */
  public function getPreflightValidations()
  {
    return $this->preflightValidations;
  }
  /**
   * Optional. Configuration for PSC-I for PipelineJob.
   *
   * @param GoogleCloudAiplatformV1PscInterfaceConfig $pscInterfaceConfig
   */
  public function setPscInterfaceConfig(GoogleCloudAiplatformV1PscInterfaceConfig $pscInterfaceConfig)
  {
    $this->pscInterfaceConfig = $pscInterfaceConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1PscInterfaceConfig
   */
  public function getPscInterfaceConfig()
  {
    return $this->pscInterfaceConfig;
  }
  /**
   * A list of names for the reserved ip ranges under the VPC network that can
   * be used for this Pipeline Job's workload. If set, we will deploy the
   * Pipeline Job's workload within the provided ip ranges. Otherwise, the job
   * will be deployed to any ip ranges under the provided VPC network. Example:
   * ['vertex-ai-ip-range'].
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
  /**
   * Runtime config of the pipeline.
   *
   * @param GoogleCloudAiplatformV1PipelineJobRuntimeConfig $runtimeConfig
   */
  public function setRuntimeConfig(GoogleCloudAiplatformV1PipelineJobRuntimeConfig $runtimeConfig)
  {
    $this->runtimeConfig = $runtimeConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1PipelineJobRuntimeConfig
   */
  public function getRuntimeConfig()
  {
    return $this->runtimeConfig;
  }
  /**
   * Output only. The schedule resource name. Only returned if the Pipeline is
   * created by Schedule API.
   *
   * @param string $scheduleName
   */
  public function setScheduleName($scheduleName)
  {
    $this->scheduleName = $scheduleName;
  }
  /**
   * @return string
   */
  public function getScheduleName()
  {
    return $this->scheduleName;
  }
  /**
   * The service account that the pipeline workload runs as. If not specified,
   * the Compute Engine default service account in the project will be used. See
   * https://cloud.google.com/compute/docs/access/service-
   * accounts#default_service_account Users starting the pipeline must have the
   * `iam.serviceAccounts.actAs` permission on this service account.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Output only. Pipeline start time.
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
   * Output only. The detailed state of the job.
   *
   * Accepted values: PIPELINE_STATE_UNSPECIFIED, PIPELINE_STATE_QUEUED,
   * PIPELINE_STATE_PENDING, PIPELINE_STATE_RUNNING, PIPELINE_STATE_SUCCEEDED,
   * PIPELINE_STATE_FAILED, PIPELINE_STATE_CANCELLING, PIPELINE_STATE_CANCELLED,
   * PIPELINE_STATE_PAUSED
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
   * Output only. Pipeline template metadata. Will fill up fields if
   * PipelineJob.template_uri is from supported template registry.
   *
   * @param GoogleCloudAiplatformV1PipelineTemplateMetadata $templateMetadata
   */
  public function setTemplateMetadata(GoogleCloudAiplatformV1PipelineTemplateMetadata $templateMetadata)
  {
    $this->templateMetadata = $templateMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1PipelineTemplateMetadata
   */
  public function getTemplateMetadata()
  {
    return $this->templateMetadata;
  }
  /**
   * A template uri from where the PipelineJob.pipeline_spec, if empty, will be
   * downloaded. Currently, only uri from Vertex Template Registry & Gallery is
   * supported. Reference to https://cloud.google.com/vertex-
   * ai/docs/pipelines/create-pipeline-template.
   *
   * @param string $templateUri
   */
  public function setTemplateUri($templateUri)
  {
    $this->templateUri = $templateUri;
  }
  /**
   * @return string
   */
  public function getTemplateUri()
  {
    return $this->templateUri;
  }
  /**
   * Output only. Timestamp when this PipelineJob was most recently updated.
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
class_alias(GoogleCloudAiplatformV1PipelineJob::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PipelineJob');

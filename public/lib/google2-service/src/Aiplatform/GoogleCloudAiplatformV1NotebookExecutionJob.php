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

class GoogleCloudAiplatformV1NotebookExecutionJob extends \Google\Model
{
  /**
   * The job state is unspecified.
   */
  public const JOB_STATE_JOB_STATE_UNSPECIFIED = 'JOB_STATE_UNSPECIFIED';
  /**
   * The job has been just created or resumed and processing has not yet begun.
   */
  public const JOB_STATE_JOB_STATE_QUEUED = 'JOB_STATE_QUEUED';
  /**
   * The service is preparing to run the job.
   */
  public const JOB_STATE_JOB_STATE_PENDING = 'JOB_STATE_PENDING';
  /**
   * The job is in progress.
   */
  public const JOB_STATE_JOB_STATE_RUNNING = 'JOB_STATE_RUNNING';
  /**
   * The job completed successfully.
   */
  public const JOB_STATE_JOB_STATE_SUCCEEDED = 'JOB_STATE_SUCCEEDED';
  /**
   * The job failed.
   */
  public const JOB_STATE_JOB_STATE_FAILED = 'JOB_STATE_FAILED';
  /**
   * The job is being cancelled. From this state the job may only go to either
   * `JOB_STATE_SUCCEEDED`, `JOB_STATE_FAILED` or `JOB_STATE_CANCELLED`.
   */
  public const JOB_STATE_JOB_STATE_CANCELLING = 'JOB_STATE_CANCELLING';
  /**
   * The job has been cancelled.
   */
  public const JOB_STATE_JOB_STATE_CANCELLED = 'JOB_STATE_CANCELLED';
  /**
   * The job has been stopped, and can be resumed.
   */
  public const JOB_STATE_JOB_STATE_PAUSED = 'JOB_STATE_PAUSED';
  /**
   * The job has expired.
   */
  public const JOB_STATE_JOB_STATE_EXPIRED = 'JOB_STATE_EXPIRED';
  /**
   * The job is being updated. Only jobs in the `RUNNING` state can be updated.
   * After updating, the job goes back to the `RUNNING` state.
   */
  public const JOB_STATE_JOB_STATE_UPDATING = 'JOB_STATE_UPDATING';
  /**
   * The job is partially succeeded, some results may be missing due to errors.
   */
  public const JOB_STATE_JOB_STATE_PARTIALLY_SUCCEEDED = 'JOB_STATE_PARTIALLY_SUCCEEDED';
  /**
   * Output only. Timestamp when this NotebookExecutionJob was created.
   *
   * @var string
   */
  public $createTime;
  protected $customEnvironmentSpecType = GoogleCloudAiplatformV1NotebookExecutionJobCustomEnvironmentSpec::class;
  protected $customEnvironmentSpecDataType = '';
  protected $dataformRepositorySourceType = GoogleCloudAiplatformV1NotebookExecutionJobDataformRepositorySource::class;
  protected $dataformRepositorySourceDataType = '';
  protected $directNotebookSourceType = GoogleCloudAiplatformV1NotebookExecutionJobDirectNotebookSource::class;
  protected $directNotebookSourceDataType = '';
  /**
   * The display name of the NotebookExecutionJob. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Max running time of the execution job in seconds (default 86400s / 24 hrs).
   *
   * @var string
   */
  public $executionTimeout;
  /**
   * The user email to run the execution as. Only supported by Colab runtimes.
   *
   * @var string
   */
  public $executionUser;
  protected $gcsNotebookSourceType = GoogleCloudAiplatformV1NotebookExecutionJobGcsNotebookSource::class;
  protected $gcsNotebookSourceDataType = '';
  /**
   * The Cloud Storage location to upload the result to. Format: `gs://bucket-
   * name`
   *
   * @var string
   */
  public $gcsOutputUri;
  /**
   * Output only. The state of the NotebookExecutionJob.
   *
   * @var string
   */
  public $jobState;
  /**
   * The name of the kernel to use during notebook execution. If unset, the
   * default kernel is used.
   *
   * @var string
   */
  public $kernelName;
  /**
   * The labels with user-defined metadata to organize NotebookExecutionJobs.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. See
   * https://goo.gl/xmQnxf for more information and examples of labels. System
   * reserved label keys are prefixed with "aiplatform.googleapis.com/" and are
   * immutable.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The resource name of this NotebookExecutionJob. Format:
   * `projects/{project_id}/locations/{location}/notebookExecutionJobs/{job_id}`
   *
   * @var string
   */
  public $name;
  /**
   * The NotebookRuntimeTemplate to source compute configuration from.
   *
   * @var string
   */
  public $notebookRuntimeTemplateResourceName;
  /**
   * The Schedule resource name if this job is triggered by one. Format:
   * `projects/{project_id}/locations/{location}/schedules/{schedule_id}`
   *
   * @var string
   */
  public $scheduleResourceName;
  /**
   * The service account to run the execution as.
   *
   * @var string
   */
  public $serviceAccount;
  protected $statusType = GoogleRpcStatus::class;
  protected $statusDataType = '';
  /**
   * Output only. Timestamp when this NotebookExecutionJob was most recently
   * updated.
   *
   * @var string
   */
  public $updateTime;
  protected $workbenchRuntimeType = GoogleCloudAiplatformV1NotebookExecutionJobWorkbenchRuntime::class;
  protected $workbenchRuntimeDataType = '';

  /**
   * Output only. Timestamp when this NotebookExecutionJob was created.
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
   * The custom compute configuration for an execution job.
   *
   * @param GoogleCloudAiplatformV1NotebookExecutionJobCustomEnvironmentSpec $customEnvironmentSpec
   */
  public function setCustomEnvironmentSpec(GoogleCloudAiplatformV1NotebookExecutionJobCustomEnvironmentSpec $customEnvironmentSpec)
  {
    $this->customEnvironmentSpec = $customEnvironmentSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1NotebookExecutionJobCustomEnvironmentSpec
   */
  public function getCustomEnvironmentSpec()
  {
    return $this->customEnvironmentSpec;
  }
  /**
   * The Dataform Repository pointing to a single file notebook repository.
   *
   * @param GoogleCloudAiplatformV1NotebookExecutionJobDataformRepositorySource $dataformRepositorySource
   */
  public function setDataformRepositorySource(GoogleCloudAiplatformV1NotebookExecutionJobDataformRepositorySource $dataformRepositorySource)
  {
    $this->dataformRepositorySource = $dataformRepositorySource;
  }
  /**
   * @return GoogleCloudAiplatformV1NotebookExecutionJobDataformRepositorySource
   */
  public function getDataformRepositorySource()
  {
    return $this->dataformRepositorySource;
  }
  /**
   * The contents of an input notebook file.
   *
   * @param GoogleCloudAiplatformV1NotebookExecutionJobDirectNotebookSource $directNotebookSource
   */
  public function setDirectNotebookSource(GoogleCloudAiplatformV1NotebookExecutionJobDirectNotebookSource $directNotebookSource)
  {
    $this->directNotebookSource = $directNotebookSource;
  }
  /**
   * @return GoogleCloudAiplatformV1NotebookExecutionJobDirectNotebookSource
   */
  public function getDirectNotebookSource()
  {
    return $this->directNotebookSource;
  }
  /**
   * The display name of the NotebookExecutionJob. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
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
   * Customer-managed encryption key spec for the notebook execution job. This
   * field is auto-populated if the NotebookRuntimeTemplate has an encryption
   * spec.
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
   * Max running time of the execution job in seconds (default 86400s / 24 hrs).
   *
   * @param string $executionTimeout
   */
  public function setExecutionTimeout($executionTimeout)
  {
    $this->executionTimeout = $executionTimeout;
  }
  /**
   * @return string
   */
  public function getExecutionTimeout()
  {
    return $this->executionTimeout;
  }
  /**
   * The user email to run the execution as. Only supported by Colab runtimes.
   *
   * @param string $executionUser
   */
  public function setExecutionUser($executionUser)
  {
    $this->executionUser = $executionUser;
  }
  /**
   * @return string
   */
  public function getExecutionUser()
  {
    return $this->executionUser;
  }
  /**
   * The Cloud Storage url pointing to the ipynb file. Format:
   * `gs://bucket/notebook_file.ipynb`
   *
   * @param GoogleCloudAiplatformV1NotebookExecutionJobGcsNotebookSource $gcsNotebookSource
   */
  public function setGcsNotebookSource(GoogleCloudAiplatformV1NotebookExecutionJobGcsNotebookSource $gcsNotebookSource)
  {
    $this->gcsNotebookSource = $gcsNotebookSource;
  }
  /**
   * @return GoogleCloudAiplatformV1NotebookExecutionJobGcsNotebookSource
   */
  public function getGcsNotebookSource()
  {
    return $this->gcsNotebookSource;
  }
  /**
   * The Cloud Storage location to upload the result to. Format: `gs://bucket-
   * name`
   *
   * @param string $gcsOutputUri
   */
  public function setGcsOutputUri($gcsOutputUri)
  {
    $this->gcsOutputUri = $gcsOutputUri;
  }
  /**
   * @return string
   */
  public function getGcsOutputUri()
  {
    return $this->gcsOutputUri;
  }
  /**
   * Output only. The state of the NotebookExecutionJob.
   *
   * Accepted values: JOB_STATE_UNSPECIFIED, JOB_STATE_QUEUED,
   * JOB_STATE_PENDING, JOB_STATE_RUNNING, JOB_STATE_SUCCEEDED,
   * JOB_STATE_FAILED, JOB_STATE_CANCELLING, JOB_STATE_CANCELLED,
   * JOB_STATE_PAUSED, JOB_STATE_EXPIRED, JOB_STATE_UPDATING,
   * JOB_STATE_PARTIALLY_SUCCEEDED
   *
   * @param self::JOB_STATE_* $jobState
   */
  public function setJobState($jobState)
  {
    $this->jobState = $jobState;
  }
  /**
   * @return self::JOB_STATE_*
   */
  public function getJobState()
  {
    return $this->jobState;
  }
  /**
   * The name of the kernel to use during notebook execution. If unset, the
   * default kernel is used.
   *
   * @param string $kernelName
   */
  public function setKernelName($kernelName)
  {
    $this->kernelName = $kernelName;
  }
  /**
   * @return string
   */
  public function getKernelName()
  {
    return $this->kernelName;
  }
  /**
   * The labels with user-defined metadata to organize NotebookExecutionJobs.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. See
   * https://goo.gl/xmQnxf for more information and examples of labels. System
   * reserved label keys are prefixed with "aiplatform.googleapis.com/" and are
   * immutable.
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
   * Output only. The resource name of this NotebookExecutionJob. Format:
   * `projects/{project_id}/locations/{location}/notebookExecutionJobs/{job_id}`
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
   * The NotebookRuntimeTemplate to source compute configuration from.
   *
   * @param string $notebookRuntimeTemplateResourceName
   */
  public function setNotebookRuntimeTemplateResourceName($notebookRuntimeTemplateResourceName)
  {
    $this->notebookRuntimeTemplateResourceName = $notebookRuntimeTemplateResourceName;
  }
  /**
   * @return string
   */
  public function getNotebookRuntimeTemplateResourceName()
  {
    return $this->notebookRuntimeTemplateResourceName;
  }
  /**
   * The Schedule resource name if this job is triggered by one. Format:
   * `projects/{project_id}/locations/{location}/schedules/{schedule_id}`
   *
   * @param string $scheduleResourceName
   */
  public function setScheduleResourceName($scheduleResourceName)
  {
    $this->scheduleResourceName = $scheduleResourceName;
  }
  /**
   * @return string
   */
  public function getScheduleResourceName()
  {
    return $this->scheduleResourceName;
  }
  /**
   * The service account to run the execution as.
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
   * Output only. Populated when the NotebookExecutionJob is completed. When
   * there is an error during notebook execution, the error details are
   * populated.
   *
   * @param GoogleRpcStatus $status
   */
  public function setStatus(GoogleRpcStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. Timestamp when this NotebookExecutionJob was most recently
   * updated.
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
   * The Workbench runtime configuration to use for the notebook execution.
   *
   * @param GoogleCloudAiplatformV1NotebookExecutionJobWorkbenchRuntime $workbenchRuntime
   */
  public function setWorkbenchRuntime(GoogleCloudAiplatformV1NotebookExecutionJobWorkbenchRuntime $workbenchRuntime)
  {
    $this->workbenchRuntime = $workbenchRuntime;
  }
  /**
   * @return GoogleCloudAiplatformV1NotebookExecutionJobWorkbenchRuntime
   */
  public function getWorkbenchRuntime()
  {
    return $this->workbenchRuntime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NotebookExecutionJob::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NotebookExecutionJob');

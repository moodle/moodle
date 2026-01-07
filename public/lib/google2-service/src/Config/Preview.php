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

namespace Google\Service\Config;

class Preview extends \Google\Collection
{
  /**
   * No error code was specified.
   */
  public const ERROR_CODE_ERROR_CODE_UNSPECIFIED = 'ERROR_CODE_UNSPECIFIED';
  /**
   * Cloud Build failed due to a permissions issue.
   */
  public const ERROR_CODE_CLOUD_BUILD_PERMISSION_DENIED = 'CLOUD_BUILD_PERMISSION_DENIED';
  /**
   * Cloud Storage bucket failed to create due to a permissions issue.
   */
  public const ERROR_CODE_BUCKET_CREATION_PERMISSION_DENIED = 'BUCKET_CREATION_PERMISSION_DENIED';
  /**
   * Cloud Storage bucket failed for a non-permissions-related issue.
   */
  public const ERROR_CODE_BUCKET_CREATION_FAILED = 'BUCKET_CREATION_FAILED';
  /**
   * Acquiring lock on provided deployment reference failed.
   */
  public const ERROR_CODE_DEPLOYMENT_LOCK_ACQUIRE_FAILED = 'DEPLOYMENT_LOCK_ACQUIRE_FAILED';
  /**
   * Preview encountered an error when trying to access Cloud Build API.
   */
  public const ERROR_CODE_PREVIEW_BUILD_API_FAILED = 'PREVIEW_BUILD_API_FAILED';
  /**
   * Preview created a build but build failed and logs were generated.
   */
  public const ERROR_CODE_PREVIEW_BUILD_RUN_FAILED = 'PREVIEW_BUILD_RUN_FAILED';
  /**
   * Failed to import values from an external source.
   */
  public const ERROR_CODE_EXTERNAL_VALUE_SOURCE_IMPORT_FAILED = 'EXTERNAL_VALUE_SOURCE_IMPORT_FAILED';
  /**
   * Unspecified policy, default mode will be used.
   */
  public const PREVIEW_MODE_PREVIEW_MODE_UNSPECIFIED = 'PREVIEW_MODE_UNSPECIFIED';
  /**
   * DEFAULT mode generates an execution plan for reconciling current resource
   * state into expected resource state.
   */
  public const PREVIEW_MODE_DEFAULT = 'DEFAULT';
  /**
   * DELETE mode generates as execution plan for destroying current resources.
   */
  public const PREVIEW_MODE_DELETE = 'DELETE';
  /**
   * The default value. This value is used if the state is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The preview is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The preview has succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The preview is being applied.
   */
  public const STATE_APPLYING = 'APPLYING';
  /**
   * The preview is stale. A preview can become stale if a revision has been
   * applied after this preview was created.
   */
  public const STATE_STALE = 'STALE';
  /**
   * The preview is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The preview has encountered an unexpected error.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The preview has been deleted.
   */
  public const STATE_DELETED = 'DELETED';
  protected $collection_key = 'tfErrors';
  /**
   * Optional. Arbitrary key-value metadata storage e.g. to help client tools
   * identify preview during automation. See
   * https://google.aip.dev/148#annotations for details on format and size
   * limitations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Optional. User-defined location of Cloud Build logs, artifacts, and in
   * Google Cloud Storage. Format: `gs://{bucket}/{folder}` A default bucket
   * will be bootstrapped if the field is not set or empty Default Bucket
   * Format: `gs://--blueprint-config` Constraints: - The bucket needs to be in
   * the same project as the deployment - The path cannot be within the path of
   * `gcs_source` If omitted and deployment resource ref provided has
   * artifacts_gcs_bucket defined, that artifact bucket is used.
   *
   * @var string
   */
  public $artifactsGcsBucket;
  /**
   * Output only. Cloud Build instance UUID associated with this preview.
   *
   * @var string
   */
  public $build;
  /**
   * Output only. Time the preview was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Optional deployment reference. If specified, the preview will be
   * performed using the provided deployment's current state and use any
   * relevant fields from the deployment unless explicitly specified in the
   * preview create request.
   *
   * @var string
   */
  public $deployment;
  /**
   * Output only. Code describing any errors that may have occurred.
   *
   * @var string
   */
  public $errorCode;
  /**
   * Output only. Link to tf-error.ndjson file, which contains the full list of
   * the errors encountered during a Terraform preview. Format:
   * `gs://{bucket}/{object}`.
   *
   * @var string
   */
  public $errorLogs;
  protected $errorStatusType = Status::class;
  protected $errorStatusDataType = '';
  /**
   * Optional. User-defined labels for the preview.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Location of preview logs in `gs://{bucket}/{object}` format.
   *
   * @var string
   */
  public $logs;
  /**
   * Identifier. Resource name of the preview. Resource name can be user
   * provided or server generated ID if unspecified. Format:
   * `projects/{project}/locations/{location}/previews/{preview}`
   *
   * @var string
   */
  public $name;
  protected $previewArtifactsType = PreviewArtifacts::class;
  protected $previewArtifactsDataType = '';
  /**
   * Optional. Current mode of preview.
   *
   * @var string
   */
  public $previewMode;
  protected $providerConfigType = ProviderConfig::class;
  protected $providerConfigDataType = '';
  /**
   * Required. User-specified Service Account (SA) credentials to be used when
   * previewing resources. Format:
   * `projects/{projectID}/serviceAccounts/{serviceAccount}`
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. Current state of the preview.
   *
   * @var string
   */
  public $state;
  protected $terraformBlueprintType = TerraformBlueprint::class;
  protected $terraformBlueprintDataType = '';
  protected $tfErrorsType = TerraformError::class;
  protected $tfErrorsDataType = 'array';
  /**
   * Output only. The current Terraform version set on the preview. It is in the
   * format of "Major.Minor.Patch", for example, "1.3.10".
   *
   * @var string
   */
  public $tfVersion;
  /**
   * Optional. The user-specified Terraform version constraint. Example:
   * "=1.3.10".
   *
   * @var string
   */
  public $tfVersionConstraint;
  /**
   * Optional. The user-specified Worker Pool resource in which the Cloud Build
   * job will execute. Format
   * projects/{project}/locations/{location}/workerPools/{workerPoolId} If this
   * field is unspecified, the default Cloud Build worker pool will be used. If
   * omitted and deployment resource ref provided has worker_pool defined, that
   * worker pool is used.
   *
   * @var string
   */
  public $workerPool;

  /**
   * Optional. Arbitrary key-value metadata storage e.g. to help client tools
   * identify preview during automation. See
   * https://google.aip.dev/148#annotations for details on format and size
   * limitations.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Optional. User-defined location of Cloud Build logs, artifacts, and in
   * Google Cloud Storage. Format: `gs://{bucket}/{folder}` A default bucket
   * will be bootstrapped if the field is not set or empty Default Bucket
   * Format: `gs://--blueprint-config` Constraints: - The bucket needs to be in
   * the same project as the deployment - The path cannot be within the path of
   * `gcs_source` If omitted and deployment resource ref provided has
   * artifacts_gcs_bucket defined, that artifact bucket is used.
   *
   * @param string $artifactsGcsBucket
   */
  public function setArtifactsGcsBucket($artifactsGcsBucket)
  {
    $this->artifactsGcsBucket = $artifactsGcsBucket;
  }
  /**
   * @return string
   */
  public function getArtifactsGcsBucket()
  {
    return $this->artifactsGcsBucket;
  }
  /**
   * Output only. Cloud Build instance UUID associated with this preview.
   *
   * @param string $build
   */
  public function setBuild($build)
  {
    $this->build = $build;
  }
  /**
   * @return string
   */
  public function getBuild()
  {
    return $this->build;
  }
  /**
   * Output only. Time the preview was created.
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
   * Optional. Optional deployment reference. If specified, the preview will be
   * performed using the provided deployment's current state and use any
   * relevant fields from the deployment unless explicitly specified in the
   * preview create request.
   *
   * @param string $deployment
   */
  public function setDeployment($deployment)
  {
    $this->deployment = $deployment;
  }
  /**
   * @return string
   */
  public function getDeployment()
  {
    return $this->deployment;
  }
  /**
   * Output only. Code describing any errors that may have occurred.
   *
   * Accepted values: ERROR_CODE_UNSPECIFIED, CLOUD_BUILD_PERMISSION_DENIED,
   * BUCKET_CREATION_PERMISSION_DENIED, BUCKET_CREATION_FAILED,
   * DEPLOYMENT_LOCK_ACQUIRE_FAILED, PREVIEW_BUILD_API_FAILED,
   * PREVIEW_BUILD_RUN_FAILED, EXTERNAL_VALUE_SOURCE_IMPORT_FAILED
   *
   * @param self::ERROR_CODE_* $errorCode
   */
  public function setErrorCode($errorCode)
  {
    $this->errorCode = $errorCode;
  }
  /**
   * @return self::ERROR_CODE_*
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  /**
   * Output only. Link to tf-error.ndjson file, which contains the full list of
   * the errors encountered during a Terraform preview. Format:
   * `gs://{bucket}/{object}`.
   *
   * @param string $errorLogs
   */
  public function setErrorLogs($errorLogs)
  {
    $this->errorLogs = $errorLogs;
  }
  /**
   * @return string
   */
  public function getErrorLogs()
  {
    return $this->errorLogs;
  }
  /**
   * Output only. Additional information regarding the current state.
   *
   * @param Status $errorStatus
   */
  public function setErrorStatus(Status $errorStatus)
  {
    $this->errorStatus = $errorStatus;
  }
  /**
   * @return Status
   */
  public function getErrorStatus()
  {
    return $this->errorStatus;
  }
  /**
   * Optional. User-defined labels for the preview.
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
   * Output only. Location of preview logs in `gs://{bucket}/{object}` format.
   *
   * @param string $logs
   */
  public function setLogs($logs)
  {
    $this->logs = $logs;
  }
  /**
   * @return string
   */
  public function getLogs()
  {
    return $this->logs;
  }
  /**
   * Identifier. Resource name of the preview. Resource name can be user
   * provided or server generated ID if unspecified. Format:
   * `projects/{project}/locations/{location}/previews/{preview}`
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
   * Output only. Artifacts from preview.
   *
   * @param PreviewArtifacts $previewArtifacts
   */
  public function setPreviewArtifacts(PreviewArtifacts $previewArtifacts)
  {
    $this->previewArtifacts = $previewArtifacts;
  }
  /**
   * @return PreviewArtifacts
   */
  public function getPreviewArtifacts()
  {
    return $this->previewArtifacts;
  }
  /**
   * Optional. Current mode of preview.
   *
   * Accepted values: PREVIEW_MODE_UNSPECIFIED, DEFAULT, DELETE
   *
   * @param self::PREVIEW_MODE_* $previewMode
   */
  public function setPreviewMode($previewMode)
  {
    $this->previewMode = $previewMode;
  }
  /**
   * @return self::PREVIEW_MODE_*
   */
  public function getPreviewMode()
  {
    return $this->previewMode;
  }
  /**
   * Optional. This field specifies the provider configurations.
   *
   * @param ProviderConfig $providerConfig
   */
  public function setProviderConfig(ProviderConfig $providerConfig)
  {
    $this->providerConfig = $providerConfig;
  }
  /**
   * @return ProviderConfig
   */
  public function getProviderConfig()
  {
    return $this->providerConfig;
  }
  /**
   * Required. User-specified Service Account (SA) credentials to be used when
   * previewing resources. Format:
   * `projects/{projectID}/serviceAccounts/{serviceAccount}`
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
   * Output only. Current state of the preview.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, SUCCEEDED, APPLYING, STALE,
   * DELETING, FAILED, DELETED
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
   * The terraform blueprint to preview.
   *
   * @param TerraformBlueprint $terraformBlueprint
   */
  public function setTerraformBlueprint(TerraformBlueprint $terraformBlueprint)
  {
    $this->terraformBlueprint = $terraformBlueprint;
  }
  /**
   * @return TerraformBlueprint
   */
  public function getTerraformBlueprint()
  {
    return $this->terraformBlueprint;
  }
  /**
   * Output only. Summary of errors encountered during Terraform preview. It has
   * a size limit of 10, i.e. only top 10 errors will be summarized here.
   *
   * @param TerraformError[] $tfErrors
   */
  public function setTfErrors($tfErrors)
  {
    $this->tfErrors = $tfErrors;
  }
  /**
   * @return TerraformError[]
   */
  public function getTfErrors()
  {
    return $this->tfErrors;
  }
  /**
   * Output only. The current Terraform version set on the preview. It is in the
   * format of "Major.Minor.Patch", for example, "1.3.10".
   *
   * @param string $tfVersion
   */
  public function setTfVersion($tfVersion)
  {
    $this->tfVersion = $tfVersion;
  }
  /**
   * @return string
   */
  public function getTfVersion()
  {
    return $this->tfVersion;
  }
  /**
   * Optional. The user-specified Terraform version constraint. Example:
   * "=1.3.10".
   *
   * @param string $tfVersionConstraint
   */
  public function setTfVersionConstraint($tfVersionConstraint)
  {
    $this->tfVersionConstraint = $tfVersionConstraint;
  }
  /**
   * @return string
   */
  public function getTfVersionConstraint()
  {
    return $this->tfVersionConstraint;
  }
  /**
   * Optional. The user-specified Worker Pool resource in which the Cloud Build
   * job will execute. Format
   * projects/{project}/locations/{location}/workerPools/{workerPoolId} If this
   * field is unspecified, the default Cloud Build worker pool will be used. If
   * omitted and deployment resource ref provided has worker_pool defined, that
   * worker pool is used.
   *
   * @param string $workerPool
   */
  public function setWorkerPool($workerPool)
  {
    $this->workerPool = $workerPool;
  }
  /**
   * @return string
   */
  public function getWorkerPool()
  {
    return $this->workerPool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Preview::class, 'Google_Service_Config_Preview');

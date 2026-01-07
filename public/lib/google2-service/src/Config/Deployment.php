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

class Deployment extends \Google\Collection
{
  /**
   * No error code was specified.
   */
  public const ERROR_CODE_ERROR_CODE_UNSPECIFIED = 'ERROR_CODE_UNSPECIFIED';
  /**
   * The revision failed. See Revision for more details.
   */
  public const ERROR_CODE_REVISION_FAILED = 'REVISION_FAILED';
  /**
   * Cloud Build failed due to a permission issue.
   */
  public const ERROR_CODE_CLOUD_BUILD_PERMISSION_DENIED = 'CLOUD_BUILD_PERMISSION_DENIED';
  /**
   * Cloud Build job associated with a deployment deletion could not be started.
   */
  public const ERROR_CODE_DELETE_BUILD_API_FAILED = 'DELETE_BUILD_API_FAILED';
  /**
   * Cloud Build job associated with a deployment deletion was started but
   * failed.
   */
  public const ERROR_CODE_DELETE_BUILD_RUN_FAILED = 'DELETE_BUILD_RUN_FAILED';
  /**
   * Cloud Storage bucket creation failed due to a permission issue.
   */
  public const ERROR_CODE_BUCKET_CREATION_PERMISSION_DENIED = 'BUCKET_CREATION_PERMISSION_DENIED';
  /**
   * Cloud Storage bucket creation failed due to an issue unrelated to
   * permissions.
   */
  public const ERROR_CODE_BUCKET_CREATION_FAILED = 'BUCKET_CREATION_FAILED';
  /**
   * Failed to import values from an external source.
   */
  public const ERROR_CODE_EXTERNAL_VALUE_SOURCE_IMPORT_FAILED = 'EXTERNAL_VALUE_SOURCE_IMPORT_FAILED';
  /**
   * The default value. This value is used if the lock state is omitted.
   */
  public const LOCK_STATE_LOCK_STATE_UNSPECIFIED = 'LOCK_STATE_UNSPECIFIED';
  /**
   * The deployment is locked.
   */
  public const LOCK_STATE_LOCKED = 'LOCKED';
  /**
   * The deployment is unlocked.
   */
  public const LOCK_STATE_UNLOCKED = 'UNLOCKED';
  /**
   * The deployment is being locked.
   */
  public const LOCK_STATE_LOCKING = 'LOCKING';
  /**
   * The deployment is being unlocked.
   */
  public const LOCK_STATE_UNLOCKING = 'UNLOCKING';
  /**
   * The deployment has failed to lock.
   */
  public const LOCK_STATE_LOCK_FAILED = 'LOCK_FAILED';
  /**
   * The deployment has failed to unlock.
   */
  public const LOCK_STATE_UNLOCK_FAILED = 'UNLOCK_FAILED';
  /**
   * The default value. QuotaValidation on terraform configuration files will be
   * disabled in this case.
   */
  public const QUOTA_VALIDATION_QUOTA_VALIDATION_UNSPECIFIED = 'QUOTA_VALIDATION_UNSPECIFIED';
  /**
   * Enable computing quotas for resources in terraform configuration files to
   * get visibility on resources with insufficient quotas.
   */
  public const QUOTA_VALIDATION_ENABLED = 'ENABLED';
  /**
   * Enforce quota checks so deployment fails if there isn't sufficient quotas
   * available to deploy resources in terraform configuration files.
   */
  public const QUOTA_VALIDATION_ENFORCED = 'ENFORCED';
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The deployment is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The deployment is healthy.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The deployment is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The deployment is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The deployment has encountered an unexpected error.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The deployment is no longer being actively reconciled. This may be the
   * result of recovering the project after deletion.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * The deployment has been deleted.
   */
  public const STATE_DELETED = 'DELETED';
  protected $collection_key = 'tfErrors';
  /**
   * Optional. Arbitrary key-value metadata storage e.g. to help client tools
   * identify deployments during automation. See
   * https://google.aip.dev/148#annotations for details on format and size
   * limitations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Optional. User-defined location of Cloud Build logs and artifacts in Google
   * Cloud Storage. Format: `gs://{bucket}/{folder}` A default bucket will be
   * bootstrapped if the field is not set or empty. Default bucket format:
   * `gs://--blueprint-config` Constraints: - The bucket needs to be in the same
   * project as the deployment - The path cannot be within the path of
   * `gcs_source` - The field cannot be updated, including changing its presence
   *
   * @var string
   */
  public $artifactsGcsBucket;
  /**
   * Output only. Time when the deployment was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Cloud Build instance UUID associated with deleting this
   * deployment.
   *
   * @var string
   */
  public $deleteBuild;
  /**
   * Output only. Location of Cloud Build logs in Google Cloud Storage,
   * populated when deleting this deployment. Format: `gs://{bucket}/{object}`.
   *
   * @var string
   */
  public $deleteLogs;
  protected $deleteResultsType = ApplyResults::class;
  protected $deleteResultsDataType = '';
  /**
   * Output only. Error code describing errors that may have occurred.
   *
   * @var string
   */
  public $errorCode;
  /**
   * Output only. Location of Terraform error logs in Google Cloud Storage.
   * Format: `gs://{bucket}/{object}`.
   *
   * @var string
   */
  public $errorLogs;
  /**
   * By default, Infra Manager will return a failure when Terraform encounters a
   * 409 code (resource conflict error) during actuation. If this flag is set to
   * true, Infra Manager will instead attempt to automatically import the
   * resource into the Terraform state (for supported resource types) and
   * continue actuation. Not all resource types are supported, refer to
   * documentation.
   *
   * @var bool
   */
  public $importExistingResources;
  /**
   * Optional. User-defined metadata for the deployment.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Revision name that was most recently applied. Format:
   * `projects/{project}/locations/{location}/deployments/{deployment}/
   * revisions/{revision}`
   *
   * @var string
   */
  public $latestRevision;
  /**
   * Output only. Current lock state of the deployment.
   *
   * @var string
   */
  public $lockState;
  /**
   * Identifier. Resource name of the deployment. Format:
   * `projects/{project}/locations/{location}/deployments/{deployment}`
   *
   * @var string
   */
  public $name;
  protected $providerConfigType = ProviderConfig::class;
  protected $providerConfigDataType = '';
  /**
   * Optional. Input to control quota checks for resources in terraform
   * configuration files. There are limited resources on which quota validation
   * applies.
   *
   * @var string
   */
  public $quotaValidation;
  /**
   * Required. User-specified Service Account (SA) credentials to be used when
   * actuating resources. Format:
   * `projects/{projectID}/serviceAccounts/{serviceAccount}`
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. Current state of the deployment.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Additional information regarding the current state.
   *
   * @var string
   */
  public $stateDetail;
  protected $terraformBlueprintType = TerraformBlueprint::class;
  protected $terraformBlueprintDataType = '';
  protected $tfErrorsType = TerraformError::class;
  protected $tfErrorsDataType = 'array';
  /**
   * Output only. The current Terraform version set on the deployment. It is in
   * the format of "Major.Minor.Patch", for example, "1.3.10".
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
   * Output only. Time when the deployment was last modified.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Optional. The user-specified Cloud Build worker pool resource in which the
   * Cloud Build job will execute. Format:
   * `projects/{project}/locations/{location}/workerPools/{workerPoolId}`. If
   * this field is unspecified, the default Cloud Build worker pool will be
   * used.
   *
   * @var string
   */
  public $workerPool;

  /**
   * Optional. Arbitrary key-value metadata storage e.g. to help client tools
   * identify deployments during automation. See
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
   * Optional. User-defined location of Cloud Build logs and artifacts in Google
   * Cloud Storage. Format: `gs://{bucket}/{folder}` A default bucket will be
   * bootstrapped if the field is not set or empty. Default bucket format:
   * `gs://--blueprint-config` Constraints: - The bucket needs to be in the same
   * project as the deployment - The path cannot be within the path of
   * `gcs_source` - The field cannot be updated, including changing its presence
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
   * Output only. Time when the deployment was created.
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
   * Output only. Cloud Build instance UUID associated with deleting this
   * deployment.
   *
   * @param string $deleteBuild
   */
  public function setDeleteBuild($deleteBuild)
  {
    $this->deleteBuild = $deleteBuild;
  }
  /**
   * @return string
   */
  public function getDeleteBuild()
  {
    return $this->deleteBuild;
  }
  /**
   * Output only. Location of Cloud Build logs in Google Cloud Storage,
   * populated when deleting this deployment. Format: `gs://{bucket}/{object}`.
   *
   * @param string $deleteLogs
   */
  public function setDeleteLogs($deleteLogs)
  {
    $this->deleteLogs = $deleteLogs;
  }
  /**
   * @return string
   */
  public function getDeleteLogs()
  {
    return $this->deleteLogs;
  }
  /**
   * Output only. Location of artifacts from a DeleteDeployment operation.
   *
   * @param ApplyResults $deleteResults
   */
  public function setDeleteResults(ApplyResults $deleteResults)
  {
    $this->deleteResults = $deleteResults;
  }
  /**
   * @return ApplyResults
   */
  public function getDeleteResults()
  {
    return $this->deleteResults;
  }
  /**
   * Output only. Error code describing errors that may have occurred.
   *
   * Accepted values: ERROR_CODE_UNSPECIFIED, REVISION_FAILED,
   * CLOUD_BUILD_PERMISSION_DENIED, DELETE_BUILD_API_FAILED,
   * DELETE_BUILD_RUN_FAILED, BUCKET_CREATION_PERMISSION_DENIED,
   * BUCKET_CREATION_FAILED, EXTERNAL_VALUE_SOURCE_IMPORT_FAILED
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
   * Output only. Location of Terraform error logs in Google Cloud Storage.
   * Format: `gs://{bucket}/{object}`.
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
   * By default, Infra Manager will return a failure when Terraform encounters a
   * 409 code (resource conflict error) during actuation. If this flag is set to
   * true, Infra Manager will instead attempt to automatically import the
   * resource into the Terraform state (for supported resource types) and
   * continue actuation. Not all resource types are supported, refer to
   * documentation.
   *
   * @param bool $importExistingResources
   */
  public function setImportExistingResources($importExistingResources)
  {
    $this->importExistingResources = $importExistingResources;
  }
  /**
   * @return bool
   */
  public function getImportExistingResources()
  {
    return $this->importExistingResources;
  }
  /**
   * Optional. User-defined metadata for the deployment.
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
   * Output only. Revision name that was most recently applied. Format:
   * `projects/{project}/locations/{location}/deployments/{deployment}/
   * revisions/{revision}`
   *
   * @param string $latestRevision
   */
  public function setLatestRevision($latestRevision)
  {
    $this->latestRevision = $latestRevision;
  }
  /**
   * @return string
   */
  public function getLatestRevision()
  {
    return $this->latestRevision;
  }
  /**
   * Output only. Current lock state of the deployment.
   *
   * Accepted values: LOCK_STATE_UNSPECIFIED, LOCKED, UNLOCKED, LOCKING,
   * UNLOCKING, LOCK_FAILED, UNLOCK_FAILED
   *
   * @param self::LOCK_STATE_* $lockState
   */
  public function setLockState($lockState)
  {
    $this->lockState = $lockState;
  }
  /**
   * @return self::LOCK_STATE_*
   */
  public function getLockState()
  {
    return $this->lockState;
  }
  /**
   * Identifier. Resource name of the deployment. Format:
   * `projects/{project}/locations/{location}/deployments/{deployment}`
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
   * Optional. Input to control quota checks for resources in terraform
   * configuration files. There are limited resources on which quota validation
   * applies.
   *
   * Accepted values: QUOTA_VALIDATION_UNSPECIFIED, ENABLED, ENFORCED
   *
   * @param self::QUOTA_VALIDATION_* $quotaValidation
   */
  public function setQuotaValidation($quotaValidation)
  {
    $this->quotaValidation = $quotaValidation;
  }
  /**
   * @return self::QUOTA_VALIDATION_*
   */
  public function getQuotaValidation()
  {
    return $this->quotaValidation;
  }
  /**
   * Required. User-specified Service Account (SA) credentials to be used when
   * actuating resources. Format:
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
   * Output only. Current state of the deployment.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, UPDATING, DELETING,
   * FAILED, SUSPENDED, DELETED
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
   * Output only. Additional information regarding the current state.
   *
   * @param string $stateDetail
   */
  public function setStateDetail($stateDetail)
  {
    $this->stateDetail = $stateDetail;
  }
  /**
   * @return string
   */
  public function getStateDetail()
  {
    return $this->stateDetail;
  }
  /**
   * A blueprint described using Terraform's HashiCorp Configuration Language as
   * a root module.
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
   * Output only. Errors encountered when deleting this deployment. Errors are
   * truncated to 10 entries, see `delete_results` and `error_logs` for full
   * details.
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
   * Output only. The current Terraform version set on the deployment. It is in
   * the format of "Major.Minor.Patch", for example, "1.3.10".
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
   * Output only. Time when the deployment was last modified.
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
   * Optional. The user-specified Cloud Build worker pool resource in which the
   * Cloud Build job will execute. Format:
   * `projects/{project}/locations/{location}/workerPools/{workerPoolId}`. If
   * this field is unspecified, the default Cloud Build worker pool will be
   * used.
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
class_alias(Deployment::class, 'Google_Service_Config_Deployment');

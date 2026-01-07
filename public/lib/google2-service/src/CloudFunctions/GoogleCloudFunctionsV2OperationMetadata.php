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

namespace Google\Service\CloudFunctions;

class GoogleCloudFunctionsV2OperationMetadata extends \Google\Collection
{
  /**
   * Unspecified
   */
  public const OPERATION_TYPE_OPERATIONTYPE_UNSPECIFIED = 'OPERATIONTYPE_UNSPECIFIED';
  /**
   * CreateFunction
   */
  public const OPERATION_TYPE_CREATE_FUNCTION = 'CREATE_FUNCTION';
  /**
   * UpdateFunction
   */
  public const OPERATION_TYPE_UPDATE_FUNCTION = 'UPDATE_FUNCTION';
  /**
   * DeleteFunction
   */
  public const OPERATION_TYPE_DELETE_FUNCTION = 'DELETE_FUNCTION';
  /**
   * RedirectFunctionUpgradeTraffic
   */
  public const OPERATION_TYPE_REDIRECT_FUNCTION_UPGRADE_TRAFFIC = 'REDIRECT_FUNCTION_UPGRADE_TRAFFIC';
  /**
   * RollbackFunctionUpgradeTraffic
   */
  public const OPERATION_TYPE_ROLLBACK_FUNCTION_UPGRADE_TRAFFIC = 'ROLLBACK_FUNCTION_UPGRADE_TRAFFIC';
  /**
   * SetupFunctionUpgradeConfig
   */
  public const OPERATION_TYPE_SETUP_FUNCTION_UPGRADE_CONFIG = 'SETUP_FUNCTION_UPGRADE_CONFIG';
  /**
   * AbortFunctionUpgrade
   */
  public const OPERATION_TYPE_ABORT_FUNCTION_UPGRADE = 'ABORT_FUNCTION_UPGRADE';
  /**
   * CommitFunctionUpgrade
   */
  public const OPERATION_TYPE_COMMIT_FUNCTION_UPGRADE = 'COMMIT_FUNCTION_UPGRADE';
  /**
   * DetachFunction
   */
  public const OPERATION_TYPE_DETACH_FUNCTION = 'DETACH_FUNCTION';
  /**
   * CommitFunctionUpgradeAsGen2
   */
  public const OPERATION_TYPE_COMMIT_FUNCTION_UPGRADE_AS_GEN2 = 'COMMIT_FUNCTION_UPGRADE_AS_GEN2';
  protected $collection_key = 'stages';
  /**
   * API version used to start the operation.
   *
   * @var string
   */
  public $apiVersion;
  /**
   * The build name of the function for create and update operations.
   *
   * @var string
   */
  public $buildName;
  /**
   * Identifies whether the user has requested cancellation of the operation.
   * Operations that have successfully been cancelled have
   * google.longrunning.Operation.error value with a google.rpc.Status.code of
   * 1, corresponding to `Code.CANCELLED`.
   *
   * @var bool
   */
  public $cancelRequested;
  /**
   * The time the operation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Whether a custom IAM role binding was detected during the
   * upgrade.
   *
   * @var bool
   */
  public $customIamRoleDetected;
  /**
   * The time the operation finished running.
   *
   * @var string
   */
  public $endTime;
  /**
   * The operation type.
   *
   * @var string
   */
  public $operationType;
  /**
   * The original request that started the operation.
   *
   * @var array[]
   */
  public $requestResource;
  /**
   * An identifier for Firebase function sources. Disclaimer: This field is only
   * supported for Firebase function deployments.
   *
   * @var string
   */
  public $sourceToken;
  protected $stagesType = GoogleCloudFunctionsV2Stage::class;
  protected $stagesDataType = 'array';
  /**
   * Human-readable status of the operation, if any.
   *
   * @var string
   */
  public $statusDetail;
  /**
   * Server-defined resource path for the target of the operation.
   *
   * @var string
   */
  public $target;
  /**
   * Name of the verb executed by the operation.
   *
   * @var string
   */
  public $verb;

  /**
   * API version used to start the operation.
   *
   * @param string $apiVersion
   */
  public function setApiVersion($apiVersion)
  {
    $this->apiVersion = $apiVersion;
  }
  /**
   * @return string
   */
  public function getApiVersion()
  {
    return $this->apiVersion;
  }
  /**
   * The build name of the function for create and update operations.
   *
   * @param string $buildName
   */
  public function setBuildName($buildName)
  {
    $this->buildName = $buildName;
  }
  /**
   * @return string
   */
  public function getBuildName()
  {
    return $this->buildName;
  }
  /**
   * Identifies whether the user has requested cancellation of the operation.
   * Operations that have successfully been cancelled have
   * google.longrunning.Operation.error value with a google.rpc.Status.code of
   * 1, corresponding to `Code.CANCELLED`.
   *
   * @param bool $cancelRequested
   */
  public function setCancelRequested($cancelRequested)
  {
    $this->cancelRequested = $cancelRequested;
  }
  /**
   * @return bool
   */
  public function getCancelRequested()
  {
    return $this->cancelRequested;
  }
  /**
   * The time the operation was created.
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
   * Output only. Whether a custom IAM role binding was detected during the
   * upgrade.
   *
   * @param bool $customIamRoleDetected
   */
  public function setCustomIamRoleDetected($customIamRoleDetected)
  {
    $this->customIamRoleDetected = $customIamRoleDetected;
  }
  /**
   * @return bool
   */
  public function getCustomIamRoleDetected()
  {
    return $this->customIamRoleDetected;
  }
  /**
   * The time the operation finished running.
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
   * The operation type.
   *
   * Accepted values: OPERATIONTYPE_UNSPECIFIED, CREATE_FUNCTION,
   * UPDATE_FUNCTION, DELETE_FUNCTION, REDIRECT_FUNCTION_UPGRADE_TRAFFIC,
   * ROLLBACK_FUNCTION_UPGRADE_TRAFFIC, SETUP_FUNCTION_UPGRADE_CONFIG,
   * ABORT_FUNCTION_UPGRADE, COMMIT_FUNCTION_UPGRADE, DETACH_FUNCTION,
   * COMMIT_FUNCTION_UPGRADE_AS_GEN2
   *
   * @param self::OPERATION_TYPE_* $operationType
   */
  public function setOperationType($operationType)
  {
    $this->operationType = $operationType;
  }
  /**
   * @return self::OPERATION_TYPE_*
   */
  public function getOperationType()
  {
    return $this->operationType;
  }
  /**
   * The original request that started the operation.
   *
   * @param array[] $requestResource
   */
  public function setRequestResource($requestResource)
  {
    $this->requestResource = $requestResource;
  }
  /**
   * @return array[]
   */
  public function getRequestResource()
  {
    return $this->requestResource;
  }
  /**
   * An identifier for Firebase function sources. Disclaimer: This field is only
   * supported for Firebase function deployments.
   *
   * @param string $sourceToken
   */
  public function setSourceToken($sourceToken)
  {
    $this->sourceToken = $sourceToken;
  }
  /**
   * @return string
   */
  public function getSourceToken()
  {
    return $this->sourceToken;
  }
  /**
   * Mechanism for reporting in-progress stages
   *
   * @param GoogleCloudFunctionsV2Stage[] $stages
   */
  public function setStages($stages)
  {
    $this->stages = $stages;
  }
  /**
   * @return GoogleCloudFunctionsV2Stage[]
   */
  public function getStages()
  {
    return $this->stages;
  }
  /**
   * Human-readable status of the operation, if any.
   *
   * @param string $statusDetail
   */
  public function setStatusDetail($statusDetail)
  {
    $this->statusDetail = $statusDetail;
  }
  /**
   * @return string
   */
  public function getStatusDetail()
  {
    return $this->statusDetail;
  }
  /**
   * Server-defined resource path for the target of the operation.
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
  /**
   * Name of the verb executed by the operation.
   *
   * @param string $verb
   */
  public function setVerb($verb)
  {
    $this->verb = $verb;
  }
  /**
   * @return string
   */
  public function getVerb()
  {
    return $this->verb;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudFunctionsV2OperationMetadata::class, 'Google_Service_CloudFunctions_GoogleCloudFunctionsV2OperationMetadata');

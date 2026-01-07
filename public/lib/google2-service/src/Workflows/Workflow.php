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

namespace Google\Service\Workflows;

class Workflow extends \Google\Collection
{
  /**
   * No call logging level specified.
   */
  public const CALL_LOG_LEVEL_CALL_LOG_LEVEL_UNSPECIFIED = 'CALL_LOG_LEVEL_UNSPECIFIED';
  /**
   * Log all call steps within workflows, all call returns, and all exceptions
   * raised.
   */
  public const CALL_LOG_LEVEL_LOG_ALL_CALLS = 'LOG_ALL_CALLS';
  /**
   * Log only exceptions that are raised from call steps within workflows.
   */
  public const CALL_LOG_LEVEL_LOG_ERRORS_ONLY = 'LOG_ERRORS_ONLY';
  /**
   * Explicitly log nothing.
   */
  public const CALL_LOG_LEVEL_LOG_NONE = 'LOG_NONE';
  /**
   * The default/unset value.
   */
  public const EXECUTION_HISTORY_LEVEL_EXECUTION_HISTORY_LEVEL_UNSPECIFIED = 'EXECUTION_HISTORY_LEVEL_UNSPECIFIED';
  /**
   * Enable execution history basic feature.
   */
  public const EXECUTION_HISTORY_LEVEL_EXECUTION_HISTORY_BASIC = 'EXECUTION_HISTORY_BASIC';
  /**
   * Enable execution history detailed feature.
   */
  public const EXECUTION_HISTORY_LEVEL_EXECUTION_HISTORY_DETAILED = 'EXECUTION_HISTORY_DETAILED';
  /**
   * Invalid state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The workflow has been deployed successfully and is serving.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Workflow data is unavailable. See the `state_error` field.
   */
  public const STATE_UNAVAILABLE = 'UNAVAILABLE';
  protected $collection_key = 'allKmsKeysVersions';
  /**
   * Output only. A list of all KMS crypto keys used to encrypt or decrypt the
   * data associated with the workflow.
   *
   * @var string[]
   */
  public $allKmsKeys;
  /**
   * Output only. A list of all KMS crypto key versions used to encrypt or
   * decrypt the data associated with the workflow.
   *
   * @var string[]
   */
  public $allKmsKeysVersions;
  /**
   * Optional. Describes the level of platform logging to apply to calls and
   * call responses during executions of this workflow. If both the workflow and
   * the execution specify a logging level, the execution level takes
   * precedence.
   *
   * @var string
   */
  public $callLogLevel;
  /**
   * Output only. The timestamp for when the workflow was created. This is a
   * workflow-wide field and is not tied to a specific revision.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The resource name of a KMS crypto key used to encrypt or decrypt
   * the data associated with the workflow. Format: projects/{project}/locations
   * /{location}/keyRings/{keyRing}/cryptoKeys/{cryptoKey} Using `-` as a
   * wildcard for the `{project}` or not providing one at all will infer the
   * project from the account. If not provided, data associated with the
   * workflow will not be CMEK-encrypted.
   *
   * @var string
   */
  public $cryptoKeyName;
  /**
   * Output only. The resource name of a KMS crypto key version used to encrypt
   * or decrypt the data associated with the workflow. Format: projects/{project
   * }/locations/{location}/keyRings/{keyRing}/cryptoKeys/{cryptoKey}/cryptoKeyV
   * ersions/{cryptoKeyVersion}
   *
   * @var string
   */
  public $cryptoKeyVersion;
  /**
   * Description of the workflow provided by the user. Must be at most 1000
   * Unicode characters long. This is a workflow-wide field and is not tied to a
   * specific revision.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Describes the execution history level to apply to this workflow.
   *
   * @var string
   */
  public $executionHistoryLevel;
  /**
   * Labels associated with this workflow. Labels can contain at most 64
   * entries. Keys and values can be no longer than 63 characters and can only
   * contain lowercase letters, numeric characters, underscores, and dashes.
   * Label keys must start with a letter. International characters are allowed.
   * This is a workflow-wide field and is not tied to a specific revision.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The resource name of the workflow. Format:
   * projects/{project}/locations/{location}/workflows/{workflow}. This is a
   * workflow-wide field and is not tied to a specific revision.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The timestamp for the latest revision of the workflow's
   * creation.
   *
   * @var string
   */
  public $revisionCreateTime;
  /**
   * Output only. The revision of the workflow. A new revision of a workflow is
   * created as a result of updating the following properties of a workflow: -
   * Service account - Workflow code to be executed The format is "000001-a4d",
   * where the first six characters define the zero-padded revision ordinal
   * number. They are followed by a hyphen and three hexadecimal random
   * characters.
   *
   * @var string
   */
  public $revisionId;
  /**
   * The service account associated with the latest workflow version. This
   * service account represents the identity of the workflow and determines what
   * permissions the workflow has. Format:
   * projects/{project}/serviceAccounts/{account} or {account} Using `-` as a
   * wildcard for the `{project}` or not providing one at all will infer the
   * project from the account. The `{account}` value can be the `email` address
   * or the `unique_id` of the service account. If not provided, workflow will
   * use the project's default service account. Modifying this field for an
   * existing workflow results in a new workflow revision.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Workflow code to be executed. The size limit is 128KB.
   *
   * @var string
   */
  public $sourceContents;
  /**
   * Output only. State of the workflow deployment.
   *
   * @var string
   */
  public $state;
  protected $stateErrorType = StateError::class;
  protected $stateErrorDataType = '';
  /**
   * Optional. Input only. Immutable. Tags associated with this workflow.
   *
   * @var string[]
   */
  public $tags;
  /**
   * Output only. The timestamp for when the workflow was last updated. This is
   * a workflow-wide field and is not tied to a specific revision.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Optional. User-defined environment variables associated with this workflow
   * revision. This map has a maximum length of 20. Each string can take up to
   * 4KiB. Keys cannot be empty strings and cannot start with "GOOGLE" or
   * "WORKFLOWS".
   *
   * @var string[]
   */
  public $userEnvVars;

  /**
   * Output only. A list of all KMS crypto keys used to encrypt or decrypt the
   * data associated with the workflow.
   *
   * @param string[] $allKmsKeys
   */
  public function setAllKmsKeys($allKmsKeys)
  {
    $this->allKmsKeys = $allKmsKeys;
  }
  /**
   * @return string[]
   */
  public function getAllKmsKeys()
  {
    return $this->allKmsKeys;
  }
  /**
   * Output only. A list of all KMS crypto key versions used to encrypt or
   * decrypt the data associated with the workflow.
   *
   * @param string[] $allKmsKeysVersions
   */
  public function setAllKmsKeysVersions($allKmsKeysVersions)
  {
    $this->allKmsKeysVersions = $allKmsKeysVersions;
  }
  /**
   * @return string[]
   */
  public function getAllKmsKeysVersions()
  {
    return $this->allKmsKeysVersions;
  }
  /**
   * Optional. Describes the level of platform logging to apply to calls and
   * call responses during executions of this workflow. If both the workflow and
   * the execution specify a logging level, the execution level takes
   * precedence.
   *
   * Accepted values: CALL_LOG_LEVEL_UNSPECIFIED, LOG_ALL_CALLS,
   * LOG_ERRORS_ONLY, LOG_NONE
   *
   * @param self::CALL_LOG_LEVEL_* $callLogLevel
   */
  public function setCallLogLevel($callLogLevel)
  {
    $this->callLogLevel = $callLogLevel;
  }
  /**
   * @return self::CALL_LOG_LEVEL_*
   */
  public function getCallLogLevel()
  {
    return $this->callLogLevel;
  }
  /**
   * Output only. The timestamp for when the workflow was created. This is a
   * workflow-wide field and is not tied to a specific revision.
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
   * Optional. The resource name of a KMS crypto key used to encrypt or decrypt
   * the data associated with the workflow. Format: projects/{project}/locations
   * /{location}/keyRings/{keyRing}/cryptoKeys/{cryptoKey} Using `-` as a
   * wildcard for the `{project}` or not providing one at all will infer the
   * project from the account. If not provided, data associated with the
   * workflow will not be CMEK-encrypted.
   *
   * @param string $cryptoKeyName
   */
  public function setCryptoKeyName($cryptoKeyName)
  {
    $this->cryptoKeyName = $cryptoKeyName;
  }
  /**
   * @return string
   */
  public function getCryptoKeyName()
  {
    return $this->cryptoKeyName;
  }
  /**
   * Output only. The resource name of a KMS crypto key version used to encrypt
   * or decrypt the data associated with the workflow. Format: projects/{project
   * }/locations/{location}/keyRings/{keyRing}/cryptoKeys/{cryptoKey}/cryptoKeyV
   * ersions/{cryptoKeyVersion}
   *
   * @param string $cryptoKeyVersion
   */
  public function setCryptoKeyVersion($cryptoKeyVersion)
  {
    $this->cryptoKeyVersion = $cryptoKeyVersion;
  }
  /**
   * @return string
   */
  public function getCryptoKeyVersion()
  {
    return $this->cryptoKeyVersion;
  }
  /**
   * Description of the workflow provided by the user. Must be at most 1000
   * Unicode characters long. This is a workflow-wide field and is not tied to a
   * specific revision.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Describes the execution history level to apply to this workflow.
   *
   * Accepted values: EXECUTION_HISTORY_LEVEL_UNSPECIFIED,
   * EXECUTION_HISTORY_BASIC, EXECUTION_HISTORY_DETAILED
   *
   * @param self::EXECUTION_HISTORY_LEVEL_* $executionHistoryLevel
   */
  public function setExecutionHistoryLevel($executionHistoryLevel)
  {
    $this->executionHistoryLevel = $executionHistoryLevel;
  }
  /**
   * @return self::EXECUTION_HISTORY_LEVEL_*
   */
  public function getExecutionHistoryLevel()
  {
    return $this->executionHistoryLevel;
  }
  /**
   * Labels associated with this workflow. Labels can contain at most 64
   * entries. Keys and values can be no longer than 63 characters and can only
   * contain lowercase letters, numeric characters, underscores, and dashes.
   * Label keys must start with a letter. International characters are allowed.
   * This is a workflow-wide field and is not tied to a specific revision.
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
   * The resource name of the workflow. Format:
   * projects/{project}/locations/{location}/workflows/{workflow}. This is a
   * workflow-wide field and is not tied to a specific revision.
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
   * Output only. The timestamp for the latest revision of the workflow's
   * creation.
   *
   * @param string $revisionCreateTime
   */
  public function setRevisionCreateTime($revisionCreateTime)
  {
    $this->revisionCreateTime = $revisionCreateTime;
  }
  /**
   * @return string
   */
  public function getRevisionCreateTime()
  {
    return $this->revisionCreateTime;
  }
  /**
   * Output only. The revision of the workflow. A new revision of a workflow is
   * created as a result of updating the following properties of a workflow: -
   * Service account - Workflow code to be executed The format is "000001-a4d",
   * where the first six characters define the zero-padded revision ordinal
   * number. They are followed by a hyphen and three hexadecimal random
   * characters.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * The service account associated with the latest workflow version. This
   * service account represents the identity of the workflow and determines what
   * permissions the workflow has. Format:
   * projects/{project}/serviceAccounts/{account} or {account} Using `-` as a
   * wildcard for the `{project}` or not providing one at all will infer the
   * project from the account. The `{account}` value can be the `email` address
   * or the `unique_id` of the service account. If not provided, workflow will
   * use the project's default service account. Modifying this field for an
   * existing workflow results in a new workflow revision.
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
   * Workflow code to be executed. The size limit is 128KB.
   *
   * @param string $sourceContents
   */
  public function setSourceContents($sourceContents)
  {
    $this->sourceContents = $sourceContents;
  }
  /**
   * @return string
   */
  public function getSourceContents()
  {
    return $this->sourceContents;
  }
  /**
   * Output only. State of the workflow deployment.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, UNAVAILABLE
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
   * Output only. Error regarding the state of the workflow. For example, this
   * field will have error details if the execution data is unavailable due to
   * revoked KMS key permissions.
   *
   * @param StateError $stateError
   */
  public function setStateError(StateError $stateError)
  {
    $this->stateError = $stateError;
  }
  /**
   * @return StateError
   */
  public function getStateError()
  {
    return $this->stateError;
  }
  /**
   * Optional. Input only. Immutable. Tags associated with this workflow.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Output only. The timestamp for when the workflow was last updated. This is
   * a workflow-wide field and is not tied to a specific revision.
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
   * Optional. User-defined environment variables associated with this workflow
   * revision. This map has a maximum length of 20. Each string can take up to
   * 4KiB. Keys cannot be empty strings and cannot start with "GOOGLE" or
   * "WORKFLOWS".
   *
   * @param string[] $userEnvVars
   */
  public function setUserEnvVars($userEnvVars)
  {
    $this->userEnvVars = $userEnvVars;
  }
  /**
   * @return string[]
   */
  public function getUserEnvVars()
  {
    return $this->userEnvVars;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Workflow::class, 'Google_Service_Workflows_Workflow');

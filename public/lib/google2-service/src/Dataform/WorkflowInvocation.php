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

namespace Google\Service\Dataform;

class WorkflowInvocation extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The workflow invocation is currently running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The workflow invocation succeeded. A terminal state.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The workflow invocation was cancelled. A terminal state.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The workflow invocation failed. A terminal state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The workflow invocation is being cancelled, but some actions are still
   * running.
   */
  public const STATE_CANCELING = 'CANCELING';
  /**
   * Immutable. The name of the compilation result to use for this invocation.
   * Must be in the format `projects/locations/repositories/compilationResults`.
   *
   * @var string
   */
  public $compilationResult;
  protected $dataEncryptionStateType = DataEncryptionState::class;
  protected $dataEncryptionStateDataType = '';
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @var string
   */
  public $internalMetadata;
  protected $invocationConfigType = InvocationConfig::class;
  protected $invocationConfigDataType = '';
  protected $invocationTimingType = Interval::class;
  protected $invocationTimingDataType = '';
  /**
   * Output only. The workflow invocation's name.
   *
   * @var string
   */
  public $name;
  protected $privateResourceMetadataType = PrivateResourceMetadata::class;
  protected $privateResourceMetadataDataType = '';
  /**
   * Output only. The resolved compilation result that was used to create this
   * invocation. Will be in the format
   * `projects/locations/repositories/compilationResults`.
   *
   * @var string
   */
  public $resolvedCompilationResult;
  /**
   * Output only. This workflow invocation's current state.
   *
   * @var string
   */
  public $state;
  /**
   * Immutable. The name of the workflow config to invoke. Must be in the format
   * `projects/locations/repositories/workflowConfigs`.
   *
   * @var string
   */
  public $workflowConfig;

  /**
   * Immutable. The name of the compilation result to use for this invocation.
   * Must be in the format `projects/locations/repositories/compilationResults`.
   *
   * @param string $compilationResult
   */
  public function setCompilationResult($compilationResult)
  {
    $this->compilationResult = $compilationResult;
  }
  /**
   * @return string
   */
  public function getCompilationResult()
  {
    return $this->compilationResult;
  }
  /**
   * Output only. Only set if the repository has a KMS Key.
   *
   * @param DataEncryptionState $dataEncryptionState
   */
  public function setDataEncryptionState(DataEncryptionState $dataEncryptionState)
  {
    $this->dataEncryptionState = $dataEncryptionState;
  }
  /**
   * @return DataEncryptionState
   */
  public function getDataEncryptionState()
  {
    return $this->dataEncryptionState;
  }
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @param string $internalMetadata
   */
  public function setInternalMetadata($internalMetadata)
  {
    $this->internalMetadata = $internalMetadata;
  }
  /**
   * @return string
   */
  public function getInternalMetadata()
  {
    return $this->internalMetadata;
  }
  /**
   * Immutable. If left unset, a default InvocationConfig will be used.
   *
   * @param InvocationConfig $invocationConfig
   */
  public function setInvocationConfig(InvocationConfig $invocationConfig)
  {
    $this->invocationConfig = $invocationConfig;
  }
  /**
   * @return InvocationConfig
   */
  public function getInvocationConfig()
  {
    return $this->invocationConfig;
  }
  /**
   * Output only. This workflow invocation's timing details.
   *
   * @param Interval $invocationTiming
   */
  public function setInvocationTiming(Interval $invocationTiming)
  {
    $this->invocationTiming = $invocationTiming;
  }
  /**
   * @return Interval
   */
  public function getInvocationTiming()
  {
    return $this->invocationTiming;
  }
  /**
   * Output only. The workflow invocation's name.
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
   * Output only. Metadata indicating whether this resource is user-scoped.
   * `WorkflowInvocation` resource is `user_scoped` only if it is sourced from a
   * compilation result and the compilation result is user-scoped.
   *
   * @param PrivateResourceMetadata $privateResourceMetadata
   */
  public function setPrivateResourceMetadata(PrivateResourceMetadata $privateResourceMetadata)
  {
    $this->privateResourceMetadata = $privateResourceMetadata;
  }
  /**
   * @return PrivateResourceMetadata
   */
  public function getPrivateResourceMetadata()
  {
    return $this->privateResourceMetadata;
  }
  /**
   * Output only. The resolved compilation result that was used to create this
   * invocation. Will be in the format
   * `projects/locations/repositories/compilationResults`.
   *
   * @param string $resolvedCompilationResult
   */
  public function setResolvedCompilationResult($resolvedCompilationResult)
  {
    $this->resolvedCompilationResult = $resolvedCompilationResult;
  }
  /**
   * @return string
   */
  public function getResolvedCompilationResult()
  {
    return $this->resolvedCompilationResult;
  }
  /**
   * Output only. This workflow invocation's current state.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, SUCCEEDED, CANCELLED, FAILED,
   * CANCELING
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
   * Immutable. The name of the workflow config to invoke. Must be in the format
   * `projects/locations/repositories/workflowConfigs`.
   *
   * @param string $workflowConfig
   */
  public function setWorkflowConfig($workflowConfig)
  {
    $this->workflowConfig = $workflowConfig;
  }
  /**
   * @return string
   */
  public function getWorkflowConfig()
  {
    return $this->workflowConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkflowInvocation::class, 'Google_Service_Dataform_WorkflowInvocation');

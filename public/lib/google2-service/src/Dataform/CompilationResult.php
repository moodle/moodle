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

class CompilationResult extends \Google\Collection
{
  protected $collection_key = 'compilationErrors';
  protected $codeCompilationConfigType = CodeCompilationConfig::class;
  protected $codeCompilationConfigDataType = '';
  protected $compilationErrorsType = CompilationError::class;
  protected $compilationErrorsDataType = 'array';
  /**
   * Output only. The timestamp of when the compilation result was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataEncryptionStateType = DataEncryptionState::class;
  protected $dataEncryptionStateDataType = '';
  /**
   * Output only. The version of `@dataform/core` that was used for compilation.
   *
   * @var string
   */
  public $dataformCoreVersion;
  /**
   * Immutable. Git commit/tag/branch name at which the repository should be
   * compiled. Must exist in the remote repository. Examples: - a commit SHA:
   * `12ade345` - a tag: `tag1` - a branch name: `branch1`
   *
   * @var string
   */
  public $gitCommitish;
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @var string
   */
  public $internalMetadata;
  /**
   * Output only. The compilation result's name.
   *
   * @var string
   */
  public $name;
  protected $privateResourceMetadataType = PrivateResourceMetadata::class;
  protected $privateResourceMetadataDataType = '';
  /**
   * Immutable. The name of the release config to compile. Must be in the format
   * `projects/locations/repositories/releaseConfigs`.
   *
   * @var string
   */
  public $releaseConfig;
  /**
   * Output only. The fully resolved Git commit SHA of the code that was
   * compiled. Not set for compilation results whose source is a workspace.
   *
   * @var string
   */
  public $resolvedGitCommitSha;
  /**
   * Immutable. The name of the workspace to compile. Must be in the format
   * `projects/locations/repositories/workspaces`.
   *
   * @var string
   */
  public $workspace;

  /**
   * Immutable. If set, fields of `code_compilation_config` override the default
   * compilation settings that are specified in dataform.json.
   *
   * @param CodeCompilationConfig $codeCompilationConfig
   */
  public function setCodeCompilationConfig(CodeCompilationConfig $codeCompilationConfig)
  {
    $this->codeCompilationConfig = $codeCompilationConfig;
  }
  /**
   * @return CodeCompilationConfig
   */
  public function getCodeCompilationConfig()
  {
    return $this->codeCompilationConfig;
  }
  /**
   * Output only. Errors encountered during project compilation.
   *
   * @param CompilationError[] $compilationErrors
   */
  public function setCompilationErrors($compilationErrors)
  {
    $this->compilationErrors = $compilationErrors;
  }
  /**
   * @return CompilationError[]
   */
  public function getCompilationErrors()
  {
    return $this->compilationErrors;
  }
  /**
   * Output only. The timestamp of when the compilation result was created.
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
   * Output only. The version of `@dataform/core` that was used for compilation.
   *
   * @param string $dataformCoreVersion
   */
  public function setDataformCoreVersion($dataformCoreVersion)
  {
    $this->dataformCoreVersion = $dataformCoreVersion;
  }
  /**
   * @return string
   */
  public function getDataformCoreVersion()
  {
    return $this->dataformCoreVersion;
  }
  /**
   * Immutable. Git commit/tag/branch name at which the repository should be
   * compiled. Must exist in the remote repository. Examples: - a commit SHA:
   * `12ade345` - a tag: `tag1` - a branch name: `branch1`
   *
   * @param string $gitCommitish
   */
  public function setGitCommitish($gitCommitish)
  {
    $this->gitCommitish = $gitCommitish;
  }
  /**
   * @return string
   */
  public function getGitCommitish()
  {
    return $this->gitCommitish;
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
   * Output only. The compilation result's name.
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
   * `CompilationResult` resource is `user_scoped` only if it is sourced from a
   * workspace.
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
   * Immutable. The name of the release config to compile. Must be in the format
   * `projects/locations/repositories/releaseConfigs`.
   *
   * @param string $releaseConfig
   */
  public function setReleaseConfig($releaseConfig)
  {
    $this->releaseConfig = $releaseConfig;
  }
  /**
   * @return string
   */
  public function getReleaseConfig()
  {
    return $this->releaseConfig;
  }
  /**
   * Output only. The fully resolved Git commit SHA of the code that was
   * compiled. Not set for compilation results whose source is a workspace.
   *
   * @param string $resolvedGitCommitSha
   */
  public function setResolvedGitCommitSha($resolvedGitCommitSha)
  {
    $this->resolvedGitCommitSha = $resolvedGitCommitSha;
  }
  /**
   * @return string
   */
  public function getResolvedGitCommitSha()
  {
    return $this->resolvedGitCommitSha;
  }
  /**
   * Immutable. The name of the workspace to compile. Must be in the format
   * `projects/locations/repositories/workspaces`.
   *
   * @param string $workspace
   */
  public function setWorkspace($workspace)
  {
    $this->workspace = $workspace;
  }
  /**
   * @return string
   */
  public function getWorkspace()
  {
    return $this->workspace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CompilationResult::class, 'Google_Service_Dataform_CompilationResult');

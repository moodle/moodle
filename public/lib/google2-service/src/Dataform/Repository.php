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

class Repository extends \Google\Model
{
  /**
   * Output only. The timestamp of when the repository was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataEncryptionStateType = DataEncryptionState::class;
  protected $dataEncryptionStateDataType = '';
  /**
   * Optional. The repository's user-friendly name.
   *
   * @var string
   */
  public $displayName;
  protected $gitRemoteSettingsType = GitRemoteSettings::class;
  protected $gitRemoteSettingsDataType = '';
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @var string
   */
  public $internalMetadata;
  /**
   * Optional. The reference to a KMS encryption key. If provided, it will be
   * used to encrypt user data in the repository and all child resources. It is
   * not possible to add or update the encryption key after the repository is
   * created. Example: `projects/{kms_project}/locations/{location}/keyRings/{ke
   * y_location}/cryptoKeys/{key}`
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Optional. Repository user labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The repository's name.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The name of the Secret Manager secret version to be used to
   * interpolate variables into the .npmrc file for package installation
   * operations. Must be in the format `projects/secrets/versions`. The file
   * itself must be in a JSON format.
   *
   * @var string
   */
  public $npmrcEnvironmentVariablesSecretVersion;
  /**
   * Optional. The service account to run workflow invocations under.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Optional. Input only. If set to true, the authenticated user will be
   * granted the roles/dataform.admin role on the created repository.
   *
   * @var bool
   */
  public $setAuthenticatedUserAdmin;
  protected $workspaceCompilationOverridesType = WorkspaceCompilationOverrides::class;
  protected $workspaceCompilationOverridesDataType = '';

  /**
   * Output only. The timestamp of when the repository was created.
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
   * Output only. A data encryption state of a Git repository if this Repository
   * is protected by a KMS key.
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
   * Optional. The repository's user-friendly name.
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
   * Optional. If set, configures this repository to be linked to a Git remote.
   *
   * @param GitRemoteSettings $gitRemoteSettings
   */
  public function setGitRemoteSettings(GitRemoteSettings $gitRemoteSettings)
  {
    $this->gitRemoteSettings = $gitRemoteSettings;
  }
  /**
   * @return GitRemoteSettings
   */
  public function getGitRemoteSettings()
  {
    return $this->gitRemoteSettings;
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
   * Optional. The reference to a KMS encryption key. If provided, it will be
   * used to encrypt user data in the repository and all child resources. It is
   * not possible to add or update the encryption key after the repository is
   * created. Example: `projects/{kms_project}/locations/{location}/keyRings/{ke
   * y_location}/cryptoKeys/{key}`
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Optional. Repository user labels.
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
   * Identifier. The repository's name.
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
   * Optional. The name of the Secret Manager secret version to be used to
   * interpolate variables into the .npmrc file for package installation
   * operations. Must be in the format `projects/secrets/versions`. The file
   * itself must be in a JSON format.
   *
   * @param string $npmrcEnvironmentVariablesSecretVersion
   */
  public function setNpmrcEnvironmentVariablesSecretVersion($npmrcEnvironmentVariablesSecretVersion)
  {
    $this->npmrcEnvironmentVariablesSecretVersion = $npmrcEnvironmentVariablesSecretVersion;
  }
  /**
   * @return string
   */
  public function getNpmrcEnvironmentVariablesSecretVersion()
  {
    return $this->npmrcEnvironmentVariablesSecretVersion;
  }
  /**
   * Optional. The service account to run workflow invocations under.
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
   * Optional. Input only. If set to true, the authenticated user will be
   * granted the roles/dataform.admin role on the created repository.
   *
   * @param bool $setAuthenticatedUserAdmin
   */
  public function setSetAuthenticatedUserAdmin($setAuthenticatedUserAdmin)
  {
    $this->setAuthenticatedUserAdmin = $setAuthenticatedUserAdmin;
  }
  /**
   * @return bool
   */
  public function getSetAuthenticatedUserAdmin()
  {
    return $this->setAuthenticatedUserAdmin;
  }
  /**
   * Optional. If set, fields of `workspace_compilation_overrides` override the
   * default compilation settings that are specified in dataform.json when
   * creating workspace-scoped compilation results. See documentation for
   * `WorkspaceCompilationOverrides` for more information.
   *
   * @param WorkspaceCompilationOverrides $workspaceCompilationOverrides
   */
  public function setWorkspaceCompilationOverrides(WorkspaceCompilationOverrides $workspaceCompilationOverrides)
  {
    $this->workspaceCompilationOverrides = $workspaceCompilationOverrides;
  }
  /**
   * @return WorkspaceCompilationOverrides
   */
  public function getWorkspaceCompilationOverrides()
  {
    return $this->workspaceCompilationOverrides;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Repository::class, 'Google_Service_Dataform_Repository');

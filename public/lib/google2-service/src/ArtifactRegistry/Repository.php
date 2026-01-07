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

namespace Google\Service\ArtifactRegistry;

class Repository extends \Google\Model
{
  /**
   * Unspecified package format.
   */
  public const FORMAT_FORMAT_UNSPECIFIED = 'FORMAT_UNSPECIFIED';
  /**
   * Docker package format.
   */
  public const FORMAT_DOCKER = 'DOCKER';
  /**
   * Maven package format.
   */
  public const FORMAT_MAVEN = 'MAVEN';
  /**
   * NPM package format.
   */
  public const FORMAT_NPM = 'NPM';
  /**
   * APT package format.
   */
  public const FORMAT_APT = 'APT';
  /**
   * YUM package format.
   */
  public const FORMAT_YUM = 'YUM';
  /**
   * GooGet package format.
   */
  public const FORMAT_GOOGET = 'GOOGET';
  /**
   * Python package format.
   */
  public const FORMAT_PYTHON = 'PYTHON';
  /**
   * Kubeflow Pipelines package format.
   */
  public const FORMAT_KFP = 'KFP';
  /**
   * Go package format.
   */
  public const FORMAT_GO = 'GO';
  /**
   * Generic package format.
   */
  public const FORMAT_GENERIC = 'GENERIC';
  /**
   * Ruby package format.
   */
  public const FORMAT_RUBY = 'RUBY';
  /**
   * Unspecified mode.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * A standard repository storing artifacts.
   */
  public const MODE_STANDARD_REPOSITORY = 'STANDARD_REPOSITORY';
  /**
   * A virtual repository to serve artifacts from one or more sources.
   */
  public const MODE_VIRTUAL_REPOSITORY = 'VIRTUAL_REPOSITORY';
  /**
   * A remote repository to serve artifacts from a remote source.
   */
  public const MODE_REMOTE_REPOSITORY = 'REMOTE_REPOSITORY';
  /**
   * An AOSS repository provides artifacts from AOSS upstreams.
   */
  public const MODE_AOSS_REPOSITORY = 'AOSS_REPOSITORY';
  /**
   * Replacement of AOSS_REPOSITORY.
   */
  public const MODE_ASSURED_OSS_REPOSITORY = 'ASSURED_OSS_REPOSITORY';
  protected $cleanupPoliciesType = CleanupPolicy::class;
  protected $cleanupPoliciesDataType = 'map';
  /**
   * Optional. If true, the cleanup pipeline is prevented from deleting versions
   * in this repository.
   *
   * @var bool
   */
  public $cleanupPolicyDryRun;
  /**
   * Output only. The time when the repository was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The user-provided description of the repository.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. If this is true, an unspecified repo type will be treated as
   * error rather than defaulting to standard.
   *
   * @var bool
   */
  public $disallowUnspecifiedMode;
  protected $dockerConfigType = DockerRepositoryConfig::class;
  protected $dockerConfigDataType = '';
  /**
   * Optional. The format of packages that are stored in the repository.
   *
   * @var string
   */
  public $format;
  /**
   * The Cloud KMS resource name of the customer managed encryption key that's
   * used to encrypt the contents of the Repository. Has the form: `projects/my-
   * project/locations/my-region/keyRings/my-kr/cryptoKeys/my-key`. This value
   * may not be changed after the Repository has been created.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Labels with user-defined metadata. This field may contain up to 64 entries.
   * Label keys and values may be no longer than 63 characters. Label keys must
   * begin with a lowercase letter and may only contain lowercase letters,
   * numeric characters, underscores, and dashes.
   *
   * @var string[]
   */
  public $labels;
  protected $mavenConfigType = MavenRepositoryConfig::class;
  protected $mavenConfigDataType = '';
  /**
   * Optional. The mode of the repository.
   *
   * @var string
   */
  public $mode;
  /**
   * The name of the repository, for example: `projects/p1/locations/us-
   * central1/repositories/repo1`. For each location in a project, repository
   * names must be unique.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The repository endpoint, for example: `us-docker.pkg.dev/my-
   * proj/my-repo`.
   *
   * @var string
   */
  public $registryUri;
  protected $remoteRepositoryConfigType = RemoteRepositoryConfig::class;
  protected $remoteRepositoryConfigDataType = '';
  /**
   * Output only. Whether or not this repository satisfies PZI.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Whether or not this repository satisfies PZS.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The size, in bytes, of all artifact storage in this
   * repository. Repositories that are generally available or in public preview
   * use this to calculate storage costs.
   *
   * @var string
   */
  public $sizeBytes;
  /**
   * Output only. The time when the repository was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $virtualRepositoryConfigType = VirtualRepositoryConfig::class;
  protected $virtualRepositoryConfigDataType = '';
  protected $vulnerabilityScanningConfigType = VulnerabilityScanningConfig::class;
  protected $vulnerabilityScanningConfigDataType = '';

  /**
   * Optional. Cleanup policies for this repository. Cleanup policies indicate
   * when certain package versions can be automatically deleted. Map keys are
   * policy IDs supplied by users during policy creation. They must unique
   * within a repository and be under 128 characters in length.
   *
   * @param CleanupPolicy[] $cleanupPolicies
   */
  public function setCleanupPolicies($cleanupPolicies)
  {
    $this->cleanupPolicies = $cleanupPolicies;
  }
  /**
   * @return CleanupPolicy[]
   */
  public function getCleanupPolicies()
  {
    return $this->cleanupPolicies;
  }
  /**
   * Optional. If true, the cleanup pipeline is prevented from deleting versions
   * in this repository.
   *
   * @param bool $cleanupPolicyDryRun
   */
  public function setCleanupPolicyDryRun($cleanupPolicyDryRun)
  {
    $this->cleanupPolicyDryRun = $cleanupPolicyDryRun;
  }
  /**
   * @return bool
   */
  public function getCleanupPolicyDryRun()
  {
    return $this->cleanupPolicyDryRun;
  }
  /**
   * Output only. The time when the repository was created.
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
   * The user-provided description of the repository.
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
   * Optional. If this is true, an unspecified repo type will be treated as
   * error rather than defaulting to standard.
   *
   * @param bool $disallowUnspecifiedMode
   */
  public function setDisallowUnspecifiedMode($disallowUnspecifiedMode)
  {
    $this->disallowUnspecifiedMode = $disallowUnspecifiedMode;
  }
  /**
   * @return bool
   */
  public function getDisallowUnspecifiedMode()
  {
    return $this->disallowUnspecifiedMode;
  }
  /**
   * Docker repository config contains repository level configuration for the
   * repositories of docker type.
   *
   * @param DockerRepositoryConfig $dockerConfig
   */
  public function setDockerConfig(DockerRepositoryConfig $dockerConfig)
  {
    $this->dockerConfig = $dockerConfig;
  }
  /**
   * @return DockerRepositoryConfig
   */
  public function getDockerConfig()
  {
    return $this->dockerConfig;
  }
  /**
   * Optional. The format of packages that are stored in the repository.
   *
   * Accepted values: FORMAT_UNSPECIFIED, DOCKER, MAVEN, NPM, APT, YUM, GOOGET,
   * PYTHON, KFP, GO, GENERIC, RUBY
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * The Cloud KMS resource name of the customer managed encryption key that's
   * used to encrypt the contents of the Repository. Has the form: `projects/my-
   * project/locations/my-region/keyRings/my-kr/cryptoKeys/my-key`. This value
   * may not be changed after the Repository has been created.
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
   * Labels with user-defined metadata. This field may contain up to 64 entries.
   * Label keys and values may be no longer than 63 characters. Label keys must
   * begin with a lowercase letter and may only contain lowercase letters,
   * numeric characters, underscores, and dashes.
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
   * Maven repository config contains repository level configuration for the
   * repositories of maven type.
   *
   * @param MavenRepositoryConfig $mavenConfig
   */
  public function setMavenConfig(MavenRepositoryConfig $mavenConfig)
  {
    $this->mavenConfig = $mavenConfig;
  }
  /**
   * @return MavenRepositoryConfig
   */
  public function getMavenConfig()
  {
    return $this->mavenConfig;
  }
  /**
   * Optional. The mode of the repository.
   *
   * Accepted values: MODE_UNSPECIFIED, STANDARD_REPOSITORY, VIRTUAL_REPOSITORY,
   * REMOTE_REPOSITORY, AOSS_REPOSITORY, ASSURED_OSS_REPOSITORY
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * The name of the repository, for example: `projects/p1/locations/us-
   * central1/repositories/repo1`. For each location in a project, repository
   * names must be unique.
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
   * Output only. The repository endpoint, for example: `us-docker.pkg.dev/my-
   * proj/my-repo`.
   *
   * @param string $registryUri
   */
  public function setRegistryUri($registryUri)
  {
    $this->registryUri = $registryUri;
  }
  /**
   * @return string
   */
  public function getRegistryUri()
  {
    return $this->registryUri;
  }
  /**
   * Configuration specific for a Remote Repository.
   *
   * @param RemoteRepositoryConfig $remoteRepositoryConfig
   */
  public function setRemoteRepositoryConfig(RemoteRepositoryConfig $remoteRepositoryConfig)
  {
    $this->remoteRepositoryConfig = $remoteRepositoryConfig;
  }
  /**
   * @return RemoteRepositoryConfig
   */
  public function getRemoteRepositoryConfig()
  {
    return $this->remoteRepositoryConfig;
  }
  /**
   * Output only. Whether or not this repository satisfies PZI.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Whether or not this repository satisfies PZS.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. The size, in bytes, of all artifact storage in this
   * repository. Repositories that are generally available or in public preview
   * use this to calculate storage costs.
   *
   * @param string $sizeBytes
   */
  public function setSizeBytes($sizeBytes)
  {
    $this->sizeBytes = $sizeBytes;
  }
  /**
   * @return string
   */
  public function getSizeBytes()
  {
    return $this->sizeBytes;
  }
  /**
   * Output only. The time when the repository was last updated.
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
   * Configuration specific for a Virtual Repository.
   *
   * @param VirtualRepositoryConfig $virtualRepositoryConfig
   */
  public function setVirtualRepositoryConfig(VirtualRepositoryConfig $virtualRepositoryConfig)
  {
    $this->virtualRepositoryConfig = $virtualRepositoryConfig;
  }
  /**
   * @return VirtualRepositoryConfig
   */
  public function getVirtualRepositoryConfig()
  {
    return $this->virtualRepositoryConfig;
  }
  /**
   * Optional. Config and state for vulnerability scanning of resources within
   * this Repository.
   *
   * @param VulnerabilityScanningConfig $vulnerabilityScanningConfig
   */
  public function setVulnerabilityScanningConfig(VulnerabilityScanningConfig $vulnerabilityScanningConfig)
  {
    $this->vulnerabilityScanningConfig = $vulnerabilityScanningConfig;
  }
  /**
   * @return VulnerabilityScanningConfig
   */
  public function getVulnerabilityScanningConfig()
  {
    return $this->vulnerabilityScanningConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Repository::class, 'Google_Service_ArtifactRegistry_Repository');

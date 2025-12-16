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

class MavenRepositoryConfig extends \Google\Model
{
  /**
   * VERSION_POLICY_UNSPECIFIED - the version policy is not defined. When the
   * version policy is not defined, no validation is performed for the versions.
   */
  public const VERSION_POLICY_VERSION_POLICY_UNSPECIFIED = 'VERSION_POLICY_UNSPECIFIED';
  /**
   * RELEASE - repository will accept only Release versions.
   */
  public const VERSION_POLICY_RELEASE = 'RELEASE';
  /**
   * SNAPSHOT - repository will accept only Snapshot versions.
   */
  public const VERSION_POLICY_SNAPSHOT = 'SNAPSHOT';
  /**
   * The repository with this flag will allow publishing the same snapshot
   * versions.
   *
   * @var bool
   */
  public $allowSnapshotOverwrites;
  /**
   * Version policy defines the versions that the registry will accept.
   *
   * @var string
   */
  public $versionPolicy;

  /**
   * The repository with this flag will allow publishing the same snapshot
   * versions.
   *
   * @param bool $allowSnapshotOverwrites
   */
  public function setAllowSnapshotOverwrites($allowSnapshotOverwrites)
  {
    $this->allowSnapshotOverwrites = $allowSnapshotOverwrites;
  }
  /**
   * @return bool
   */
  public function getAllowSnapshotOverwrites()
  {
    return $this->allowSnapshotOverwrites;
  }
  /**
   * Version policy defines the versions that the registry will accept.
   *
   * Accepted values: VERSION_POLICY_UNSPECIFIED, RELEASE, SNAPSHOT
   *
   * @param self::VERSION_POLICY_* $versionPolicy
   */
  public function setVersionPolicy($versionPolicy)
  {
    $this->versionPolicy = $versionPolicy;
  }
  /**
   * @return self::VERSION_POLICY_*
   */
  public function getVersionPolicy()
  {
    return $this->versionPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MavenRepositoryConfig::class, 'Google_Service_ArtifactRegistry_MavenRepositoryConfig');

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

class SecretVolume extends \Google\Collection
{
  protected $collection_key = 'versions';
  /**
   * The path within the container to mount the secret volume. For example,
   * setting the mount_path as `/etc/secrets` would mount the secret value files
   * under the `/etc/secrets` directory. This directory will also be completely
   * shadowed and unavailable to mount any other secrets. Recommended mount
   * path: /etc/secrets
   *
   * @var string
   */
  public $mountPath;
  /**
   * Project identifier (preferably project number but can also be the project
   * ID) of the project that contains the secret. If not set, it is assumed that
   * the secret is in the same project as the function.
   *
   * @var string
   */
  public $projectId;
  /**
   * Name of the secret in secret manager (not the full resource name).
   *
   * @var string
   */
  public $secret;
  protected $versionsType = SecretVersion::class;
  protected $versionsDataType = 'array';

  /**
   * The path within the container to mount the secret volume. For example,
   * setting the mount_path as `/etc/secrets` would mount the secret value files
   * under the `/etc/secrets` directory. This directory will also be completely
   * shadowed and unavailable to mount any other secrets. Recommended mount
   * path: /etc/secrets
   *
   * @param string $mountPath
   */
  public function setMountPath($mountPath)
  {
    $this->mountPath = $mountPath;
  }
  /**
   * @return string
   */
  public function getMountPath()
  {
    return $this->mountPath;
  }
  /**
   * Project identifier (preferably project number but can also be the project
   * ID) of the project that contains the secret. If not set, it is assumed that
   * the secret is in the same project as the function.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Name of the secret in secret manager (not the full resource name).
   *
   * @param string $secret
   */
  public function setSecret($secret)
  {
    $this->secret = $secret;
  }
  /**
   * @return string
   */
  public function getSecret()
  {
    return $this->secret;
  }
  /**
   * List of secret versions to mount for this secret. If empty, the `latest`
   * version of the secret will be made available in a file named after the
   * secret under the mount point.
   *
   * @param SecretVersion[] $versions
   */
  public function setVersions($versions)
  {
    $this->versions = $versions;
  }
  /**
   * @return SecretVersion[]
   */
  public function getVersions()
  {
    return $this->versions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecretVolume::class, 'Google_Service_CloudFunctions_SecretVolume');

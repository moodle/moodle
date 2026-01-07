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

class SecretVersion extends \Google\Model
{
  /**
   * Relative path of the file under the mount path where the secret value for
   * this version will be fetched and made available. For example, setting the
   * mount_path as '/etc/secrets' and path as `secret_foo` would mount the
   * secret value file at `/etc/secrets/secret_foo`.
   *
   * @var string
   */
  public $path;
  /**
   * Version of the secret (version number or the string 'latest'). It is
   * preferable to use `latest` version with secret volumes as secret value
   * changes are reflected immediately.
   *
   * @var string
   */
  public $version;

  /**
   * Relative path of the file under the mount path where the secret value for
   * this version will be fetched and made available. For example, setting the
   * mount_path as '/etc/secrets' and path as `secret_foo` would mount the
   * secret value file at `/etc/secrets/secret_foo`.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Version of the secret (version number or the string 'latest'). It is
   * preferable to use `latest` version with secret volumes as secret value
   * changes are reflected immediately.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecretVersion::class, 'Google_Service_CloudFunctions_SecretVersion');

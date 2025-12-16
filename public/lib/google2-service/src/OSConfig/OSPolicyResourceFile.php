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

namespace Google\Service\OSConfig;

class OSPolicyResourceFile extends \Google\Model
{
  /**
   * Defaults to false. When false, files are subject to validations based on
   * the file type: Remote: A checksum must be specified. Cloud Storage: An
   * object generation number must be specified.
   *
   * @var bool
   */
  public $allowInsecure;
  protected $gcsType = OSPolicyResourceFileGcs::class;
  protected $gcsDataType = '';
  /**
   * A local path within the VM to use.
   *
   * @var string
   */
  public $localPath;
  protected $remoteType = OSPolicyResourceFileRemote::class;
  protected $remoteDataType = '';

  /**
   * Defaults to false. When false, files are subject to validations based on
   * the file type: Remote: A checksum must be specified. Cloud Storage: An
   * object generation number must be specified.
   *
   * @param bool $allowInsecure
   */
  public function setAllowInsecure($allowInsecure)
  {
    $this->allowInsecure = $allowInsecure;
  }
  /**
   * @return bool
   */
  public function getAllowInsecure()
  {
    return $this->allowInsecure;
  }
  /**
   * A Cloud Storage object.
   *
   * @param OSPolicyResourceFileGcs $gcs
   */
  public function setGcs(OSPolicyResourceFileGcs $gcs)
  {
    $this->gcs = $gcs;
  }
  /**
   * @return OSPolicyResourceFileGcs
   */
  public function getGcs()
  {
    return $this->gcs;
  }
  /**
   * A local path within the VM to use.
   *
   * @param string $localPath
   */
  public function setLocalPath($localPath)
  {
    $this->localPath = $localPath;
  }
  /**
   * @return string
   */
  public function getLocalPath()
  {
    return $this->localPath;
  }
  /**
   * A generic remote file.
   *
   * @param OSPolicyResourceFileRemote $remote
   */
  public function setRemote(OSPolicyResourceFileRemote $remote)
  {
    $this->remote = $remote;
  }
  /**
   * @return OSPolicyResourceFileRemote
   */
  public function getRemote()
  {
    return $this->remote;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OSPolicyResourceFile::class, 'Google_Service_OSConfig_OSPolicyResourceFile');

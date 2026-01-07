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

namespace Google\Service\AndroidManagement;

class AppProcessInfo extends \Google\Collection
{
  protected $collection_key = 'packageNames';
  /**
   * SHA-256 hash of the base APK, in hexadecimal format.
   *
   * @var string
   */
  public $apkSha256Hash;
  /**
   * Package names of all packages that are associated with the particular user
   * ID. In most cases, this will be a single package name, the package that has
   * been assigned that user ID. If multiple application share a UID then all
   * packages sharing UID will be included.
   *
   * @var string[]
   */
  public $packageNames;
  /**
   * Process ID.
   *
   * @var int
   */
  public $pid;
  /**
   * Process name.
   *
   * @var string
   */
  public $processName;
  /**
   * SELinux policy info.
   *
   * @var string
   */
  public $seinfo;
  /**
   * Process start time.
   *
   * @var string
   */
  public $startTime;
  /**
   * UID of the package.
   *
   * @var int
   */
  public $uid;

  /**
   * SHA-256 hash of the base APK, in hexadecimal format.
   *
   * @param string $apkSha256Hash
   */
  public function setApkSha256Hash($apkSha256Hash)
  {
    $this->apkSha256Hash = $apkSha256Hash;
  }
  /**
   * @return string
   */
  public function getApkSha256Hash()
  {
    return $this->apkSha256Hash;
  }
  /**
   * Package names of all packages that are associated with the particular user
   * ID. In most cases, this will be a single package name, the package that has
   * been assigned that user ID. If multiple application share a UID then all
   * packages sharing UID will be included.
   *
   * @param string[] $packageNames
   */
  public function setPackageNames($packageNames)
  {
    $this->packageNames = $packageNames;
  }
  /**
   * @return string[]
   */
  public function getPackageNames()
  {
    return $this->packageNames;
  }
  /**
   * Process ID.
   *
   * @param int $pid
   */
  public function setPid($pid)
  {
    $this->pid = $pid;
  }
  /**
   * @return int
   */
  public function getPid()
  {
    return $this->pid;
  }
  /**
   * Process name.
   *
   * @param string $processName
   */
  public function setProcessName($processName)
  {
    $this->processName = $processName;
  }
  /**
   * @return string
   */
  public function getProcessName()
  {
    return $this->processName;
  }
  /**
   * SELinux policy info.
   *
   * @param string $seinfo
   */
  public function setSeinfo($seinfo)
  {
    $this->seinfo = $seinfo;
  }
  /**
   * @return string
   */
  public function getSeinfo()
  {
    return $this->seinfo;
  }
  /**
   * Process start time.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * UID of the package.
   *
   * @param int $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return int
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppProcessInfo::class, 'Google_Service_AndroidManagement_AppProcessInfo');

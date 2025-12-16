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

namespace Google\Service\WorkloadManager;

class SapDiscoveryResourceInstancePropertiesKernelVersionVersion extends \Google\Model
{
  /**
   * Optional. The build version number.
   *
   * @var int
   */
  public $build;
  /**
   * Optional. The major version number.
   *
   * @var int
   */
  public $major;
  /**
   * Optional. The minor version number.
   *
   * @var int
   */
  public $minor;
  /**
   * Optional. The patch version number.
   *
   * @var int
   */
  public $patch;
  /**
   * Optional. A catch-all for any unparsed version components. This is in case
   * the number of points in the version string exceeds the expected count of 4.
   *
   * @var string
   */
  public $remainder;

  /**
   * Optional. The build version number.
   *
   * @param int $build
   */
  public function setBuild($build)
  {
    $this->build = $build;
  }
  /**
   * @return int
   */
  public function getBuild()
  {
    return $this->build;
  }
  /**
   * Optional. The major version number.
   *
   * @param int $major
   */
  public function setMajor($major)
  {
    $this->major = $major;
  }
  /**
   * @return int
   */
  public function getMajor()
  {
    return $this->major;
  }
  /**
   * Optional. The minor version number.
   *
   * @param int $minor
   */
  public function setMinor($minor)
  {
    $this->minor = $minor;
  }
  /**
   * @return int
   */
  public function getMinor()
  {
    return $this->minor;
  }
  /**
   * Optional. The patch version number.
   *
   * @param int $patch
   */
  public function setPatch($patch)
  {
    $this->patch = $patch;
  }
  /**
   * @return int
   */
  public function getPatch()
  {
    return $this->patch;
  }
  /**
   * Optional. A catch-all for any unparsed version components. This is in case
   * the number of points in the version string exceeds the expected count of 4.
   *
   * @param string $remainder
   */
  public function setRemainder($remainder)
  {
    $this->remainder = $remainder;
  }
  /**
   * @return string
   */
  public function getRemainder()
  {
    return $this->remainder;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapDiscoveryResourceInstancePropertiesKernelVersionVersion::class, 'Google_Service_WorkloadManager_SapDiscoveryResourceInstancePropertiesKernelVersionVersion');

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

namespace Google\Service\CloudWorkstations;

class PersistentDirectory extends \Google\Model
{
  protected $gceHdType = GceHyperdiskBalancedHighAvailability::class;
  protected $gceHdDataType = '';
  protected $gcePdType = GceRegionalPersistentDisk::class;
  protected $gcePdDataType = '';
  /**
   * Optional. Location of this directory in the running workstation.
   *
   * @var string
   */
  public $mountPath;

  /**
   * A PersistentDirectory backed by a Compute Engine hyperdisk high
   * availability disk.
   *
   * @param GceHyperdiskBalancedHighAvailability $gceHd
   */
  public function setGceHd(GceHyperdiskBalancedHighAvailability $gceHd)
  {
    $this->gceHd = $gceHd;
  }
  /**
   * @return GceHyperdiskBalancedHighAvailability
   */
  public function getGceHd()
  {
    return $this->gceHd;
  }
  /**
   * A PersistentDirectory backed by a Compute Engine persistent disk.
   *
   * @param GceRegionalPersistentDisk $gcePd
   */
  public function setGcePd(GceRegionalPersistentDisk $gcePd)
  {
    $this->gcePd = $gcePd;
  }
  /**
   * @return GceRegionalPersistentDisk
   */
  public function getGcePd()
  {
    return $this->gcePd;
  }
  /**
   * Optional. Location of this directory in the running workstation.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PersistentDirectory::class, 'Google_Service_CloudWorkstations_PersistentDirectory');

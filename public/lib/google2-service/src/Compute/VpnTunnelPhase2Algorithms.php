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

namespace Google\Service\Compute;

class VpnTunnelPhase2Algorithms extends \Google\Collection
{
  protected $collection_key = 'pfs';
  /**
   * @var string[]
   */
  public $encryption;
  /**
   * @var string[]
   */
  public $integrity;
  /**
   * @var string[]
   */
  public $pfs;

  /**
   * @param string[] $encryption
   */
  public function setEncryption($encryption)
  {
    $this->encryption = $encryption;
  }
  /**
   * @return string[]
   */
  public function getEncryption()
  {
    return $this->encryption;
  }
  /**
   * @param string[] $integrity
   */
  public function setIntegrity($integrity)
  {
    $this->integrity = $integrity;
  }
  /**
   * @return string[]
   */
  public function getIntegrity()
  {
    return $this->integrity;
  }
  /**
   * @param string[] $pfs
   */
  public function setPfs($pfs)
  {
    $this->pfs = $pfs;
  }
  /**
   * @return string[]
   */
  public function getPfs()
  {
    return $this->pfs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpnTunnelPhase2Algorithms::class, 'Google_Service_Compute_VpnTunnelPhase2Algorithms');

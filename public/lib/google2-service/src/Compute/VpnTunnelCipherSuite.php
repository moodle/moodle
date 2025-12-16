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

class VpnTunnelCipherSuite extends \Google\Model
{
  protected $phase1Type = VpnTunnelPhase1Algorithms::class;
  protected $phase1DataType = '';
  protected $phase2Type = VpnTunnelPhase2Algorithms::class;
  protected $phase2DataType = '';

  /**
   * @param VpnTunnelPhase1Algorithms $phase1
   */
  public function setPhase1(VpnTunnelPhase1Algorithms $phase1)
  {
    $this->phase1 = $phase1;
  }
  /**
   * @return VpnTunnelPhase1Algorithms
   */
  public function getPhase1()
  {
    return $this->phase1;
  }
  /**
   * @param VpnTunnelPhase2Algorithms $phase2
   */
  public function setPhase2(VpnTunnelPhase2Algorithms $phase2)
  {
    $this->phase2 = $phase2;
  }
  /**
   * @return VpnTunnelPhase2Algorithms
   */
  public function getPhase2()
  {
    return $this->phase2;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpnTunnelCipherSuite::class, 'Google_Service_Compute_VpnTunnelCipherSuite');

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

namespace Google\Service\Spanner;

class QuorumType extends \Google\Model
{
  protected $dualRegionType = DualRegionQuorum::class;
  protected $dualRegionDataType = '';
  protected $singleRegionType = SingleRegionQuorum::class;
  protected $singleRegionDataType = '';

  /**
   * Dual-region quorum type.
   *
   * @param DualRegionQuorum $dualRegion
   */
  public function setDualRegion(DualRegionQuorum $dualRegion)
  {
    $this->dualRegion = $dualRegion;
  }
  /**
   * @return DualRegionQuorum
   */
  public function getDualRegion()
  {
    return $this->dualRegion;
  }
  /**
   * Single-region quorum type.
   *
   * @param SingleRegionQuorum $singleRegion
   */
  public function setSingleRegion(SingleRegionQuorum $singleRegion)
  {
    $this->singleRegion = $singleRegion;
  }
  /**
   * @return SingleRegionQuorum
   */
  public function getSingleRegion()
  {
    return $this->singleRegion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QuorumType::class, 'Google_Service_Spanner_QuorumType');

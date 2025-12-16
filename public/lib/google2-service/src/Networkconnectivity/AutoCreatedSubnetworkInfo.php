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

namespace Google\Service\Networkconnectivity;

class AutoCreatedSubnetworkInfo extends \Google\Model
{
  /**
   * Output only. URI of the automatically created Internal Range. Only set if
   * the subnetwork mode is AUTO_CREATED during creation.
   *
   * @var string
   */
  public $internalRange;
  /**
   * Output only. URI of the automatically created Internal Range reference.
   * Only set if the subnetwork mode is AUTO_CREATED during creation.
   *
   * @var string
   */
  public $internalRangeRef;
  /**
   * Output only. URI of the automatically created subnetwork. Only set if the
   * subnetwork mode is AUTO_CREATED during creation.
   *
   * @var string
   */
  public $subnetwork;
  /**
   * Output only. URI of the automatically created subnetwork reference. Only
   * set if the subnetwork mode is AUTO_CREATED during creation.
   *
   * @var string
   */
  public $subnetworkRef;

  /**
   * Output only. URI of the automatically created Internal Range. Only set if
   * the subnetwork mode is AUTO_CREATED during creation.
   *
   * @param string $internalRange
   */
  public function setInternalRange($internalRange)
  {
    $this->internalRange = $internalRange;
  }
  /**
   * @return string
   */
  public function getInternalRange()
  {
    return $this->internalRange;
  }
  /**
   * Output only. URI of the automatically created Internal Range reference.
   * Only set if the subnetwork mode is AUTO_CREATED during creation.
   *
   * @param string $internalRangeRef
   */
  public function setInternalRangeRef($internalRangeRef)
  {
    $this->internalRangeRef = $internalRangeRef;
  }
  /**
   * @return string
   */
  public function getInternalRangeRef()
  {
    return $this->internalRangeRef;
  }
  /**
   * Output only. URI of the automatically created subnetwork. Only set if the
   * subnetwork mode is AUTO_CREATED during creation.
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
  /**
   * Output only. URI of the automatically created subnetwork reference. Only
   * set if the subnetwork mode is AUTO_CREATED during creation.
   *
   * @param string $subnetworkRef
   */
  public function setSubnetworkRef($subnetworkRef)
  {
    $this->subnetworkRef = $subnetworkRef;
  }
  /**
   * @return string
   */
  public function getSubnetworkRef()
  {
    return $this->subnetworkRef;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoCreatedSubnetworkInfo::class, 'Google_Service_Networkconnectivity_AutoCreatedSubnetworkInfo');

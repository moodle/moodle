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

class InterconnectGroupPhysicalStructureMetrosFacilitiesZones extends \Google\Collection
{
  protected $collection_key = 'interconnects';
  /**
   * Output only. [Output Only] URLs of Interconnects in this redundancy group
   * in the given metro, facility, and zone.
   *
   * @var string[]
   */
  public $interconnects;
  /**
   * Output only. [Output Only] The name of the zone, either "zone1" or "zone2".
   * This is the second component of the location of Interconnects in this
   * facility.
   *
   * @var string
   */
  public $zone;

  /**
   * Output only. [Output Only] URLs of Interconnects in this redundancy group
   * in the given metro, facility, and zone.
   *
   * @param string[] $interconnects
   */
  public function setInterconnects($interconnects)
  {
    $this->interconnects = $interconnects;
  }
  /**
   * @return string[]
   */
  public function getInterconnects()
  {
    return $this->interconnects;
  }
  /**
   * Output only. [Output Only] The name of the zone, either "zone1" or "zone2".
   * This is the second component of the location of Interconnects in this
   * facility.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectGroupPhysicalStructureMetrosFacilitiesZones::class, 'Google_Service_Compute_InterconnectGroupPhysicalStructureMetrosFacilitiesZones');

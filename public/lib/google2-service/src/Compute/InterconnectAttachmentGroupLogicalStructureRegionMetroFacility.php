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

class InterconnectAttachmentGroupLogicalStructureRegionMetroFacility extends \Google\Collection
{
  protected $collection_key = 'zones';
  /**
   * Output only. [Output Only] The name of a facility, like "iad-1234".
   *
   * @var string
   */
  public $facility;
  protected $zonesType = InterconnectAttachmentGroupLogicalStructureRegionMetroFacilityZone::class;
  protected $zonesDataType = 'array';

  /**
   * Output only. [Output Only] The name of a facility, like "iad-1234".
   *
   * @param string $facility
   */
  public function setFacility($facility)
  {
    $this->facility = $facility;
  }
  /**
   * @return string
   */
  public function getFacility()
  {
    return $this->facility;
  }
  /**
   * @param InterconnectAttachmentGroupLogicalStructureRegionMetroFacilityZone[] $zones
   */
  public function setZones($zones)
  {
    $this->zones = $zones;
  }
  /**
   * @return InterconnectAttachmentGroupLogicalStructureRegionMetroFacilityZone[]
   */
  public function getZones()
  {
    return $this->zones;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachmentGroupLogicalStructureRegionMetroFacility::class, 'Google_Service_Compute_InterconnectAttachmentGroupLogicalStructureRegionMetroFacility');

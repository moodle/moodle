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

class InterconnectGroupPhysicalStructureMetros extends \Google\Collection
{
  protected $collection_key = 'facilities';
  protected $facilitiesType = InterconnectGroupPhysicalStructureMetrosFacilities::class;
  protected $facilitiesDataType = 'array';
  /**
   * Output only. [Output Only] The name of the metro, as a three-letter
   * lowercase string like "iad". This is the first component of the location of
   * Interconnects underneath this.
   *
   * @var string
   */
  public $metro;

  /**
   * @param InterconnectGroupPhysicalStructureMetrosFacilities[] $facilities
   */
  public function setFacilities($facilities)
  {
    $this->facilities = $facilities;
  }
  /**
   * @return InterconnectGroupPhysicalStructureMetrosFacilities[]
   */
  public function getFacilities()
  {
    return $this->facilities;
  }
  /**
   * Output only. [Output Only] The name of the metro, as a three-letter
   * lowercase string like "iad". This is the first component of the location of
   * Interconnects underneath this.
   *
   * @param string $metro
   */
  public function setMetro($metro)
  {
    $this->metro = $metro;
  }
  /**
   * @return string
   */
  public function getMetro()
  {
    return $this->metro;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectGroupPhysicalStructureMetros::class, 'Google_Service_Compute_InterconnectGroupPhysicalStructureMetros');

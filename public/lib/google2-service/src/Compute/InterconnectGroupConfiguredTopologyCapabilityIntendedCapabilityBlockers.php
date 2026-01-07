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

class InterconnectGroupConfiguredTopologyCapabilityIntendedCapabilityBlockers extends \Google\Collection
{
  public const BLOCKER_TYPE_INCOMPATIBLE_METROS = 'INCOMPATIBLE_METROS';
  public const BLOCKER_TYPE_NOT_AVAILABLE = 'NOT_AVAILABLE';
  public const BLOCKER_TYPE_NO_INTERCONNECTS = 'NO_INTERCONNECTS';
  public const BLOCKER_TYPE_NO_INTERCONNECTS_IN_METRO_AND_ZONE = 'NO_INTERCONNECTS_IN_METRO_AND_ZONE';
  public const BLOCKER_TYPE_OTHER = 'OTHER';
  public const BLOCKER_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  protected $collection_key = 'zones';
  /**
   * @var string
   */
  public $blockerType;
  /**
   * Output only. [Output Only] The url of Google Cloud public documentation
   * explaining this requirement. This is set for every type of requirement.
   *
   * @var string
   */
  public $documentationLink;
  /**
   * Output only. [Output Only] A human-readable explanation of this requirement
   * and why it's not met. This is set for every type of requirement.
   *
   * @var string
   */
  public $explanation;
  /**
   * Output only. [Output Only] Facilities used to explain this blocker in more
   * detail. Like physicalStructure.metros.facilities.facility, this is a
   * numeric string like "5467".
   *
   * @var string[]
   */
  public $facilities;
  /**
   * Output only. [Output Only] Interconnects used to explain this blocker in
   * more detail.
   *
   * @var string[]
   */
  public $interconnects;
  /**
   * Output only. [Output Only] Metros used to explain this blocker in more
   * detail. These are three-letter lowercase strings like "iad". A blocker like
   * INCOMPATIBLE_METROS will specify the problematic metros in this field.
   *
   * @var string[]
   */
  public $metros;
  /**
   * Output only. [Output Only] Zones used to explain this blocker in more
   * detail. Zone names are "zone1" and/or "zone2".
   *
   * @var string[]
   */
  public $zones;

  /**
   * @param self::BLOCKER_TYPE_* $blockerType
   */
  public function setBlockerType($blockerType)
  {
    $this->blockerType = $blockerType;
  }
  /**
   * @return self::BLOCKER_TYPE_*
   */
  public function getBlockerType()
  {
    return $this->blockerType;
  }
  /**
   * Output only. [Output Only] The url of Google Cloud public documentation
   * explaining this requirement. This is set for every type of requirement.
   *
   * @param string $documentationLink
   */
  public function setDocumentationLink($documentationLink)
  {
    $this->documentationLink = $documentationLink;
  }
  /**
   * @return string
   */
  public function getDocumentationLink()
  {
    return $this->documentationLink;
  }
  /**
   * Output only. [Output Only] A human-readable explanation of this requirement
   * and why it's not met. This is set for every type of requirement.
   *
   * @param string $explanation
   */
  public function setExplanation($explanation)
  {
    $this->explanation = $explanation;
  }
  /**
   * @return string
   */
  public function getExplanation()
  {
    return $this->explanation;
  }
  /**
   * Output only. [Output Only] Facilities used to explain this blocker in more
   * detail. Like physicalStructure.metros.facilities.facility, this is a
   * numeric string like "5467".
   *
   * @param string[] $facilities
   */
  public function setFacilities($facilities)
  {
    $this->facilities = $facilities;
  }
  /**
   * @return string[]
   */
  public function getFacilities()
  {
    return $this->facilities;
  }
  /**
   * Output only. [Output Only] Interconnects used to explain this blocker in
   * more detail.
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
   * Output only. [Output Only] Metros used to explain this blocker in more
   * detail. These are three-letter lowercase strings like "iad". A blocker like
   * INCOMPATIBLE_METROS will specify the problematic metros in this field.
   *
   * @param string[] $metros
   */
  public function setMetros($metros)
  {
    $this->metros = $metros;
  }
  /**
   * @return string[]
   */
  public function getMetros()
  {
    return $this->metros;
  }
  /**
   * Output only. [Output Only] Zones used to explain this blocker in more
   * detail. Zone names are "zone1" and/or "zone2".
   *
   * @param string[] $zones
   */
  public function setZones($zones)
  {
    $this->zones = $zones;
  }
  /**
   * @return string[]
   */
  public function getZones()
  {
    return $this->zones;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectGroupConfiguredTopologyCapabilityIntendedCapabilityBlockers::class, 'Google_Service_Compute_InterconnectGroupConfiguredTopologyCapabilityIntendedCapabilityBlockers');

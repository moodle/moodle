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

class InterconnectAttachmentGroupConfiguredAvailabilitySLAIntendedSlaBlockers extends \Google\Collection
{
  public const BLOCKER_TYPE_BLOCKER_TYPE_UNSPECIFIED = 'BLOCKER_TYPE_UNSPECIFIED';
  public const BLOCKER_TYPE_INCOMPATIBLE_METROS = 'INCOMPATIBLE_METROS';
  public const BLOCKER_TYPE_INCOMPATIBLE_REGIONS = 'INCOMPATIBLE_REGIONS';
  public const BLOCKER_TYPE_MISSING_GLOBAL_ROUTING = 'MISSING_GLOBAL_ROUTING';
  public const BLOCKER_TYPE_NO_ATTACHMENTS = 'NO_ATTACHMENTS';
  public const BLOCKER_TYPE_NO_ATTACHMENTS_IN_METRO_AND_ZONE = 'NO_ATTACHMENTS_IN_METRO_AND_ZONE';
  public const BLOCKER_TYPE_OTHER = 'OTHER';
  protected $collection_key = 'zones';
  /**
   * Output only. [Output Only] URLs of any particular Attachments to explain
   * this blocker in more detail.
   *
   * @var string[]
   */
  public $attachments;
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
   * Output only. [Output Only] Metros used to explain this blocker in more
   * detail. These are three-letter lowercase strings like "iad". This will be
   * set for some blockers (like NO_ATTACHMENTS_IN_METRO_AND_ZONE) but does not
   * apply to others.
   *
   * @var string[]
   */
  public $metros;
  /**
   * Output only. [Output Only] Regions used to explain this blocker in more
   * detail. These are region names formatted like "us-central1". This will be
   * set for some blockers (like INCOMPATIBLE_REGIONS) but does not apply to
   * others.
   *
   * @var string[]
   */
  public $regions;
  /**
   * Output only. [Output Only] Zones used to explain this blocker in more
   * detail. Format is "zone1" and/or "zone2". This will be set for some
   * blockers (like  MISSING_ZONE) but does not apply to others.
   *
   * @var string[]
   */
  public $zones;

  /**
   * Output only. [Output Only] URLs of any particular Attachments to explain
   * this blocker in more detail.
   *
   * @param string[] $attachments
   */
  public function setAttachments($attachments)
  {
    $this->attachments = $attachments;
  }
  /**
   * @return string[]
   */
  public function getAttachments()
  {
    return $this->attachments;
  }
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
   * Output only. [Output Only] Metros used to explain this blocker in more
   * detail. These are three-letter lowercase strings like "iad". This will be
   * set for some blockers (like NO_ATTACHMENTS_IN_METRO_AND_ZONE) but does not
   * apply to others.
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
   * Output only. [Output Only] Regions used to explain this blocker in more
   * detail. These are region names formatted like "us-central1". This will be
   * set for some blockers (like INCOMPATIBLE_REGIONS) but does not apply to
   * others.
   *
   * @param string[] $regions
   */
  public function setRegions($regions)
  {
    $this->regions = $regions;
  }
  /**
   * @return string[]
   */
  public function getRegions()
  {
    return $this->regions;
  }
  /**
   * Output only. [Output Only] Zones used to explain this blocker in more
   * detail. Format is "zone1" and/or "zone2". This will be set for some
   * blockers (like  MISSING_ZONE) but does not apply to others.
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
class_alias(InterconnectAttachmentGroupConfiguredAvailabilitySLAIntendedSlaBlockers::class, 'Google_Service_Compute_InterconnectAttachmentGroupConfiguredAvailabilitySLAIntendedSlaBlockers');

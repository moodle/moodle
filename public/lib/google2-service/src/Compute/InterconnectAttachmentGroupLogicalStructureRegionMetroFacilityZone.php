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

class InterconnectAttachmentGroupLogicalStructureRegionMetroFacilityZone extends \Google\Collection
{
  protected $collection_key = 'attachments';
  /**
   * Output only. [Output Only] URLs of Attachments in the given zone, to the
   * given region, on Interconnects in the given facility and metro. Every
   * Attachment in the AG has such an entry.
   *
   * @var string[]
   */
  public $attachments;
  /**
   * Output only. [Output Only] The name of a zone, either "zone1" or "zone2".
   *
   * @var string
   */
  public $zone;

  /**
   * Output only. [Output Only] URLs of Attachments in the given zone, to the
   * given region, on Interconnects in the given facility and metro. Every
   * Attachment in the AG has such an entry.
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
   * Output only. [Output Only] The name of a zone, either "zone1" or "zone2".
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
class_alias(InterconnectAttachmentGroupLogicalStructureRegionMetroFacilityZone::class, 'Google_Service_Compute_InterconnectAttachmentGroupLogicalStructureRegionMetroFacilityZone');

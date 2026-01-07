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

namespace Google\Service\Dfareporting;

class CreativeAssignment extends \Google\Collection
{
  protected $collection_key = 'richMediaExitOverrides';
  /**
   * Whether this creative assignment is active. When true, the creative will be
   * included in the ad's rotation.
   *
   * @var bool
   */
  public $active;
  /**
   * Whether applicable event tags should fire when this creative assignment is
   * rendered. If this value is unset when the ad is inserted or updated, it
   * will default to true for all creative types EXCEPT for INTERNAL_REDIRECT,
   * INTERSTITIAL_INTERNAL_REDIRECT, and INSTREAM_VIDEO.
   *
   * @var bool
   */
  public $applyEventTags;
  protected $clickThroughUrlType = ClickThroughUrl::class;
  protected $clickThroughUrlDataType = '';
  protected $companionCreativeOverridesType = CompanionClickThroughOverride::class;
  protected $companionCreativeOverridesDataType = 'array';
  protected $creativeGroupAssignmentsType = CreativeGroupAssignment::class;
  protected $creativeGroupAssignmentsDataType = 'array';
  /**
   * ID of the creative to be assigned. This is a required field.
   *
   * @var string
   */
  public $creativeId;
  protected $creativeIdDimensionValueType = DimensionValue::class;
  protected $creativeIdDimensionValueDataType = '';
  /**
   * @var string
   */
  public $endTime;
  protected $richMediaExitOverridesType = RichMediaExitOverride::class;
  protected $richMediaExitOverridesDataType = 'array';
  /**
   * Sequence number of the creative assignment, applicable when the rotation
   * type is CREATIVE_ROTATION_TYPE_SEQUENTIAL. Acceptable values are 1 to
   * 65535, inclusive.
   *
   * @var int
   */
  public $sequence;
  /**
   * Whether the creative to be assigned is SSL-compliant. This is a read-only
   * field that is auto-generated when the ad is inserted or updated.
   *
   * @var bool
   */
  public $sslCompliant;
  /**
   * @var string
   */
  public $startTime;
  /**
   * Weight of the creative assignment, applicable when the rotation type is
   * CREATIVE_ROTATION_TYPE_RANDOM. Value must be greater than or equal to 1.
   *
   * @var int
   */
  public $weight;

  /**
   * Whether this creative assignment is active. When true, the creative will be
   * included in the ad's rotation.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Whether applicable event tags should fire when this creative assignment is
   * rendered. If this value is unset when the ad is inserted or updated, it
   * will default to true for all creative types EXCEPT for INTERNAL_REDIRECT,
   * INTERSTITIAL_INTERNAL_REDIRECT, and INSTREAM_VIDEO.
   *
   * @param bool $applyEventTags
   */
  public function setApplyEventTags($applyEventTags)
  {
    $this->applyEventTags = $applyEventTags;
  }
  /**
   * @return bool
   */
  public function getApplyEventTags()
  {
    return $this->applyEventTags;
  }
  /**
   * Click-through URL of the creative assignment.
   *
   * @param ClickThroughUrl $clickThroughUrl
   */
  public function setClickThroughUrl(ClickThroughUrl $clickThroughUrl)
  {
    $this->clickThroughUrl = $clickThroughUrl;
  }
  /**
   * @return ClickThroughUrl
   */
  public function getClickThroughUrl()
  {
    return $this->clickThroughUrl;
  }
  /**
   * Companion creative overrides for this creative assignment. Applicable to
   * video ads.
   *
   * @param CompanionClickThroughOverride[] $companionCreativeOverrides
   */
  public function setCompanionCreativeOverrides($companionCreativeOverrides)
  {
    $this->companionCreativeOverrides = $companionCreativeOverrides;
  }
  /**
   * @return CompanionClickThroughOverride[]
   */
  public function getCompanionCreativeOverrides()
  {
    return $this->companionCreativeOverrides;
  }
  /**
   * Creative group assignments for this creative assignment. Only one
   * assignment per creative group number is allowed for a maximum of two
   * assignments.
   *
   * @param CreativeGroupAssignment[] $creativeGroupAssignments
   */
  public function setCreativeGroupAssignments($creativeGroupAssignments)
  {
    $this->creativeGroupAssignments = $creativeGroupAssignments;
  }
  /**
   * @return CreativeGroupAssignment[]
   */
  public function getCreativeGroupAssignments()
  {
    return $this->creativeGroupAssignments;
  }
  /**
   * ID of the creative to be assigned. This is a required field.
   *
   * @param string $creativeId
   */
  public function setCreativeId($creativeId)
  {
    $this->creativeId = $creativeId;
  }
  /**
   * @return string
   */
  public function getCreativeId()
  {
    return $this->creativeId;
  }
  /**
   * Dimension value for the ID of the creative. This is a read-only, auto-
   * generated field.
   *
   * @param DimensionValue $creativeIdDimensionValue
   */
  public function setCreativeIdDimensionValue(DimensionValue $creativeIdDimensionValue)
  {
    $this->creativeIdDimensionValue = $creativeIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getCreativeIdDimensionValue()
  {
    return $this->creativeIdDimensionValue;
  }
  /**
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Rich media exit overrides for this creative assignment. Applicable when the
   * creative type is any of the following: - DISPLAY - RICH_MEDIA_INPAGE -
   * RICH_MEDIA_INPAGE_FLOATING - RICH_MEDIA_IM_EXPAND - RICH_MEDIA_EXPANDING -
   * RICH_MEDIA_INTERSTITIAL_FLOAT - RICH_MEDIA_MOBILE_IN_APP -
   * RICH_MEDIA_MULTI_FLOATING - RICH_MEDIA_PEEL_DOWN - VPAID_LINEAR -
   * VPAID_NON_LINEAR
   *
   * @param RichMediaExitOverride[] $richMediaExitOverrides
   */
  public function setRichMediaExitOverrides($richMediaExitOverrides)
  {
    $this->richMediaExitOverrides = $richMediaExitOverrides;
  }
  /**
   * @return RichMediaExitOverride[]
   */
  public function getRichMediaExitOverrides()
  {
    return $this->richMediaExitOverrides;
  }
  /**
   * Sequence number of the creative assignment, applicable when the rotation
   * type is CREATIVE_ROTATION_TYPE_SEQUENTIAL. Acceptable values are 1 to
   * 65535, inclusive.
   *
   * @param int $sequence
   */
  public function setSequence($sequence)
  {
    $this->sequence = $sequence;
  }
  /**
   * @return int
   */
  public function getSequence()
  {
    return $this->sequence;
  }
  /**
   * Whether the creative to be assigned is SSL-compliant. This is a read-only
   * field that is auto-generated when the ad is inserted or updated.
   *
   * @param bool $sslCompliant
   */
  public function setSslCompliant($sslCompliant)
  {
    $this->sslCompliant = $sslCompliant;
  }
  /**
   * @return bool
   */
  public function getSslCompliant()
  {
    return $this->sslCompliant;
  }
  /**
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Weight of the creative assignment, applicable when the rotation type is
   * CREATIVE_ROTATION_TYPE_RANDOM. Value must be greater than or equal to 1.
   *
   * @param int $weight
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }
  /**
   * @return int
   */
  public function getWeight()
  {
    return $this->weight;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeAssignment::class, 'Google_Service_Dfareporting_CreativeAssignment');

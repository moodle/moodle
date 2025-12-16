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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2TrafficTarget extends \Google\Model
{
  /**
   * Unspecified instance allocation type.
   */
  public const TYPE_TRAFFIC_TARGET_ALLOCATION_TYPE_UNSPECIFIED = 'TRAFFIC_TARGET_ALLOCATION_TYPE_UNSPECIFIED';
  /**
   * Allocates instances to the Service's latest ready Revision.
   */
  public const TYPE_TRAFFIC_TARGET_ALLOCATION_TYPE_LATEST = 'TRAFFIC_TARGET_ALLOCATION_TYPE_LATEST';
  /**
   * Allocates instances to a Revision by name.
   */
  public const TYPE_TRAFFIC_TARGET_ALLOCATION_TYPE_REVISION = 'TRAFFIC_TARGET_ALLOCATION_TYPE_REVISION';
  /**
   * Specifies percent of the traffic to this Revision. This defaults to zero if
   * unspecified.
   *
   * @var int
   */
  public $percent;
  /**
   * Revision to which to send this portion of traffic, if traffic allocation is
   * by revision.
   *
   * @var string
   */
  public $revision;
  /**
   * Indicates a string to be part of the URI to exclusively reference this
   * target.
   *
   * @var string
   */
  public $tag;
  /**
   * The allocation type for this traffic target.
   *
   * @var string
   */
  public $type;

  /**
   * Specifies percent of the traffic to this Revision. This defaults to zero if
   * unspecified.
   *
   * @param int $percent
   */
  public function setPercent($percent)
  {
    $this->percent = $percent;
  }
  /**
   * @return int
   */
  public function getPercent()
  {
    return $this->percent;
  }
  /**
   * Revision to which to send this portion of traffic, if traffic allocation is
   * by revision.
   *
   * @param string $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return string
   */
  public function getRevision()
  {
    return $this->revision;
  }
  /**
   * Indicates a string to be part of the URI to exclusively reference this
   * target.
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
  /**
   * The allocation type for this traffic target.
   *
   * Accepted values: TRAFFIC_TARGET_ALLOCATION_TYPE_UNSPECIFIED,
   * TRAFFIC_TARGET_ALLOCATION_TYPE_LATEST,
   * TRAFFIC_TARGET_ALLOCATION_TYPE_REVISION
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2TrafficTarget::class, 'Google_Service_CloudRun_GoogleCloudRunV2TrafficTarget');

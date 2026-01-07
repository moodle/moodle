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

class GoogleCloudRunV2InstanceSplitStatus extends \Google\Model
{
  /**
   * Unspecified instance allocation type.
   */
  public const TYPE_INSTANCE_SPLIT_ALLOCATION_TYPE_UNSPECIFIED = 'INSTANCE_SPLIT_ALLOCATION_TYPE_UNSPECIFIED';
  /**
   * Allocates instances to the Service's latest ready Revision.
   */
  public const TYPE_INSTANCE_SPLIT_ALLOCATION_TYPE_LATEST = 'INSTANCE_SPLIT_ALLOCATION_TYPE_LATEST';
  /**
   * Allocates instances to a Revision by name.
   */
  public const TYPE_INSTANCE_SPLIT_ALLOCATION_TYPE_REVISION = 'INSTANCE_SPLIT_ALLOCATION_TYPE_REVISION';
  /**
   * Specifies percent of the instance split to this Revision.
   *
   * @var int
   */
  public $percent;
  /**
   * Revision to which this instance split is assigned.
   *
   * @var string
   */
  public $revision;
  /**
   * The allocation type for this instance split.
   *
   * @var string
   */
  public $type;

  /**
   * Specifies percent of the instance split to this Revision.
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
   * Revision to which this instance split is assigned.
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
   * The allocation type for this instance split.
   *
   * Accepted values: INSTANCE_SPLIT_ALLOCATION_TYPE_UNSPECIFIED,
   * INSTANCE_SPLIT_ALLOCATION_TYPE_LATEST,
   * INSTANCE_SPLIT_ALLOCATION_TYPE_REVISION
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
class_alias(GoogleCloudRunV2InstanceSplitStatus::class, 'Google_Service_CloudRun_GoogleCloudRunV2InstanceSplitStatus');

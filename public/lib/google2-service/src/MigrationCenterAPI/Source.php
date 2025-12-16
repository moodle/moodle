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

namespace Google\Service\MigrationCenterAPI;

class Source extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The source is active and ready to be used.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * In the process of being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Source is in an invalid state. Asset frames reported to it will be ignored.
   */
  public const STATE_INVALID = 'INVALID';
  /**
   * Unspecified
   */
  public const TYPE_SOURCE_TYPE_UNKNOWN = 'SOURCE_TYPE_UNKNOWN';
  /**
   * Manually uploaded file (e.g. CSV)
   */
  public const TYPE_SOURCE_TYPE_UPLOAD = 'SOURCE_TYPE_UPLOAD';
  /**
   * Guest-level info
   */
  public const TYPE_SOURCE_TYPE_GUEST_OS_SCAN = 'SOURCE_TYPE_GUEST_OS_SCAN';
  /**
   * Inventory-level scan
   */
  public const TYPE_SOURCE_TYPE_INVENTORY_SCAN = 'SOURCE_TYPE_INVENTORY_SCAN';
  /**
   * Third-party owned sources.
   */
  public const TYPE_SOURCE_TYPE_CUSTOM = 'SOURCE_TYPE_CUSTOM';
  /**
   * Discovery clients
   */
  public const TYPE_SOURCE_TYPE_DISCOVERY_CLIENT = 'SOURCE_TYPE_DISCOVERY_CLIENT';
  /**
   * Output only. The timestamp when the source was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Free-text description.
   *
   * @var string
   */
  public $description;
  /**
   * User-friendly display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The number of frames that were reported by the source and
   * contained errors.
   *
   * @var int
   */
  public $errorFrameCount;
  /**
   * If `true`, the source is managed by other service(s).
   *
   * @var bool
   */
  public $managed;
  /**
   * Output only. The full name of the source.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Number of frames that are still being processed.
   *
   * @var int
   */
  public $pendingFrameCount;
  /**
   * The information confidence of the source. The higher the value, the higher
   * the confidence.
   *
   * @var int
   */
  public $priority;
  /**
   * Output only. The state of the source.
   *
   * @var string
   */
  public $state;
  /**
   * Data source type.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The timestamp when the source was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The timestamp when the source was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Free-text description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * User-friendly display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The number of frames that were reported by the source and
   * contained errors.
   *
   * @param int $errorFrameCount
   */
  public function setErrorFrameCount($errorFrameCount)
  {
    $this->errorFrameCount = $errorFrameCount;
  }
  /**
   * @return int
   */
  public function getErrorFrameCount()
  {
    return $this->errorFrameCount;
  }
  /**
   * If `true`, the source is managed by other service(s).
   *
   * @param bool $managed
   */
  public function setManaged($managed)
  {
    $this->managed = $managed;
  }
  /**
   * @return bool
   */
  public function getManaged()
  {
    return $this->managed;
  }
  /**
   * Output only. The full name of the source.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. Number of frames that are still being processed.
   *
   * @param int $pendingFrameCount
   */
  public function setPendingFrameCount($pendingFrameCount)
  {
    $this->pendingFrameCount = $pendingFrameCount;
  }
  /**
   * @return int
   */
  public function getPendingFrameCount()
  {
    return $this->pendingFrameCount;
  }
  /**
   * The information confidence of the source. The higher the value, the higher
   * the confidence.
   *
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Output only. The state of the source.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, DELETING, INVALID
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Data source type.
   *
   * Accepted values: SOURCE_TYPE_UNKNOWN, SOURCE_TYPE_UPLOAD,
   * SOURCE_TYPE_GUEST_OS_SCAN, SOURCE_TYPE_INVENTORY_SCAN, SOURCE_TYPE_CUSTOM,
   * SOURCE_TYPE_DISCOVERY_CLIENT
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
  /**
   * Output only. The timestamp when the source was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Source::class, 'Google_Service_MigrationCenterAPI_Source');

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

namespace Google\Service\Games;

class Snapshot extends \Google\Model
{
  /**
   * A snapshot representing a save game.
   */
  public const TYPE_SAVE_GAME = 'SAVE_GAME';
  protected $coverImageType = SnapshotImage::class;
  protected $coverImageDataType = '';
  /**
   * The description of this snapshot.
   *
   * @var string
   */
  public $description;
  /**
   * The ID of the file underlying this snapshot in the Drive API. Only present
   * if the snapshot is a view on a Drive file and the file is owned by the
   * caller.
   *
   * @var string
   */
  public $driveId;
  /**
   * The duration associated with this snapshot, in millis.
   *
   * @var string
   */
  public $durationMillis;
  /**
   * The ID of the snapshot.
   *
   * @var string
   */
  public $id;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#snapshot`.
   *
   * @var string
   */
  public $kind;
  /**
   * The timestamp (in millis since Unix epoch) of the last modification to this
   * snapshot.
   *
   * @var string
   */
  public $lastModifiedMillis;
  /**
   * The progress value (64-bit integer set by developer) associated with this
   * snapshot.
   *
   * @var string
   */
  public $progressValue;
  /**
   * The title of this snapshot.
   *
   * @var string
   */
  public $title;
  /**
   * The type of this snapshot.
   *
   * @var string
   */
  public $type;
  /**
   * The unique name provided when the snapshot was created.
   *
   * @var string
   */
  public $uniqueName;

  /**
   * The cover image of this snapshot. May be absent if there is no image.
   *
   * @param SnapshotImage $coverImage
   */
  public function setCoverImage(SnapshotImage $coverImage)
  {
    $this->coverImage = $coverImage;
  }
  /**
   * @return SnapshotImage
   */
  public function getCoverImage()
  {
    return $this->coverImage;
  }
  /**
   * The description of this snapshot.
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
   * The ID of the file underlying this snapshot in the Drive API. Only present
   * if the snapshot is a view on a Drive file and the file is owned by the
   * caller.
   *
   * @param string $driveId
   */
  public function setDriveId($driveId)
  {
    $this->driveId = $driveId;
  }
  /**
   * @return string
   */
  public function getDriveId()
  {
    return $this->driveId;
  }
  /**
   * The duration associated with this snapshot, in millis.
   *
   * @param string $durationMillis
   */
  public function setDurationMillis($durationMillis)
  {
    $this->durationMillis = $durationMillis;
  }
  /**
   * @return string
   */
  public function getDurationMillis()
  {
    return $this->durationMillis;
  }
  /**
   * The ID of the snapshot.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#snapshot`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The timestamp (in millis since Unix epoch) of the last modification to this
   * snapshot.
   *
   * @param string $lastModifiedMillis
   */
  public function setLastModifiedMillis($lastModifiedMillis)
  {
    $this->lastModifiedMillis = $lastModifiedMillis;
  }
  /**
   * @return string
   */
  public function getLastModifiedMillis()
  {
    return $this->lastModifiedMillis;
  }
  /**
   * The progress value (64-bit integer set by developer) associated with this
   * snapshot.
   *
   * @param string $progressValue
   */
  public function setProgressValue($progressValue)
  {
    $this->progressValue = $progressValue;
  }
  /**
   * @return string
   */
  public function getProgressValue()
  {
    return $this->progressValue;
  }
  /**
   * The title of this snapshot.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The type of this snapshot.
   *
   * Accepted values: SAVE_GAME
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
   * The unique name provided when the snapshot was created.
   *
   * @param string $uniqueName
   */
  public function setUniqueName($uniqueName)
  {
    $this->uniqueName = $uniqueName;
  }
  /**
   * @return string
   */
  public function getUniqueName()
  {
    return $this->uniqueName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Snapshot::class, 'Google_Service_Games_Snapshot');

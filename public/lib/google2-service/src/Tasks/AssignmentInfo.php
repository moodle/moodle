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

namespace Google\Service\Tasks;

class AssignmentInfo extends \Google\Model
{
  /**
   * Unknown value for this task's context.
   */
  public const SURFACE_TYPE_CONTEXT_TYPE_UNSPECIFIED = 'CONTEXT_TYPE_UNSPECIFIED';
  /**
   * The task is created from Gmail.
   */
  public const SURFACE_TYPE_GMAIL = 'GMAIL';
  /**
   * The task is assigned from a document.
   */
  public const SURFACE_TYPE_DOCUMENT = 'DOCUMENT';
  /**
   * The task is assigned from a Chat Space.
   */
  public const SURFACE_TYPE_SPACE = 'SPACE';
  protected $driveResourceInfoType = DriveResourceInfo::class;
  protected $driveResourceInfoDataType = '';
  /**
   * Output only. An absolute link to the original task in the surface of
   * assignment (Docs, Chat spaces, etc.).
   *
   * @var string
   */
  public $linkToTask;
  protected $spaceInfoType = SpaceInfo::class;
  protected $spaceInfoDataType = '';
  /**
   * Output only. The type of surface this assigned task originates from.
   * Currently limited to DOCUMENT or SPACE.
   *
   * @var string
   */
  public $surfaceType;

  /**
   * Output only. Information about the Drive file where this task originates
   * from. Currently, the Drive file can only be a document. This field is read-
   * only.
   *
   * @param DriveResourceInfo $driveResourceInfo
   */
  public function setDriveResourceInfo(DriveResourceInfo $driveResourceInfo)
  {
    $this->driveResourceInfo = $driveResourceInfo;
  }
  /**
   * @return DriveResourceInfo
   */
  public function getDriveResourceInfo()
  {
    return $this->driveResourceInfo;
  }
  /**
   * Output only. An absolute link to the original task in the surface of
   * assignment (Docs, Chat spaces, etc.).
   *
   * @param string $linkToTask
   */
  public function setLinkToTask($linkToTask)
  {
    $this->linkToTask = $linkToTask;
  }
  /**
   * @return string
   */
  public function getLinkToTask()
  {
    return $this->linkToTask;
  }
  /**
   * Output only. Information about the Chat Space where this task originates
   * from. This field is read-only.
   *
   * @param SpaceInfo $spaceInfo
   */
  public function setSpaceInfo(SpaceInfo $spaceInfo)
  {
    $this->spaceInfo = $spaceInfo;
  }
  /**
   * @return SpaceInfo
   */
  public function getSpaceInfo()
  {
    return $this->spaceInfo;
  }
  /**
   * Output only. The type of surface this assigned task originates from.
   * Currently limited to DOCUMENT or SPACE.
   *
   * Accepted values: CONTEXT_TYPE_UNSPECIFIED, GMAIL, DOCUMENT, SPACE
   *
   * @param self::SURFACE_TYPE_* $surfaceType
   */
  public function setSurfaceType($surfaceType)
  {
    $this->surfaceType = $surfaceType;
  }
  /**
   * @return self::SURFACE_TYPE_*
   */
  public function getSurfaceType()
  {
    return $this->surfaceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssignmentInfo::class, 'Google_Service_Tasks_AssignmentInfo');

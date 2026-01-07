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

namespace Google\Service\Classroom;

class Rubric extends \Google\Collection
{
  protected $collection_key = 'criteria';
  /**
   * Identifier of the course. Read-only.
   *
   * @var string
   */
  public $courseId;
  /**
   * Identifier for the course work this corresponds to. Read-only.
   *
   * @var string
   */
  public $courseWorkId;
  /**
   * Output only. Timestamp when this rubric was created. Read-only.
   *
   * @var string
   */
  public $creationTime;
  protected $criteriaType = Criterion::class;
  protected $criteriaDataType = 'array';
  /**
   * Classroom-assigned identifier for the rubric. This is unique among rubrics
   * for the relevant course work. Read-only.
   *
   * @var string
   */
  public $id;
  /**
   * Input only. Immutable. Google Sheets ID of the spreadsheet. This
   * spreadsheet must contain formatted rubric settings. See [Create or reuse a
   * rubric for an
   * assignment](https://support.google.com/edu/classroom/answer/9335069). Use
   * of this field requires the
   * `https://www.googleapis.com/auth/spreadsheets.readonly` or
   * `https://www.googleapis.com/auth/spreadsheets` scope.
   *
   * @var string
   */
  public $sourceSpreadsheetId;
  /**
   * Output only. Timestamp of the most recent change to this rubric. Read-only.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Identifier of the course. Read-only.
   *
   * @param string $courseId
   */
  public function setCourseId($courseId)
  {
    $this->courseId = $courseId;
  }
  /**
   * @return string
   */
  public function getCourseId()
  {
    return $this->courseId;
  }
  /**
   * Identifier for the course work this corresponds to. Read-only.
   *
   * @param string $courseWorkId
   */
  public function setCourseWorkId($courseWorkId)
  {
    $this->courseWorkId = $courseWorkId;
  }
  /**
   * @return string
   */
  public function getCourseWorkId()
  {
    return $this->courseWorkId;
  }
  /**
   * Output only. Timestamp when this rubric was created. Read-only.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * List of criteria. Each criterion is a dimension on which performance is
   * rated.
   *
   * @param Criterion[] $criteria
   */
  public function setCriteria($criteria)
  {
    $this->criteria = $criteria;
  }
  /**
   * @return Criterion[]
   */
  public function getCriteria()
  {
    return $this->criteria;
  }
  /**
   * Classroom-assigned identifier for the rubric. This is unique among rubrics
   * for the relevant course work. Read-only.
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
   * Input only. Immutable. Google Sheets ID of the spreadsheet. This
   * spreadsheet must contain formatted rubric settings. See [Create or reuse a
   * rubric for an
   * assignment](https://support.google.com/edu/classroom/answer/9335069). Use
   * of this field requires the
   * `https://www.googleapis.com/auth/spreadsheets.readonly` or
   * `https://www.googleapis.com/auth/spreadsheets` scope.
   *
   * @param string $sourceSpreadsheetId
   */
  public function setSourceSpreadsheetId($sourceSpreadsheetId)
  {
    $this->sourceSpreadsheetId = $sourceSpreadsheetId;
  }
  /**
   * @return string
   */
  public function getSourceSpreadsheetId()
  {
    return $this->sourceSpreadsheetId;
  }
  /**
   * Output only. Timestamp of the most recent change to this rubric. Read-only.
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
class_alias(Rubric::class, 'Google_Service_Classroom_Rubric');

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

namespace Google\Service\Sheets;

class ProtectedRange extends \Google\Collection
{
  protected $collection_key = 'unprotectedRanges';
  /**
   * The description of this protected range.
   *
   * @var string
   */
  public $description;
  protected $editorsType = Editors::class;
  protected $editorsDataType = '';
  /**
   * The named range this protected range is backed by, if any. When writing,
   * only one of range or named_range_id or table_id may be set.
   *
   * @var string
   */
  public $namedRangeId;
  /**
   * The ID of the protected range. This field is read-only.
   *
   * @var int
   */
  public $protectedRangeId;
  protected $rangeType = GridRange::class;
  protected $rangeDataType = '';
  /**
   * True if the user who requested this protected range can edit the protected
   * area. This field is read-only.
   *
   * @var bool
   */
  public $requestingUserCanEdit;
  /**
   * The table this protected range is backed by, if any. When writing, only one
   * of range or named_range_id or table_id may be set.
   *
   * @var string
   */
  public $tableId;
  protected $unprotectedRangesType = GridRange::class;
  protected $unprotectedRangesDataType = 'array';
  /**
   * True if this protected range will show a warning when editing. Warning-
   * based protection means that every user can edit data in the protected
   * range, except editing will prompt a warning asking the user to confirm the
   * edit. When writing: if this field is true, then editors are ignored.
   * Additionally, if this field is changed from true to false and the `editors`
   * field is not set (nor included in the field mask), then the editors will be
   * set to all the editors in the document.
   *
   * @var bool
   */
  public $warningOnly;

  /**
   * The description of this protected range.
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
   * The users and groups with edit access to the protected range. This field is
   * only visible to users with edit access to the protected range and the
   * document. Editors are not supported with warning_only protection.
   *
   * @param Editors $editors
   */
  public function setEditors(Editors $editors)
  {
    $this->editors = $editors;
  }
  /**
   * @return Editors
   */
  public function getEditors()
  {
    return $this->editors;
  }
  /**
   * The named range this protected range is backed by, if any. When writing,
   * only one of range or named_range_id or table_id may be set.
   *
   * @param string $namedRangeId
   */
  public function setNamedRangeId($namedRangeId)
  {
    $this->namedRangeId = $namedRangeId;
  }
  /**
   * @return string
   */
  public function getNamedRangeId()
  {
    return $this->namedRangeId;
  }
  /**
   * The ID of the protected range. This field is read-only.
   *
   * @param int $protectedRangeId
   */
  public function setProtectedRangeId($protectedRangeId)
  {
    $this->protectedRangeId = $protectedRangeId;
  }
  /**
   * @return int
   */
  public function getProtectedRangeId()
  {
    return $this->protectedRangeId;
  }
  /**
   * The range that is being protected. The range may be fully unbounded, in
   * which case this is considered a protected sheet. When writing, only one of
   * range or named_range_id or table_id may be set.
   *
   * @param GridRange $range
   */
  public function setRange(GridRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return GridRange
   */
  public function getRange()
  {
    return $this->range;
  }
  /**
   * True if the user who requested this protected range can edit the protected
   * area. This field is read-only.
   *
   * @param bool $requestingUserCanEdit
   */
  public function setRequestingUserCanEdit($requestingUserCanEdit)
  {
    $this->requestingUserCanEdit = $requestingUserCanEdit;
  }
  /**
   * @return bool
   */
  public function getRequestingUserCanEdit()
  {
    return $this->requestingUserCanEdit;
  }
  /**
   * The table this protected range is backed by, if any. When writing, only one
   * of range or named_range_id or table_id may be set.
   *
   * @param string $tableId
   */
  public function setTableId($tableId)
  {
    $this->tableId = $tableId;
  }
  /**
   * @return string
   */
  public function getTableId()
  {
    return $this->tableId;
  }
  /**
   * The list of unprotected ranges within a protected sheet. Unprotected ranges
   * are only supported on protected sheets.
   *
   * @param GridRange[] $unprotectedRanges
   */
  public function setUnprotectedRanges($unprotectedRanges)
  {
    $this->unprotectedRanges = $unprotectedRanges;
  }
  /**
   * @return GridRange[]
   */
  public function getUnprotectedRanges()
  {
    return $this->unprotectedRanges;
  }
  /**
   * True if this protected range will show a warning when editing. Warning-
   * based protection means that every user can edit data in the protected
   * range, except editing will prompt a warning asking the user to confirm the
   * edit. When writing: if this field is true, then editors are ignored.
   * Additionally, if this field is changed from true to false and the `editors`
   * field is not set (nor included in the field mask), then the editors will be
   * set to all the editors in the document.
   *
   * @param bool $warningOnly
   */
  public function setWarningOnly($warningOnly)
  {
    $this->warningOnly = $warningOnly;
  }
  /**
   * @return bool
   */
  public function getWarningOnly()
  {
    return $this->warningOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProtectedRange::class, 'Google_Service_Sheets_ProtectedRange');

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

class EmbeddedObjectPosition extends \Google\Model
{
  /**
   * If true, the embedded object is put on a new sheet whose ID is chosen for
   * you. Used only when writing.
   *
   * @var bool
   */
  public $newSheet;
  protected $overlayPositionType = OverlayPosition::class;
  protected $overlayPositionDataType = '';
  /**
   * The sheet this is on. Set only if the embedded object is on its own sheet.
   * Must be non-negative.
   *
   * @var int
   */
  public $sheetId;

  /**
   * If true, the embedded object is put on a new sheet whose ID is chosen for
   * you. Used only when writing.
   *
   * @param bool $newSheet
   */
  public function setNewSheet($newSheet)
  {
    $this->newSheet = $newSheet;
  }
  /**
   * @return bool
   */
  public function getNewSheet()
  {
    return $this->newSheet;
  }
  /**
   * The position at which the object is overlaid on top of a grid.
   *
   * @param OverlayPosition $overlayPosition
   */
  public function setOverlayPosition(OverlayPosition $overlayPosition)
  {
    $this->overlayPosition = $overlayPosition;
  }
  /**
   * @return OverlayPosition
   */
  public function getOverlayPosition()
  {
    return $this->overlayPosition;
  }
  /**
   * The sheet this is on. Set only if the embedded object is on its own sheet.
   * Must be non-negative.
   *
   * @param int $sheetId
   */
  public function setSheetId($sheetId)
  {
    $this->sheetId = $sheetId;
  }
  /**
   * @return int
   */
  public function getSheetId()
  {
    return $this->sheetId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EmbeddedObjectPosition::class, 'Google_Service_Sheets_EmbeddedObjectPosition');

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

class ChipRun extends \Google\Model
{
  protected $chipType = Chip::class;
  protected $chipDataType = '';
  /**
   * Required. The zero-based character index where this run starts, in UTF-16
   * code units.
   *
   * @var int
   */
  public $startIndex;

  /**
   * Optional. The chip of this run.
   *
   * @param Chip $chip
   */
  public function setChip(Chip $chip)
  {
    $this->chip = $chip;
  }
  /**
   * @return Chip
   */
  public function getChip()
  {
    return $this->chip;
  }
  /**
   * Required. The zero-based character index where this run starts, in UTF-16
   * code units.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChipRun::class, 'Google_Service_Sheets_ChipRun');

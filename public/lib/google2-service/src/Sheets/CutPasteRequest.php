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

class CutPasteRequest extends \Google\Model
{
  /**
   * Paste values, formulas, formats, and merges.
   */
  public const PASTE_TYPE_PASTE_NORMAL = 'PASTE_NORMAL';
  /**
   * Paste the values ONLY without formats, formulas, or merges.
   */
  public const PASTE_TYPE_PASTE_VALUES = 'PASTE_VALUES';
  /**
   * Paste the format and data validation only.
   */
  public const PASTE_TYPE_PASTE_FORMAT = 'PASTE_FORMAT';
  /**
   * Like `PASTE_NORMAL` but without borders.
   */
  public const PASTE_TYPE_PASTE_NO_BORDERS = 'PASTE_NO_BORDERS';
  /**
   * Paste the formulas only.
   */
  public const PASTE_TYPE_PASTE_FORMULA = 'PASTE_FORMULA';
  /**
   * Paste the data validation only.
   */
  public const PASTE_TYPE_PASTE_DATA_VALIDATION = 'PASTE_DATA_VALIDATION';
  /**
   * Paste the conditional formatting rules only.
   */
  public const PASTE_TYPE_PASTE_CONDITIONAL_FORMATTING = 'PASTE_CONDITIONAL_FORMATTING';
  protected $destinationType = GridCoordinate::class;
  protected $destinationDataType = '';
  /**
   * What kind of data to paste. All the source data will be cut, regardless of
   * what is pasted.
   *
   * @var string
   */
  public $pasteType;
  protected $sourceType = GridRange::class;
  protected $sourceDataType = '';

  /**
   * The top-left coordinate where the data should be pasted.
   *
   * @param GridCoordinate $destination
   */
  public function setDestination(GridCoordinate $destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return GridCoordinate
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * What kind of data to paste. All the source data will be cut, regardless of
   * what is pasted.
   *
   * Accepted values: PASTE_NORMAL, PASTE_VALUES, PASTE_FORMAT,
   * PASTE_NO_BORDERS, PASTE_FORMULA, PASTE_DATA_VALIDATION,
   * PASTE_CONDITIONAL_FORMATTING
   *
   * @param self::PASTE_TYPE_* $pasteType
   */
  public function setPasteType($pasteType)
  {
    $this->pasteType = $pasteType;
  }
  /**
   * @return self::PASTE_TYPE_*
   */
  public function getPasteType()
  {
    return $this->pasteType;
  }
  /**
   * The source data to cut.
   *
   * @param GridRange $source
   */
  public function setSource(GridRange $source)
  {
    $this->source = $source;
  }
  /**
   * @return GridRange
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CutPasteRequest::class, 'Google_Service_Sheets_CutPasteRequest');

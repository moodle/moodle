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

class PasteDataRequest extends \Google\Model
{
  /**
   * Paste values, formulas, formats, and merges.
   */
  public const TYPE_PASTE_NORMAL = 'PASTE_NORMAL';
  /**
   * Paste the values ONLY without formats, formulas, or merges.
   */
  public const TYPE_PASTE_VALUES = 'PASTE_VALUES';
  /**
   * Paste the format and data validation only.
   */
  public const TYPE_PASTE_FORMAT = 'PASTE_FORMAT';
  /**
   * Like `PASTE_NORMAL` but without borders.
   */
  public const TYPE_PASTE_NO_BORDERS = 'PASTE_NO_BORDERS';
  /**
   * Paste the formulas only.
   */
  public const TYPE_PASTE_FORMULA = 'PASTE_FORMULA';
  /**
   * Paste the data validation only.
   */
  public const TYPE_PASTE_DATA_VALIDATION = 'PASTE_DATA_VALIDATION';
  /**
   * Paste the conditional formatting rules only.
   */
  public const TYPE_PASTE_CONDITIONAL_FORMATTING = 'PASTE_CONDITIONAL_FORMATTING';
  protected $coordinateType = GridCoordinate::class;
  protected $coordinateDataType = '';
  /**
   * The data to insert.
   *
   * @var string
   */
  public $data;
  /**
   * The delimiter in the data.
   *
   * @var string
   */
  public $delimiter;
  /**
   * True if the data is HTML.
   *
   * @var bool
   */
  public $html;
  /**
   * How the data should be pasted.
   *
   * @var string
   */
  public $type;

  /**
   * The coordinate at which the data should start being inserted.
   *
   * @param GridCoordinate $coordinate
   */
  public function setCoordinate(GridCoordinate $coordinate)
  {
    $this->coordinate = $coordinate;
  }
  /**
   * @return GridCoordinate
   */
  public function getCoordinate()
  {
    return $this->coordinate;
  }
  /**
   * The data to insert.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * The delimiter in the data.
   *
   * @param string $delimiter
   */
  public function setDelimiter($delimiter)
  {
    $this->delimiter = $delimiter;
  }
  /**
   * @return string
   */
  public function getDelimiter()
  {
    return $this->delimiter;
  }
  /**
   * True if the data is HTML.
   *
   * @param bool $html
   */
  public function setHtml($html)
  {
    $this->html = $html;
  }
  /**
   * @return bool
   */
  public function getHtml()
  {
    return $this->html;
  }
  /**
   * How the data should be pasted.
   *
   * Accepted values: PASTE_NORMAL, PASTE_VALUES, PASTE_FORMAT,
   * PASTE_NO_BORDERS, PASTE_FORMULA, PASTE_DATA_VALIDATION,
   * PASTE_CONDITIONAL_FORMATTING
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
class_alias(PasteDataRequest::class, 'Google_Service_Sheets_PasteDataRequest');

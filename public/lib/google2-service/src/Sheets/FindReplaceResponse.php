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

class FindReplaceResponse extends \Google\Model
{
  /**
   * The number of formula cells changed.
   *
   * @var int
   */
  public $formulasChanged;
  /**
   * The number of occurrences (possibly multiple within a cell) changed. For
   * example, if replacing `"e"` with `"o"` in `"Google Sheets"`, this would be
   * `"3"` because `"Google Sheets"` -> `"Googlo Shoots"`.
   *
   * @var int
   */
  public $occurrencesChanged;
  /**
   * The number of rows changed.
   *
   * @var int
   */
  public $rowsChanged;
  /**
   * The number of sheets changed.
   *
   * @var int
   */
  public $sheetsChanged;
  /**
   * The number of non-formula cells changed.
   *
   * @var int
   */
  public $valuesChanged;

  /**
   * The number of formula cells changed.
   *
   * @param int $formulasChanged
   */
  public function setFormulasChanged($formulasChanged)
  {
    $this->formulasChanged = $formulasChanged;
  }
  /**
   * @return int
   */
  public function getFormulasChanged()
  {
    return $this->formulasChanged;
  }
  /**
   * The number of occurrences (possibly multiple within a cell) changed. For
   * example, if replacing `"e"` with `"o"` in `"Google Sheets"`, this would be
   * `"3"` because `"Google Sheets"` -> `"Googlo Shoots"`.
   *
   * @param int $occurrencesChanged
   */
  public function setOccurrencesChanged($occurrencesChanged)
  {
    $this->occurrencesChanged = $occurrencesChanged;
  }
  /**
   * @return int
   */
  public function getOccurrencesChanged()
  {
    return $this->occurrencesChanged;
  }
  /**
   * The number of rows changed.
   *
   * @param int $rowsChanged
   */
  public function setRowsChanged($rowsChanged)
  {
    $this->rowsChanged = $rowsChanged;
  }
  /**
   * @return int
   */
  public function getRowsChanged()
  {
    return $this->rowsChanged;
  }
  /**
   * The number of sheets changed.
   *
   * @param int $sheetsChanged
   */
  public function setSheetsChanged($sheetsChanged)
  {
    $this->sheetsChanged = $sheetsChanged;
  }
  /**
   * @return int
   */
  public function getSheetsChanged()
  {
    return $this->sheetsChanged;
  }
  /**
   * The number of non-formula cells changed.
   *
   * @param int $valuesChanged
   */
  public function setValuesChanged($valuesChanged)
  {
    $this->valuesChanged = $valuesChanged;
  }
  /**
   * @return int
   */
  public function getValuesChanged()
  {
    return $this->valuesChanged;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FindReplaceResponse::class, 'Google_Service_Sheets_FindReplaceResponse');

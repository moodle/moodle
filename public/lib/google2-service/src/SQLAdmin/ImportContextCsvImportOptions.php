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

namespace Google\Service\SQLAdmin;

class ImportContextCsvImportOptions extends \Google\Collection
{
  protected $collection_key = 'columns';
  /**
   * The columns to which CSV data is imported. If not specified, all columns of
   * the database table are loaded with CSV data.
   *
   * @var string[]
   */
  public $columns;
  /**
   * Specifies the character that should appear before a data character that
   * needs to be escaped.
   *
   * @var string
   */
  public $escapeCharacter;
  /**
   * Specifies the character that separates columns within each row (line) of
   * the file.
   *
   * @var string
   */
  public $fieldsTerminatedBy;
  /**
   * This is used to separate lines. If a line does not contain all fields, the
   * rest of the columns are set to their default values.
   *
   * @var string
   */
  public $linesTerminatedBy;
  /**
   * Specifies the quoting character to be used when a data value is quoted.
   *
   * @var string
   */
  public $quoteCharacter;
  /**
   * The table to which CSV data is imported.
   *
   * @var string
   */
  public $table;

  /**
   * The columns to which CSV data is imported. If not specified, all columns of
   * the database table are loaded with CSV data.
   *
   * @param string[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return string[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * Specifies the character that should appear before a data character that
   * needs to be escaped.
   *
   * @param string $escapeCharacter
   */
  public function setEscapeCharacter($escapeCharacter)
  {
    $this->escapeCharacter = $escapeCharacter;
  }
  /**
   * @return string
   */
  public function getEscapeCharacter()
  {
    return $this->escapeCharacter;
  }
  /**
   * Specifies the character that separates columns within each row (line) of
   * the file.
   *
   * @param string $fieldsTerminatedBy
   */
  public function setFieldsTerminatedBy($fieldsTerminatedBy)
  {
    $this->fieldsTerminatedBy = $fieldsTerminatedBy;
  }
  /**
   * @return string
   */
  public function getFieldsTerminatedBy()
  {
    return $this->fieldsTerminatedBy;
  }
  /**
   * This is used to separate lines. If a line does not contain all fields, the
   * rest of the columns are set to their default values.
   *
   * @param string $linesTerminatedBy
   */
  public function setLinesTerminatedBy($linesTerminatedBy)
  {
    $this->linesTerminatedBy = $linesTerminatedBy;
  }
  /**
   * @return string
   */
  public function getLinesTerminatedBy()
  {
    return $this->linesTerminatedBy;
  }
  /**
   * Specifies the quoting character to be used when a data value is quoted.
   *
   * @param string $quoteCharacter
   */
  public function setQuoteCharacter($quoteCharacter)
  {
    $this->quoteCharacter = $quoteCharacter;
  }
  /**
   * @return string
   */
  public function getQuoteCharacter()
  {
    return $this->quoteCharacter;
  }
  /**
   * The table to which CSV data is imported.
   *
   * @param string $table
   */
  public function setTable($table)
  {
    $this->table = $table;
  }
  /**
   * @return string
   */
  public function getTable()
  {
    return $this->table;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportContextCsvImportOptions::class, 'Google_Service_SQLAdmin_ImportContextCsvImportOptions');

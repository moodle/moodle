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

namespace Google\Service\CloudAlloyDBAdmin;

class CsvImportOptions extends \Google\Collection
{
  protected $collection_key = 'columns';
  /**
   * Optional. The columns to which CSV data is imported. If not specified, all
   * columns of the database table are loaded with CSV data.
   *
   * @var string[]
   */
  public $columns;
  /**
   * Optional. Specifies the character that should appear before a data
   * character that needs to be escaped. The default is same as quote character.
   * The value of this argument has to be a character in Hex ASCII Code.
   *
   * @var string
   */
  public $escapeCharacter;
  /**
   * Optional. Specifies the character that separates columns within each row
   * (line) of the file. The default is comma. The value of this argument has to
   * be a character in Hex ASCII Code.
   *
   * @var string
   */
  public $fieldDelimiter;
  /**
   * Optional. Specifies the quoting character to be used when a data value is
   * quoted. The default is double-quote. The value of this argument has to be a
   * character in Hex ASCII Code.
   *
   * @var string
   */
  public $quoteCharacter;
  /**
   * Required. The database table to import CSV file into.
   *
   * @var string
   */
  public $table;

  /**
   * Optional. The columns to which CSV data is imported. If not specified, all
   * columns of the database table are loaded with CSV data.
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
   * Optional. Specifies the character that should appear before a data
   * character that needs to be escaped. The default is same as quote character.
   * The value of this argument has to be a character in Hex ASCII Code.
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
   * Optional. Specifies the character that separates columns within each row
   * (line) of the file. The default is comma. The value of this argument has to
   * be a character in Hex ASCII Code.
   *
   * @param string $fieldDelimiter
   */
  public function setFieldDelimiter($fieldDelimiter)
  {
    $this->fieldDelimiter = $fieldDelimiter;
  }
  /**
   * @return string
   */
  public function getFieldDelimiter()
  {
    return $this->fieldDelimiter;
  }
  /**
   * Optional. Specifies the quoting character to be used when a data value is
   * quoted. The default is double-quote. The value of this argument has to be a
   * character in Hex ASCII Code.
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
   * Required. The database table to import CSV file into.
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
class_alias(CsvImportOptions::class, 'Google_Service_CloudAlloyDBAdmin_CsvImportOptions');

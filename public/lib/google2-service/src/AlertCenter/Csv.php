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

namespace Google\Service\AlertCenter;

class Csv extends \Google\Collection
{
  protected $collection_key = 'headers';
  protected $dataRowsType = CsvRow::class;
  protected $dataRowsDataType = 'array';
  /**
   * The list of headers for data columns in a CSV file.
   *
   * @var string[]
   */
  public $headers;

  /**
   * The list of data rows in a CSV file, as string arrays rather than as a
   * single comma-separated string.
   *
   * @param CsvRow[] $dataRows
   */
  public function setDataRows($dataRows)
  {
    $this->dataRows = $dataRows;
  }
  /**
   * @return CsvRow[]
   */
  public function getDataRows()
  {
    return $this->dataRows;
  }
  /**
   * The list of headers for data columns in a CSV file.
   *
   * @param string[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return string[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Csv::class, 'Google_Service_AlertCenter_Csv');

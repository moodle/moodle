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

class DataSource extends \Google\Collection
{
  protected $collection_key = 'calculatedColumns';
  protected $calculatedColumnsType = DataSourceColumn::class;
  protected $calculatedColumnsDataType = 'array';
  /**
   * The spreadsheet-scoped unique ID that identifies the data source. Example:
   * 1080547365.
   *
   * @var string
   */
  public $dataSourceId;
  /**
   * The ID of the Sheet connected with the data source. The field cannot be
   * changed once set. When creating a data source, an associated DATA_SOURCE
   * sheet is also created, if the field is not specified, the ID of the created
   * sheet will be randomly generated.
   *
   * @var int
   */
  public $sheetId;
  protected $specType = DataSourceSpec::class;
  protected $specDataType = '';

  /**
   * All calculated columns in the data source.
   *
   * @param DataSourceColumn[] $calculatedColumns
   */
  public function setCalculatedColumns($calculatedColumns)
  {
    $this->calculatedColumns = $calculatedColumns;
  }
  /**
   * @return DataSourceColumn[]
   */
  public function getCalculatedColumns()
  {
    return $this->calculatedColumns;
  }
  /**
   * The spreadsheet-scoped unique ID that identifies the data source. Example:
   * 1080547365.
   *
   * @param string $dataSourceId
   */
  public function setDataSourceId($dataSourceId)
  {
    $this->dataSourceId = $dataSourceId;
  }
  /**
   * @return string
   */
  public function getDataSourceId()
  {
    return $this->dataSourceId;
  }
  /**
   * The ID of the Sheet connected with the data source. The field cannot be
   * changed once set. When creating a data source, an associated DATA_SOURCE
   * sheet is also created, if the field is not specified, the ID of the created
   * sheet will be randomly generated.
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
  /**
   * The DataSourceSpec for the data source connected with this spreadsheet.
   *
   * @param DataSourceSpec $spec
   */
  public function setSpec(DataSourceSpec $spec)
  {
    $this->spec = $spec;
  }
  /**
   * @return DataSourceSpec
   */
  public function getSpec()
  {
    return $this->spec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSource::class, 'Google_Service_Sheets_DataSource');

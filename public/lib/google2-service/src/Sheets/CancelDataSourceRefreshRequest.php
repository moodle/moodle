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

class CancelDataSourceRefreshRequest extends \Google\Model
{
  /**
   * Reference to a DataSource. If specified, cancels all associated data source
   * object refreshes for this data source.
   *
   * @var string
   */
  public $dataSourceId;
  /**
   * Cancels all existing data source object refreshes for all data sources in
   * the spreadsheet.
   *
   * @var bool
   */
  public $isAll;
  protected $referencesType = DataSourceObjectReferences::class;
  protected $referencesDataType = '';

  /**
   * Reference to a DataSource. If specified, cancels all associated data source
   * object refreshes for this data source.
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
   * Cancels all existing data source object refreshes for all data sources in
   * the spreadsheet.
   *
   * @param bool $isAll
   */
  public function setIsAll($isAll)
  {
    $this->isAll = $isAll;
  }
  /**
   * @return bool
   */
  public function getIsAll()
  {
    return $this->isAll;
  }
  /**
   * References to data source objects whose refreshes are to be cancelled.
   *
   * @param DataSourceObjectReferences $references
   */
  public function setReferences(DataSourceObjectReferences $references)
  {
    $this->references = $references;
  }
  /**
   * @return DataSourceObjectReferences
   */
  public function getReferences()
  {
    return $this->references;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CancelDataSourceRefreshRequest::class, 'Google_Service_Sheets_CancelDataSourceRefreshRequest');

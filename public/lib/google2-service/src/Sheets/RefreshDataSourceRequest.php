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

class RefreshDataSourceRequest extends \Google\Model
{
  /**
   * Reference to a DataSource. If specified, refreshes all associated data
   * source objects for the data source.
   *
   * @var string
   */
  public $dataSourceId;
  /**
   * Refreshes the data source objects regardless of the current state. If not
   * set and a referenced data source object was in error state, the refresh
   * will fail immediately.
   *
   * @var bool
   */
  public $force;
  /**
   * Refreshes all existing data source objects in the spreadsheet.
   *
   * @var bool
   */
  public $isAll;
  protected $referencesType = DataSourceObjectReferences::class;
  protected $referencesDataType = '';

  /**
   * Reference to a DataSource. If specified, refreshes all associated data
   * source objects for the data source.
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
   * Refreshes the data source objects regardless of the current state. If not
   * set and a referenced data source object was in error state, the refresh
   * will fail immediately.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
  /**
   * Refreshes all existing data source objects in the spreadsheet.
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
   * References to data source objects to refresh.
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
class_alias(RefreshDataSourceRequest::class, 'Google_Service_Sheets_RefreshDataSourceRequest');

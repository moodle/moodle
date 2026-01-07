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

namespace Google\Service\Area120Tables;

class Table extends \Google\Collection
{
  protected $collection_key = 'savedViews';
  protected $columnsType = ColumnDescription::class;
  protected $columnsDataType = 'array';
  /**
   * Time when the table was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The human readable title of the table.
   *
   * @var string
   */
  public $displayName;
  /**
   * The resource name of the table. Table names have the form `tables/{table}`.
   *
   * @var string
   */
  public $name;
  protected $savedViewsType = SavedView::class;
  protected $savedViewsDataType = 'array';
  /**
   * The time zone of the table. IANA Time Zone Database time zone, e.g.
   * "America/New_York".
   *
   * @var string
   */
  public $timeZone;
  /**
   * Time when the table was last updated excluding updates to individual rows
   *
   * @var string
   */
  public $updateTime;

  /**
   * List of columns in this table. Order of columns matches the display order.
   *
   * @param ColumnDescription[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return ColumnDescription[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * Time when the table was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The human readable title of the table.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The resource name of the table. Table names have the form `tables/{table}`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Saved views for this table.
   *
   * @param SavedView[] $savedViews
   */
  public function setSavedViews($savedViews)
  {
    $this->savedViews = $savedViews;
  }
  /**
   * @return SavedView[]
   */
  public function getSavedViews()
  {
    return $this->savedViews;
  }
  /**
   * The time zone of the table. IANA Time Zone Database time zone, e.g.
   * "America/New_York".
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * Time when the table was last updated excluding updates to individual rows
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Table::class, 'Google_Service_Area120Tables_Table');

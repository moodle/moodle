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

namespace Google\Service\Docs;

class InsertTableRequest extends \Google\Model
{
  /**
   * The number of columns in the table.
   *
   * @var int
   */
  public $columns;
  protected $endOfSegmentLocationType = EndOfSegmentLocation::class;
  protected $endOfSegmentLocationDataType = '';
  protected $locationType = Location::class;
  protected $locationDataType = '';
  /**
   * The number of rows in the table.
   *
   * @var int
   */
  public $rows;

  /**
   * The number of columns in the table.
   *
   * @param int $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return int
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * Inserts the table at the end of the given header, footer or document body.
   * A newline character will be inserted before the inserted table. Tables
   * cannot be inserted inside a footnote.
   *
   * @param EndOfSegmentLocation $endOfSegmentLocation
   */
  public function setEndOfSegmentLocation(EndOfSegmentLocation $endOfSegmentLocation)
  {
    $this->endOfSegmentLocation = $endOfSegmentLocation;
  }
  /**
   * @return EndOfSegmentLocation
   */
  public function getEndOfSegmentLocation()
  {
    return $this->endOfSegmentLocation;
  }
  /**
   * Inserts the table at a specific model index. A newline character will be
   * inserted before the inserted table, therefore the table start index will be
   * at the specified location index + 1. The table must be inserted inside the
   * bounds of an existing Paragraph. For instance, it cannot be inserted at a
   * table's start index (i.e. between an existing table and its preceding
   * paragraph). Tables cannot be inserted inside a footnote or equation.
   *
   * @param Location $location
   */
  public function setLocation(Location $location)
  {
    $this->location = $location;
  }
  /**
   * @return Location
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The number of rows in the table.
   *
   * @param int $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return int
   */
  public function getRows()
  {
    return $this->rows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsertTableRequest::class, 'Google_Service_Docs_InsertTableRequest');

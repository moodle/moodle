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

class PinTableHeaderRowsRequest extends \Google\Model
{
  /**
   * The number of table rows to pin, where 0 implies that all rows are
   * unpinned.
   *
   * @var int
   */
  public $pinnedHeaderRowsCount;
  protected $tableStartLocationType = Location::class;
  protected $tableStartLocationDataType = '';

  /**
   * The number of table rows to pin, where 0 implies that all rows are
   * unpinned.
   *
   * @param int $pinnedHeaderRowsCount
   */
  public function setPinnedHeaderRowsCount($pinnedHeaderRowsCount)
  {
    $this->pinnedHeaderRowsCount = $pinnedHeaderRowsCount;
  }
  /**
   * @return int
   */
  public function getPinnedHeaderRowsCount()
  {
    return $this->pinnedHeaderRowsCount;
  }
  /**
   * The location where the table starts in the document.
   *
   * @param Location $tableStartLocation
   */
  public function setTableStartLocation(Location $tableStartLocation)
  {
    $this->tableStartLocation = $tableStartLocation;
  }
  /**
   * @return Location
   */
  public function getTableStartLocation()
  {
    return $this->tableStartLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PinTableHeaderRowsRequest::class, 'Google_Service_Docs_PinTableHeaderRowsRequest');

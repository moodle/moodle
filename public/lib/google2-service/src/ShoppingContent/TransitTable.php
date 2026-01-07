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

namespace Google\Service\ShoppingContent;

class TransitTable extends \Google\Collection
{
  protected $collection_key = 'transitTimeLabels';
  /**
   * A list of postal group names. The last value can be `"all other
   * locations"`. Example: `["zone 1", "zone 2", "all other locations"]`. The
   * referred postal code groups must match the delivery country of the service.
   *
   * @var string[]
   */
  public $postalCodeGroupNames;
  protected $rowsType = TransitTableTransitTimeRow::class;
  protected $rowsDataType = 'array';
  /**
   * A list of transit time labels. The last value can be `"all other labels"`.
   * Example: `["food", "electronics", "all other labels"]`.
   *
   * @var string[]
   */
  public $transitTimeLabels;

  /**
   * A list of postal group names. The last value can be `"all other
   * locations"`. Example: `["zone 1", "zone 2", "all other locations"]`. The
   * referred postal code groups must match the delivery country of the service.
   *
   * @param string[] $postalCodeGroupNames
   */
  public function setPostalCodeGroupNames($postalCodeGroupNames)
  {
    $this->postalCodeGroupNames = $postalCodeGroupNames;
  }
  /**
   * @return string[]
   */
  public function getPostalCodeGroupNames()
  {
    return $this->postalCodeGroupNames;
  }
  /**
   * @param TransitTableTransitTimeRow[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return TransitTableTransitTimeRow[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * A list of transit time labels. The last value can be `"all other labels"`.
   * Example: `["food", "electronics", "all other labels"]`.
   *
   * @param string[] $transitTimeLabels
   */
  public function setTransitTimeLabels($transitTimeLabels)
  {
    $this->transitTimeLabels = $transitTimeLabels;
  }
  /**
   * @return string[]
   */
  public function getTransitTimeLabels()
  {
    return $this->transitTimeLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransitTable::class, 'Google_Service_ShoppingContent_TransitTable');

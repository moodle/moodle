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

namespace Google\Service\WorkloadManager;

class BigQueryDestination extends \Google\Model
{
  /**
   * Optional. determine if results will be saved in a new table
   *
   * @var bool
   */
  public $createNewResultsTable;
  /**
   * Optional. destination dataset to save evaluation results
   *
   * @var string
   */
  public $destinationDataset;

  /**
   * Optional. determine if results will be saved in a new table
   *
   * @param bool $createNewResultsTable
   */
  public function setCreateNewResultsTable($createNewResultsTable)
  {
    $this->createNewResultsTable = $createNewResultsTable;
  }
  /**
   * @return bool
   */
  public function getCreateNewResultsTable()
  {
    return $this->createNewResultsTable;
  }
  /**
   * Optional. destination dataset to save evaluation results
   *
   * @param string $destinationDataset
   */
  public function setDestinationDataset($destinationDataset)
  {
    $this->destinationDataset = $destinationDataset;
  }
  /**
   * @return string
   */
  public function getDestinationDataset()
  {
    return $this->destinationDataset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BigQueryDestination::class, 'Google_Service_WorkloadManager_BigQueryDestination');

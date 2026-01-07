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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataDiscoveryResultScanStatistics extends \Google\Model
{
  /**
   * The data processed in bytes.
   *
   * @var string
   */
  public $dataProcessedBytes;
  /**
   * The number of files excluded.
   *
   * @var int
   */
  public $filesExcluded;
  /**
   * The number of filesets created.
   *
   * @var int
   */
  public $filesetsCreated;
  /**
   * The number of filesets deleted.
   *
   * @var int
   */
  public $filesetsDeleted;
  /**
   * The number of filesets updated.
   *
   * @var int
   */
  public $filesetsUpdated;
  /**
   * The number of files scanned.
   *
   * @var int
   */
  public $scannedFileCount;
  /**
   * The number of tables created.
   *
   * @var int
   */
  public $tablesCreated;
  /**
   * The number of tables deleted.
   *
   * @var int
   */
  public $tablesDeleted;
  /**
   * The number of tables updated.
   *
   * @var int
   */
  public $tablesUpdated;

  /**
   * The data processed in bytes.
   *
   * @param string $dataProcessedBytes
   */
  public function setDataProcessedBytes($dataProcessedBytes)
  {
    $this->dataProcessedBytes = $dataProcessedBytes;
  }
  /**
   * @return string
   */
  public function getDataProcessedBytes()
  {
    return $this->dataProcessedBytes;
  }
  /**
   * The number of files excluded.
   *
   * @param int $filesExcluded
   */
  public function setFilesExcluded($filesExcluded)
  {
    $this->filesExcluded = $filesExcluded;
  }
  /**
   * @return int
   */
  public function getFilesExcluded()
  {
    return $this->filesExcluded;
  }
  /**
   * The number of filesets created.
   *
   * @param int $filesetsCreated
   */
  public function setFilesetsCreated($filesetsCreated)
  {
    $this->filesetsCreated = $filesetsCreated;
  }
  /**
   * @return int
   */
  public function getFilesetsCreated()
  {
    return $this->filesetsCreated;
  }
  /**
   * The number of filesets deleted.
   *
   * @param int $filesetsDeleted
   */
  public function setFilesetsDeleted($filesetsDeleted)
  {
    $this->filesetsDeleted = $filesetsDeleted;
  }
  /**
   * @return int
   */
  public function getFilesetsDeleted()
  {
    return $this->filesetsDeleted;
  }
  /**
   * The number of filesets updated.
   *
   * @param int $filesetsUpdated
   */
  public function setFilesetsUpdated($filesetsUpdated)
  {
    $this->filesetsUpdated = $filesetsUpdated;
  }
  /**
   * @return int
   */
  public function getFilesetsUpdated()
  {
    return $this->filesetsUpdated;
  }
  /**
   * The number of files scanned.
   *
   * @param int $scannedFileCount
   */
  public function setScannedFileCount($scannedFileCount)
  {
    $this->scannedFileCount = $scannedFileCount;
  }
  /**
   * @return int
   */
  public function getScannedFileCount()
  {
    return $this->scannedFileCount;
  }
  /**
   * The number of tables created.
   *
   * @param int $tablesCreated
   */
  public function setTablesCreated($tablesCreated)
  {
    $this->tablesCreated = $tablesCreated;
  }
  /**
   * @return int
   */
  public function getTablesCreated()
  {
    return $this->tablesCreated;
  }
  /**
   * The number of tables deleted.
   *
   * @param int $tablesDeleted
   */
  public function setTablesDeleted($tablesDeleted)
  {
    $this->tablesDeleted = $tablesDeleted;
  }
  /**
   * @return int
   */
  public function getTablesDeleted()
  {
    return $this->tablesDeleted;
  }
  /**
   * The number of tables updated.
   *
   * @param int $tablesUpdated
   */
  public function setTablesUpdated($tablesUpdated)
  {
    $this->tablesUpdated = $tablesUpdated;
  }
  /**
   * @return int
   */
  public function getTablesUpdated()
  {
    return $this->tablesUpdated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataDiscoveryResultScanStatistics::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataDiscoveryResultScanStatistics');

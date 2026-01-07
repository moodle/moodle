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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1p2alpha1ImportDataOperationResponse extends \Google\Model
{
  /**
   * Ouptut only. The name of imported dataset.
   *
   * @var string
   */
  public $dataset;
  /**
   * Output only. Number of examples imported successfully.
   *
   * @var int
   */
  public $importCount;
  /**
   * Output only. Total number of examples requested to import
   *
   * @var int
   */
  public $totalCount;

  /**
   * Ouptut only. The name of imported dataset.
   *
   * @param string $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return string
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * Output only. Number of examples imported successfully.
   *
   * @param int $importCount
   */
  public function setImportCount($importCount)
  {
    $this->importCount = $importCount;
  }
  /**
   * @return int
   */
  public function getImportCount()
  {
    return $this->importCount;
  }
  /**
   * Output only. Total number of examples requested to import
   *
   * @param int $totalCount
   */
  public function setTotalCount($totalCount)
  {
    $this->totalCount = $totalCount;
  }
  /**
   * @return int
   */
  public function getTotalCount()
  {
    return $this->totalCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1p2alpha1ImportDataOperationResponse::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1p2alpha1ImportDataOperationResponse');

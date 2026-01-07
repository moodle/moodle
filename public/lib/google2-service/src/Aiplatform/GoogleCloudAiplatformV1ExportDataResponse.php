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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ExportDataResponse extends \Google\Collection
{
  protected $collection_key = 'exportedFiles';
  protected $dataStatsType = GoogleCloudAiplatformV1ModelDataStats::class;
  protected $dataStatsDataType = '';
  /**
   * All of the files that are exported in this export operation. For custom
   * code training export, only three (training, validation and test) Cloud
   * Storage paths in wildcard format are populated (for example,
   * gs://.../training-*).
   *
   * @var string[]
   */
  public $exportedFiles;

  /**
   * Only present for custom code training export use case. Records data stats,
   * i.e., train/validation/test item/annotation counts calculated during the
   * export operation.
   *
   * @param GoogleCloudAiplatformV1ModelDataStats $dataStats
   */
  public function setDataStats(GoogleCloudAiplatformV1ModelDataStats $dataStats)
  {
    $this->dataStats = $dataStats;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelDataStats
   */
  public function getDataStats()
  {
    return $this->dataStats;
  }
  /**
   * All of the files that are exported in this export operation. For custom
   * code training export, only three (training, validation and test) Cloud
   * Storage paths in wildcard format are populated (for example,
   * gs://.../training-*).
   *
   * @param string[] $exportedFiles
   */
  public function setExportedFiles($exportedFiles)
  {
    $this->exportedFiles = $exportedFiles;
  }
  /**
   * @return string[]
   */
  public function getExportedFiles()
  {
    return $this->exportedFiles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExportDataResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExportDataResponse');

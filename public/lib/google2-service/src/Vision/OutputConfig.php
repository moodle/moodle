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

namespace Google\Service\Vision;

class OutputConfig extends \Google\Model
{
  /**
   * The max number of response protos to put into each output JSON file on
   * Google Cloud Storage. The valid range is [1, 100]. If not specified, the
   * default value is 20. For example, for one pdf file with 100 pages, 100
   * response protos will be generated. If `batch_size` = 20, then 5 json files
   * each containing 20 response protos will be written under the prefix
   * `gcs_destination`.`uri`. Currently, batch_size only applies to
   * GcsDestination, with potential future support for other output
   * configurations.
   *
   * @var int
   */
  public $batchSize;
  protected $gcsDestinationType = GcsDestination::class;
  protected $gcsDestinationDataType = '';

  /**
   * The max number of response protos to put into each output JSON file on
   * Google Cloud Storage. The valid range is [1, 100]. If not specified, the
   * default value is 20. For example, for one pdf file with 100 pages, 100
   * response protos will be generated. If `batch_size` = 20, then 5 json files
   * each containing 20 response protos will be written under the prefix
   * `gcs_destination`.`uri`. Currently, batch_size only applies to
   * GcsDestination, with potential future support for other output
   * configurations.
   *
   * @param int $batchSize
   */
  public function setBatchSize($batchSize)
  {
    $this->batchSize = $batchSize;
  }
  /**
   * @return int
   */
  public function getBatchSize()
  {
    return $this->batchSize;
  }
  /**
   * The Google Cloud Storage location to write the output(s) to.
   *
   * @param GcsDestination $gcsDestination
   */
  public function setGcsDestination(GcsDestination $gcsDestination)
  {
    $this->gcsDestination = $gcsDestination;
  }
  /**
   * @return GcsDestination
   */
  public function getGcsDestination()
  {
    return $this->gcsDestination;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OutputConfig::class, 'Google_Service_Vision_OutputConfig');

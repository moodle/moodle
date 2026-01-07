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

namespace Google\Service\Bigquery;

class LoadQueryStatistics extends \Google\Model
{
  /**
   * Output only. The number of bad records encountered while processing a LOAD
   * query. Note that if the job has failed because of more bad records
   * encountered than the maximum allowed in the load job configuration, then
   * this number can be less than the total number of bad records present in the
   * input data.
   *
   * @var string
   */
  public $badRecords;
  /**
   * Output only. This field is deprecated. The number of bytes of source data
   * copied over the network for a `LOAD` query. `transferred_bytes` has the
   * canonical value for physical transferred bytes, which is used for BigQuery
   * Omni billing.
   *
   * @deprecated
   * @var string
   */
  public $bytesTransferred;
  /**
   * Output only. Number of bytes of source data in a LOAD query.
   *
   * @var string
   */
  public $inputFileBytes;
  /**
   * Output only. Number of source files in a LOAD query.
   *
   * @var string
   */
  public $inputFiles;
  /**
   * Output only. Size of the loaded data in bytes. Note that while a LOAD query
   * is in the running state, this value may change.
   *
   * @var string
   */
  public $outputBytes;
  /**
   * Output only. Number of rows imported in a LOAD query. Note that while a
   * LOAD query is in the running state, this value may change.
   *
   * @var string
   */
  public $outputRows;

  /**
   * Output only. The number of bad records encountered while processing a LOAD
   * query. Note that if the job has failed because of more bad records
   * encountered than the maximum allowed in the load job configuration, then
   * this number can be less than the total number of bad records present in the
   * input data.
   *
   * @param string $badRecords
   */
  public function setBadRecords($badRecords)
  {
    $this->badRecords = $badRecords;
  }
  /**
   * @return string
   */
  public function getBadRecords()
  {
    return $this->badRecords;
  }
  /**
   * Output only. This field is deprecated. The number of bytes of source data
   * copied over the network for a `LOAD` query. `transferred_bytes` has the
   * canonical value for physical transferred bytes, which is used for BigQuery
   * Omni billing.
   *
   * @deprecated
   * @param string $bytesTransferred
   */
  public function setBytesTransferred($bytesTransferred)
  {
    $this->bytesTransferred = $bytesTransferred;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBytesTransferred()
  {
    return $this->bytesTransferred;
  }
  /**
   * Output only. Number of bytes of source data in a LOAD query.
   *
   * @param string $inputFileBytes
   */
  public function setInputFileBytes($inputFileBytes)
  {
    $this->inputFileBytes = $inputFileBytes;
  }
  /**
   * @return string
   */
  public function getInputFileBytes()
  {
    return $this->inputFileBytes;
  }
  /**
   * Output only. Number of source files in a LOAD query.
   *
   * @param string $inputFiles
   */
  public function setInputFiles($inputFiles)
  {
    $this->inputFiles = $inputFiles;
  }
  /**
   * @return string
   */
  public function getInputFiles()
  {
    return $this->inputFiles;
  }
  /**
   * Output only. Size of the loaded data in bytes. Note that while a LOAD query
   * is in the running state, this value may change.
   *
   * @param string $outputBytes
   */
  public function setOutputBytes($outputBytes)
  {
    $this->outputBytes = $outputBytes;
  }
  /**
   * @return string
   */
  public function getOutputBytes()
  {
    return $this->outputBytes;
  }
  /**
   * Output only. Number of rows imported in a LOAD query. Note that while a
   * LOAD query is in the running state, this value may change.
   *
   * @param string $outputRows
   */
  public function setOutputRows($outputRows)
  {
    $this->outputRows = $outputRows;
  }
  /**
   * @return string
   */
  public function getOutputRows()
  {
    return $this->outputRows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoadQueryStatistics::class, 'Google_Service_Bigquery_LoadQueryStatistics');

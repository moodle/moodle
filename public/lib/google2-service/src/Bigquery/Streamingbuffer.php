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

class Streamingbuffer extends \Google\Model
{
  /**
   * Output only. A lower-bound estimate of the number of bytes currently in the
   * streaming buffer.
   *
   * @var string
   */
  public $estimatedBytes;
  /**
   * Output only. A lower-bound estimate of the number of rows currently in the
   * streaming buffer.
   *
   * @var string
   */
  public $estimatedRows;
  /**
   * Output only. Contains the timestamp of the oldest entry in the streaming
   * buffer, in milliseconds since the epoch, if the streaming buffer is
   * available.
   *
   * @var string
   */
  public $oldestEntryTime;

  /**
   * Output only. A lower-bound estimate of the number of bytes currently in the
   * streaming buffer.
   *
   * @param string $estimatedBytes
   */
  public function setEstimatedBytes($estimatedBytes)
  {
    $this->estimatedBytes = $estimatedBytes;
  }
  /**
   * @return string
   */
  public function getEstimatedBytes()
  {
    return $this->estimatedBytes;
  }
  /**
   * Output only. A lower-bound estimate of the number of rows currently in the
   * streaming buffer.
   *
   * @param string $estimatedRows
   */
  public function setEstimatedRows($estimatedRows)
  {
    $this->estimatedRows = $estimatedRows;
  }
  /**
   * @return string
   */
  public function getEstimatedRows()
  {
    return $this->estimatedRows;
  }
  /**
   * Output only. Contains the timestamp of the oldest entry in the streaming
   * buffer, in milliseconds since the epoch, if the streaming buffer is
   * available.
   *
   * @param string $oldestEntryTime
   */
  public function setOldestEntryTime($oldestEntryTime)
  {
    $this->oldestEntryTime = $oldestEntryTime;
  }
  /**
   * @return string
   */
  public function getOldestEntryTime()
  {
    return $this->oldestEntryTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Streamingbuffer::class, 'Google_Service_Bigquery_Streamingbuffer');

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

namespace Google\Service\Dataproc;

class ShuffleWriteQuantileMetrics extends \Google\Model
{
  protected $writeBytesType = Quantiles::class;
  protected $writeBytesDataType = '';
  protected $writeRecordsType = Quantiles::class;
  protected $writeRecordsDataType = '';
  protected $writeTimeNanosType = Quantiles::class;
  protected $writeTimeNanosDataType = '';

  /**
   * @param Quantiles $writeBytes
   */
  public function setWriteBytes(Quantiles $writeBytes)
  {
    $this->writeBytes = $writeBytes;
  }
  /**
   * @return Quantiles
   */
  public function getWriteBytes()
  {
    return $this->writeBytes;
  }
  /**
   * @param Quantiles $writeRecords
   */
  public function setWriteRecords(Quantiles $writeRecords)
  {
    $this->writeRecords = $writeRecords;
  }
  /**
   * @return Quantiles
   */
  public function getWriteRecords()
  {
    return $this->writeRecords;
  }
  /**
   * @param Quantiles $writeTimeNanos
   */
  public function setWriteTimeNanos(Quantiles $writeTimeNanos)
  {
    $this->writeTimeNanos = $writeTimeNanos;
  }
  /**
   * @return Quantiles
   */
  public function getWriteTimeNanos()
  {
    return $this->writeTimeNanos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShuffleWriteQuantileMetrics::class, 'Google_Service_Dataproc_ShuffleWriteQuantileMetrics');

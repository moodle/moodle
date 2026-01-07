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

class OutputQuantileMetrics extends \Google\Model
{
  protected $bytesWrittenType = Quantiles::class;
  protected $bytesWrittenDataType = '';
  protected $recordsWrittenType = Quantiles::class;
  protected $recordsWrittenDataType = '';

  /**
   * @param Quantiles $bytesWritten
   */
  public function setBytesWritten(Quantiles $bytesWritten)
  {
    $this->bytesWritten = $bytesWritten;
  }
  /**
   * @return Quantiles
   */
  public function getBytesWritten()
  {
    return $this->bytesWritten;
  }
  /**
   * @param Quantiles $recordsWritten
   */
  public function setRecordsWritten(Quantiles $recordsWritten)
  {
    $this->recordsWritten = $recordsWritten;
  }
  /**
   * @return Quantiles
   */
  public function getRecordsWritten()
  {
    return $this->recordsWritten;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OutputQuantileMetrics::class, 'Google_Service_Dataproc_OutputQuantileMetrics');

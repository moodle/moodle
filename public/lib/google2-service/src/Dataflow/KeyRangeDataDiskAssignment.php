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

namespace Google\Service\Dataflow;

class KeyRangeDataDiskAssignment extends \Google\Model
{
  /**
   * The name of the data disk where data for this range is stored. This name is
   * local to the Google Cloud Platform project and uniquely identifies the disk
   * within that project, for example
   * "myproject-1014-104817-4c2-harness-0-disk-1".
   *
   * @var string
   */
  public $dataDisk;
  /**
   * The end (exclusive) of the key range.
   *
   * @var string
   */
  public $end;
  /**
   * The start (inclusive) of the key range.
   *
   * @var string
   */
  public $start;

  /**
   * The name of the data disk where data for this range is stored. This name is
   * local to the Google Cloud Platform project and uniquely identifies the disk
   * within that project, for example
   * "myproject-1014-104817-4c2-harness-0-disk-1".
   *
   * @param string $dataDisk
   */
  public function setDataDisk($dataDisk)
  {
    $this->dataDisk = $dataDisk;
  }
  /**
   * @return string
   */
  public function getDataDisk()
  {
    return $this->dataDisk;
  }
  /**
   * The end (exclusive) of the key range.
   *
   * @param string $end
   */
  public function setEnd($end)
  {
    $this->end = $end;
  }
  /**
   * @return string
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * The start (inclusive) of the key range.
   *
   * @param string $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  /**
   * @return string
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyRangeDataDiskAssignment::class, 'Google_Service_Dataflow_KeyRangeDataDiskAssignment');

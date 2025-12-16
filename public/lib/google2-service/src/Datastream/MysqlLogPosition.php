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

namespace Google\Service\Datastream;

class MysqlLogPosition extends \Google\Model
{
  /**
   * Required. The binary log file name.
   *
   * @var string
   */
  public $logFile;
  /**
   * Optional. The position within the binary log file. Default is head of file.
   *
   * @var int
   */
  public $logPosition;

  /**
   * Required. The binary log file name.
   *
   * @param string $logFile
   */
  public function setLogFile($logFile)
  {
    $this->logFile = $logFile;
  }
  /**
   * @return string
   */
  public function getLogFile()
  {
    return $this->logFile;
  }
  /**
   * Optional. The position within the binary log file. Default is head of file.
   *
   * @param int $logPosition
   */
  public function setLogPosition($logPosition)
  {
    $this->logPosition = $logPosition;
  }
  /**
   * @return int
   */
  public function getLogPosition()
  {
    return $this->logPosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MysqlLogPosition::class, 'Google_Service_Datastream_MysqlLogPosition');

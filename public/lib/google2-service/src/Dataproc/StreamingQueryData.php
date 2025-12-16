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

class StreamingQueryData extends \Google\Model
{
  /**
   * @var string
   */
  public $endTimestamp;
  /**
   * @var string
   */
  public $exception;
  /**
   * @var bool
   */
  public $isActive;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $runId;
  /**
   * @var string
   */
  public $startTimestamp;
  /**
   * @var string
   */
  public $streamingQueryId;

  /**
   * @param string $endTimestamp
   */
  public function setEndTimestamp($endTimestamp)
  {
    $this->endTimestamp = $endTimestamp;
  }
  /**
   * @return string
   */
  public function getEndTimestamp()
  {
    return $this->endTimestamp;
  }
  /**
   * @param string $exception
   */
  public function setException($exception)
  {
    $this->exception = $exception;
  }
  /**
   * @return string
   */
  public function getException()
  {
    return $this->exception;
  }
  /**
   * @param bool $isActive
   */
  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
  }
  /**
   * @return bool
   */
  public function getIsActive()
  {
    return $this->isActive;
  }
  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * @param string $runId
   */
  public function setRunId($runId)
  {
    $this->runId = $runId;
  }
  /**
   * @return string
   */
  public function getRunId()
  {
    return $this->runId;
  }
  /**
   * @param string $startTimestamp
   */
  public function setStartTimestamp($startTimestamp)
  {
    $this->startTimestamp = $startTimestamp;
  }
  /**
   * @return string
   */
  public function getStartTimestamp()
  {
    return $this->startTimestamp;
  }
  /**
   * @param string $streamingQueryId
   */
  public function setStreamingQueryId($streamingQueryId)
  {
    $this->streamingQueryId = $streamingQueryId;
  }
  /**
   * @return string
   */
  public function getStreamingQueryId()
  {
    return $this->streamingQueryId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamingQueryData::class, 'Google_Service_Dataproc_StreamingQueryData');

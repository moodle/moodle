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

class StreamingQueryProgress extends \Google\Collection
{
  protected $collection_key = 'stateOperators';
  /**
   * @var string
   */
  public $batchDuration;
  /**
   * @var string
   */
  public $batchId;
  /**
   * @var string[]
   */
  public $durationMillis;
  /**
   * @var string[]
   */
  public $eventTime;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string[]
   */
  public $observedMetrics;
  /**
   * @var string
   */
  public $runId;
  protected $sinkType = SinkProgress::class;
  protected $sinkDataType = '';
  protected $sourcesType = SourceProgress::class;
  protected $sourcesDataType = 'array';
  protected $stateOperatorsType = StateOperatorProgress::class;
  protected $stateOperatorsDataType = 'array';
  /**
   * @var string
   */
  public $streamingQueryProgressId;
  /**
   * @var string
   */
  public $timestamp;

  /**
   * @param string $batchDuration
   */
  public function setBatchDuration($batchDuration)
  {
    $this->batchDuration = $batchDuration;
  }
  /**
   * @return string
   */
  public function getBatchDuration()
  {
    return $this->batchDuration;
  }
  /**
   * @param string $batchId
   */
  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  /**
   * @return string
   */
  public function getBatchId()
  {
    return $this->batchId;
  }
  /**
   * @param string[] $durationMillis
   */
  public function setDurationMillis($durationMillis)
  {
    $this->durationMillis = $durationMillis;
  }
  /**
   * @return string[]
   */
  public function getDurationMillis()
  {
    return $this->durationMillis;
  }
  /**
   * @param string[] $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string[]
   */
  public function getEventTime()
  {
    return $this->eventTime;
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
   * @param string[] $observedMetrics
   */
  public function setObservedMetrics($observedMetrics)
  {
    $this->observedMetrics = $observedMetrics;
  }
  /**
   * @return string[]
   */
  public function getObservedMetrics()
  {
    return $this->observedMetrics;
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
   * @param SinkProgress $sink
   */
  public function setSink(SinkProgress $sink)
  {
    $this->sink = $sink;
  }
  /**
   * @return SinkProgress
   */
  public function getSink()
  {
    return $this->sink;
  }
  /**
   * @param SourceProgress[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return SourceProgress[]
   */
  public function getSources()
  {
    return $this->sources;
  }
  /**
   * @param StateOperatorProgress[] $stateOperators
   */
  public function setStateOperators($stateOperators)
  {
    $this->stateOperators = $stateOperators;
  }
  /**
   * @return StateOperatorProgress[]
   */
  public function getStateOperators()
  {
    return $this->stateOperators;
  }
  /**
   * @param string $streamingQueryProgressId
   */
  public function setStreamingQueryProgressId($streamingQueryProgressId)
  {
    $this->streamingQueryProgressId = $streamingQueryProgressId;
  }
  /**
   * @return string
   */
  public function getStreamingQueryProgressId()
  {
    return $this->streamingQueryProgressId;
  }
  /**
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamingQueryProgress::class, 'Google_Service_Dataproc_StreamingQueryProgress');

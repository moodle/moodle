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

class ExplainQueryStage extends \Google\Collection
{
  /**
   * ComputeMode type not specified.
   */
  public const COMPUTE_MODE_COMPUTE_MODE_UNSPECIFIED = 'COMPUTE_MODE_UNSPECIFIED';
  /**
   * This stage was processed using BigQuery slots.
   */
  public const COMPUTE_MODE_BIGQUERY = 'BIGQUERY';
  /**
   * This stage was processed using BI Engine compute.
   */
  public const COMPUTE_MODE_BI_ENGINE = 'BI_ENGINE';
  protected $collection_key = 'steps';
  /**
   * Number of parallel input segments completed.
   *
   * @var string
   */
  public $completedParallelInputs;
  /**
   * Output only. Compute mode for this stage.
   *
   * @var string
   */
  public $computeMode;
  /**
   * Milliseconds the average shard spent on CPU-bound tasks.
   *
   * @var string
   */
  public $computeMsAvg;
  /**
   * Milliseconds the slowest shard spent on CPU-bound tasks.
   *
   * @var string
   */
  public $computeMsMax;
  /**
   * Relative amount of time the average shard spent on CPU-bound tasks.
   *
   * @var 
   */
  public $computeRatioAvg;
  /**
   * Relative amount of time the slowest shard spent on CPU-bound tasks.
   *
   * @var 
   */
  public $computeRatioMax;
  /**
   * Stage end time represented as milliseconds since the epoch.
   *
   * @var string
   */
  public $endMs;
  /**
   * Unique ID for the stage within the plan.
   *
   * @var string
   */
  public $id;
  /**
   * IDs for stages that are inputs to this stage.
   *
   * @var string[]
   */
  public $inputStages;
  /**
   * Human-readable name for the stage.
   *
   * @var string
   */
  public $name;
  /**
   * Number of parallel input segments to be processed
   *
   * @var string
   */
  public $parallelInputs;
  /**
   * Milliseconds the average shard spent reading input.
   *
   * @var string
   */
  public $readMsAvg;
  /**
   * Milliseconds the slowest shard spent reading input.
   *
   * @var string
   */
  public $readMsMax;
  /**
   * Relative amount of time the average shard spent reading input.
   *
   * @var 
   */
  public $readRatioAvg;
  /**
   * Relative amount of time the slowest shard spent reading input.
   *
   * @var 
   */
  public $readRatioMax;
  /**
   * Number of records read into the stage.
   *
   * @var string
   */
  public $recordsRead;
  /**
   * Number of records written by the stage.
   *
   * @var string
   */
  public $recordsWritten;
  /**
   * Total number of bytes written to shuffle.
   *
   * @var string
   */
  public $shuffleOutputBytes;
  /**
   * Total number of bytes written to shuffle and spilled to disk.
   *
   * @var string
   */
  public $shuffleOutputBytesSpilled;
  /**
   * Slot-milliseconds used by the stage.
   *
   * @var string
   */
  public $slotMs;
  /**
   * Stage start time represented as milliseconds since the epoch.
   *
   * @var string
   */
  public $startMs;
  /**
   * Current status for this stage.
   *
   * @var string
   */
  public $status;
  protected $stepsType = ExplainQueryStep::class;
  protected $stepsDataType = 'array';
  /**
   * Milliseconds the average shard spent waiting to be scheduled.
   *
   * @var string
   */
  public $waitMsAvg;
  /**
   * Milliseconds the slowest shard spent waiting to be scheduled.
   *
   * @var string
   */
  public $waitMsMax;
  /**
   * Relative amount of time the average shard spent waiting to be scheduled.
   *
   * @var 
   */
  public $waitRatioAvg;
  /**
   * Relative amount of time the slowest shard spent waiting to be scheduled.
   *
   * @var 
   */
  public $waitRatioMax;
  /**
   * Milliseconds the average shard spent on writing output.
   *
   * @var string
   */
  public $writeMsAvg;
  /**
   * Milliseconds the slowest shard spent on writing output.
   *
   * @var string
   */
  public $writeMsMax;
  /**
   * Relative amount of time the average shard spent on writing output.
   *
   * @var 
   */
  public $writeRatioAvg;
  /**
   * Relative amount of time the slowest shard spent on writing output.
   *
   * @var 
   */
  public $writeRatioMax;

  /**
   * Number of parallel input segments completed.
   *
   * @param string $completedParallelInputs
   */
  public function setCompletedParallelInputs($completedParallelInputs)
  {
    $this->completedParallelInputs = $completedParallelInputs;
  }
  /**
   * @return string
   */
  public function getCompletedParallelInputs()
  {
    return $this->completedParallelInputs;
  }
  /**
   * Output only. Compute mode for this stage.
   *
   * Accepted values: COMPUTE_MODE_UNSPECIFIED, BIGQUERY, BI_ENGINE
   *
   * @param self::COMPUTE_MODE_* $computeMode
   */
  public function setComputeMode($computeMode)
  {
    $this->computeMode = $computeMode;
  }
  /**
   * @return self::COMPUTE_MODE_*
   */
  public function getComputeMode()
  {
    return $this->computeMode;
  }
  /**
   * Milliseconds the average shard spent on CPU-bound tasks.
   *
   * @param string $computeMsAvg
   */
  public function setComputeMsAvg($computeMsAvg)
  {
    $this->computeMsAvg = $computeMsAvg;
  }
  /**
   * @return string
   */
  public function getComputeMsAvg()
  {
    return $this->computeMsAvg;
  }
  /**
   * Milliseconds the slowest shard spent on CPU-bound tasks.
   *
   * @param string $computeMsMax
   */
  public function setComputeMsMax($computeMsMax)
  {
    $this->computeMsMax = $computeMsMax;
  }
  /**
   * @return string
   */
  public function getComputeMsMax()
  {
    return $this->computeMsMax;
  }
  public function setComputeRatioAvg($computeRatioAvg)
  {
    $this->computeRatioAvg = $computeRatioAvg;
  }
  public function getComputeRatioAvg()
  {
    return $this->computeRatioAvg;
  }
  public function setComputeRatioMax($computeRatioMax)
  {
    $this->computeRatioMax = $computeRatioMax;
  }
  public function getComputeRatioMax()
  {
    return $this->computeRatioMax;
  }
  /**
   * Stage end time represented as milliseconds since the epoch.
   *
   * @param string $endMs
   */
  public function setEndMs($endMs)
  {
    $this->endMs = $endMs;
  }
  /**
   * @return string
   */
  public function getEndMs()
  {
    return $this->endMs;
  }
  /**
   * Unique ID for the stage within the plan.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * IDs for stages that are inputs to this stage.
   *
   * @param string[] $inputStages
   */
  public function setInputStages($inputStages)
  {
    $this->inputStages = $inputStages;
  }
  /**
   * @return string[]
   */
  public function getInputStages()
  {
    return $this->inputStages;
  }
  /**
   * Human-readable name for the stage.
   *
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
   * Number of parallel input segments to be processed
   *
   * @param string $parallelInputs
   */
  public function setParallelInputs($parallelInputs)
  {
    $this->parallelInputs = $parallelInputs;
  }
  /**
   * @return string
   */
  public function getParallelInputs()
  {
    return $this->parallelInputs;
  }
  /**
   * Milliseconds the average shard spent reading input.
   *
   * @param string $readMsAvg
   */
  public function setReadMsAvg($readMsAvg)
  {
    $this->readMsAvg = $readMsAvg;
  }
  /**
   * @return string
   */
  public function getReadMsAvg()
  {
    return $this->readMsAvg;
  }
  /**
   * Milliseconds the slowest shard spent reading input.
   *
   * @param string $readMsMax
   */
  public function setReadMsMax($readMsMax)
  {
    $this->readMsMax = $readMsMax;
  }
  /**
   * @return string
   */
  public function getReadMsMax()
  {
    return $this->readMsMax;
  }
  public function setReadRatioAvg($readRatioAvg)
  {
    $this->readRatioAvg = $readRatioAvg;
  }
  public function getReadRatioAvg()
  {
    return $this->readRatioAvg;
  }
  public function setReadRatioMax($readRatioMax)
  {
    $this->readRatioMax = $readRatioMax;
  }
  public function getReadRatioMax()
  {
    return $this->readRatioMax;
  }
  /**
   * Number of records read into the stage.
   *
   * @param string $recordsRead
   */
  public function setRecordsRead($recordsRead)
  {
    $this->recordsRead = $recordsRead;
  }
  /**
   * @return string
   */
  public function getRecordsRead()
  {
    return $this->recordsRead;
  }
  /**
   * Number of records written by the stage.
   *
   * @param string $recordsWritten
   */
  public function setRecordsWritten($recordsWritten)
  {
    $this->recordsWritten = $recordsWritten;
  }
  /**
   * @return string
   */
  public function getRecordsWritten()
  {
    return $this->recordsWritten;
  }
  /**
   * Total number of bytes written to shuffle.
   *
   * @param string $shuffleOutputBytes
   */
  public function setShuffleOutputBytes($shuffleOutputBytes)
  {
    $this->shuffleOutputBytes = $shuffleOutputBytes;
  }
  /**
   * @return string
   */
  public function getShuffleOutputBytes()
  {
    return $this->shuffleOutputBytes;
  }
  /**
   * Total number of bytes written to shuffle and spilled to disk.
   *
   * @param string $shuffleOutputBytesSpilled
   */
  public function setShuffleOutputBytesSpilled($shuffleOutputBytesSpilled)
  {
    $this->shuffleOutputBytesSpilled = $shuffleOutputBytesSpilled;
  }
  /**
   * @return string
   */
  public function getShuffleOutputBytesSpilled()
  {
    return $this->shuffleOutputBytesSpilled;
  }
  /**
   * Slot-milliseconds used by the stage.
   *
   * @param string $slotMs
   */
  public function setSlotMs($slotMs)
  {
    $this->slotMs = $slotMs;
  }
  /**
   * @return string
   */
  public function getSlotMs()
  {
    return $this->slotMs;
  }
  /**
   * Stage start time represented as milliseconds since the epoch.
   *
   * @param string $startMs
   */
  public function setStartMs($startMs)
  {
    $this->startMs = $startMs;
  }
  /**
   * @return string
   */
  public function getStartMs()
  {
    return $this->startMs;
  }
  /**
   * Current status for this stage.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * List of operations within the stage in dependency order (approximately
   * chronological).
   *
   * @param ExplainQueryStep[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return ExplainQueryStep[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
  /**
   * Milliseconds the average shard spent waiting to be scheduled.
   *
   * @param string $waitMsAvg
   */
  public function setWaitMsAvg($waitMsAvg)
  {
    $this->waitMsAvg = $waitMsAvg;
  }
  /**
   * @return string
   */
  public function getWaitMsAvg()
  {
    return $this->waitMsAvg;
  }
  /**
   * Milliseconds the slowest shard spent waiting to be scheduled.
   *
   * @param string $waitMsMax
   */
  public function setWaitMsMax($waitMsMax)
  {
    $this->waitMsMax = $waitMsMax;
  }
  /**
   * @return string
   */
  public function getWaitMsMax()
  {
    return $this->waitMsMax;
  }
  public function setWaitRatioAvg($waitRatioAvg)
  {
    $this->waitRatioAvg = $waitRatioAvg;
  }
  public function getWaitRatioAvg()
  {
    return $this->waitRatioAvg;
  }
  public function setWaitRatioMax($waitRatioMax)
  {
    $this->waitRatioMax = $waitRatioMax;
  }
  public function getWaitRatioMax()
  {
    return $this->waitRatioMax;
  }
  /**
   * Milliseconds the average shard spent on writing output.
   *
   * @param string $writeMsAvg
   */
  public function setWriteMsAvg($writeMsAvg)
  {
    $this->writeMsAvg = $writeMsAvg;
  }
  /**
   * @return string
   */
  public function getWriteMsAvg()
  {
    return $this->writeMsAvg;
  }
  /**
   * Milliseconds the slowest shard spent on writing output.
   *
   * @param string $writeMsMax
   */
  public function setWriteMsMax($writeMsMax)
  {
    $this->writeMsMax = $writeMsMax;
  }
  /**
   * @return string
   */
  public function getWriteMsMax()
  {
    return $this->writeMsMax;
  }
  public function setWriteRatioAvg($writeRatioAvg)
  {
    $this->writeRatioAvg = $writeRatioAvg;
  }
  public function getWriteRatioAvg()
  {
    return $this->writeRatioAvg;
  }
  public function setWriteRatioMax($writeRatioMax)
  {
    $this->writeRatioMax = $writeRatioMax;
  }
  public function getWriteRatioMax()
  {
    return $this->writeRatioMax;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExplainQueryStage::class, 'Google_Service_Bigquery_ExplainQueryStage');

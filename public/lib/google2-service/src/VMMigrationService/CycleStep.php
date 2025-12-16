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

namespace Google\Service\VMMigrationService;

class CycleStep extends \Google\Model
{
  /**
   * The time the cycle step has ended.
   *
   * @var string
   */
  public $endTime;
  protected $initializingReplicationType = InitializingReplicationStep::class;
  protected $initializingReplicationDataType = '';
  protected $postProcessingType = PostProcessingStep::class;
  protected $postProcessingDataType = '';
  protected $replicatingType = ReplicatingStep::class;
  protected $replicatingDataType = '';
  /**
   * The time the cycle step has started.
   *
   * @var string
   */
  public $startTime;

  /**
   * The time the cycle step has ended.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Initializing replication step.
   *
   * @param InitializingReplicationStep $initializingReplication
   */
  public function setInitializingReplication(InitializingReplicationStep $initializingReplication)
  {
    $this->initializingReplication = $initializingReplication;
  }
  /**
   * @return InitializingReplicationStep
   */
  public function getInitializingReplication()
  {
    return $this->initializingReplication;
  }
  /**
   * Post processing step.
   *
   * @param PostProcessingStep $postProcessing
   */
  public function setPostProcessing(PostProcessingStep $postProcessing)
  {
    $this->postProcessing = $postProcessing;
  }
  /**
   * @return PostProcessingStep
   */
  public function getPostProcessing()
  {
    return $this->postProcessing;
  }
  /**
   * Replicating step.
   *
   * @param ReplicatingStep $replicating
   */
  public function setReplicating(ReplicatingStep $replicating)
  {
    $this->replicating = $replicating;
  }
  /**
   * @return ReplicatingStep
   */
  public function getReplicating()
  {
    return $this->replicating;
  }
  /**
   * The time the cycle step has started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CycleStep::class, 'Google_Service_VMMigrationService_CycleStep');

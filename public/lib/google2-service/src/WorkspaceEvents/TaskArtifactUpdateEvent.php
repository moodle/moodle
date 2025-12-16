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

namespace Google\Service\WorkspaceEvents;

class TaskArtifactUpdateEvent extends \Google\Model
{
  /**
   * Whether this should be appended to a prior one produced
   *
   * @var bool
   */
  public $append;
  protected $artifactType = Artifact::class;
  protected $artifactDataType = '';
  /**
   * The id of the context that this task belongs too
   *
   * @var string
   */
  public $contextId;
  /**
   * Whether this represents the last part of an artifact
   *
   * @var bool
   */
  public $lastChunk;
  /**
   * Optional metadata associated with the artifact update.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * The id of the task for this artifact
   *
   * @var string
   */
  public $taskId;

  /**
   * Whether this should be appended to a prior one produced
   *
   * @param bool $append
   */
  public function setAppend($append)
  {
    $this->append = $append;
  }
  /**
   * @return bool
   */
  public function getAppend()
  {
    return $this->append;
  }
  /**
   * The artifact itself
   *
   * @param Artifact $artifact
   */
  public function setArtifact(Artifact $artifact)
  {
    $this->artifact = $artifact;
  }
  /**
   * @return Artifact
   */
  public function getArtifact()
  {
    return $this->artifact;
  }
  /**
   * The id of the context that this task belongs too
   *
   * @param string $contextId
   */
  public function setContextId($contextId)
  {
    $this->contextId = $contextId;
  }
  /**
   * @return string
   */
  public function getContextId()
  {
    return $this->contextId;
  }
  /**
   * Whether this represents the last part of an artifact
   *
   * @param bool $lastChunk
   */
  public function setLastChunk($lastChunk)
  {
    $this->lastChunk = $lastChunk;
  }
  /**
   * @return bool
   */
  public function getLastChunk()
  {
    return $this->lastChunk;
  }
  /**
   * Optional metadata associated with the artifact update.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The id of the task for this artifact
   *
   * @param string $taskId
   */
  public function setTaskId($taskId)
  {
    $this->taskId = $taskId;
  }
  /**
   * @return string
   */
  public function getTaskId()
  {
    return $this->taskId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskArtifactUpdateEvent::class, 'Google_Service_WorkspaceEvents_TaskArtifactUpdateEvent');

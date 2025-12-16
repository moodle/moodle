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

class StreamResponse extends \Google\Model
{
  protected $artifactUpdateType = TaskArtifactUpdateEvent::class;
  protected $artifactUpdateDataType = '';
  protected $messageType = Message::class;
  protected $messageDataType = '';
  protected $statusUpdateType = TaskStatusUpdateEvent::class;
  protected $statusUpdateDataType = '';
  protected $taskType = Task::class;
  protected $taskDataType = '';

  /**
   * @param TaskArtifactUpdateEvent $artifactUpdate
   */
  public function setArtifactUpdate(TaskArtifactUpdateEvent $artifactUpdate)
  {
    $this->artifactUpdate = $artifactUpdate;
  }
  /**
   * @return TaskArtifactUpdateEvent
   */
  public function getArtifactUpdate()
  {
    return $this->artifactUpdate;
  }
  /**
   * @param Message $message
   */
  public function setMessage(Message $message)
  {
    $this->message = $message;
  }
  /**
   * @return Message
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * @param TaskStatusUpdateEvent $statusUpdate
   */
  public function setStatusUpdate(TaskStatusUpdateEvent $statusUpdate)
  {
    $this->statusUpdate = $statusUpdate;
  }
  /**
   * @return TaskStatusUpdateEvent
   */
  public function getStatusUpdate()
  {
    return $this->statusUpdate;
  }
  /**
   * @param Task $task
   */
  public function setTask(Task $task)
  {
    $this->task = $task;
  }
  /**
   * @return Task
   */
  public function getTask()
  {
    return $this->task;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamResponse::class, 'Google_Service_WorkspaceEvents_StreamResponse');

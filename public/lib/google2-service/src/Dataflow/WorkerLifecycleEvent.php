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

class WorkerLifecycleEvent extends \Google\Model
{
  /**
   * Invalid event.
   */
  public const EVENT_UNKNOWN_EVENT = 'UNKNOWN_EVENT';
  /**
   * The time the VM started.
   */
  public const EVENT_OS_START = 'OS_START';
  /**
   * Our container code starts running. Multiple containers could be
   * distinguished with WorkerMessage.labels if desired.
   */
  public const EVENT_CONTAINER_START = 'CONTAINER_START';
  /**
   * The worker has a functional external network connection.
   */
  public const EVENT_NETWORK_UP = 'NETWORK_UP';
  /**
   * Started downloading staging files.
   */
  public const EVENT_STAGING_FILES_DOWNLOAD_START = 'STAGING_FILES_DOWNLOAD_START';
  /**
   * Finished downloading all staging files.
   */
  public const EVENT_STAGING_FILES_DOWNLOAD_FINISH = 'STAGING_FILES_DOWNLOAD_FINISH';
  /**
   * For applicable SDKs, started installation of SDK and worker packages.
   */
  public const EVENT_SDK_INSTALL_START = 'SDK_INSTALL_START';
  /**
   * Finished installing SDK.
   */
  public const EVENT_SDK_INSTALL_FINISH = 'SDK_INSTALL_FINISH';
  /**
   * The start time of this container. All events will report this so that
   * events can be grouped together across container/VM restarts.
   *
   * @var string
   */
  public $containerStartTime;
  /**
   * The event being reported.
   *
   * @var string
   */
  public $event;
  /**
   * Other stats that can accompany an event. E.g. { "downloaded_bytes" :
   * "123456" }
   *
   * @var string[]
   */
  public $metadata;

  /**
   * The start time of this container. All events will report this so that
   * events can be grouped together across container/VM restarts.
   *
   * @param string $containerStartTime
   */
  public function setContainerStartTime($containerStartTime)
  {
    $this->containerStartTime = $containerStartTime;
  }
  /**
   * @return string
   */
  public function getContainerStartTime()
  {
    return $this->containerStartTime;
  }
  /**
   * The event being reported.
   *
   * Accepted values: UNKNOWN_EVENT, OS_START, CONTAINER_START, NETWORK_UP,
   * STAGING_FILES_DOWNLOAD_START, STAGING_FILES_DOWNLOAD_FINISH,
   * SDK_INSTALL_START, SDK_INSTALL_FINISH
   *
   * @param self::EVENT_* $event
   */
  public function setEvent($event)
  {
    $this->event = $event;
  }
  /**
   * @return self::EVENT_*
   */
  public function getEvent()
  {
    return $this->event;
  }
  /**
   * Other stats that can accompany an event. E.g. { "downloaded_bytes" :
   * "123456" }
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkerLifecycleEvent::class, 'Google_Service_Dataflow_WorkerLifecycleEvent');

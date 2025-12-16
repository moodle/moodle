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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3Handler extends \Google\Model
{
  protected $eventHandlerType = GoogleCloudDialogflowCxV3HandlerEventHandler::class;
  protected $eventHandlerDataType = '';
  protected $lifecycleHandlerType = GoogleCloudDialogflowCxV3HandlerLifecycleHandler::class;
  protected $lifecycleHandlerDataType = '';

  /**
   * A handler triggered by event.
   *
   * @param GoogleCloudDialogflowCxV3HandlerEventHandler $eventHandler
   */
  public function setEventHandler(GoogleCloudDialogflowCxV3HandlerEventHandler $eventHandler)
  {
    $this->eventHandler = $eventHandler;
  }
  /**
   * @return GoogleCloudDialogflowCxV3HandlerEventHandler
   */
  public function getEventHandler()
  {
    return $this->eventHandler;
  }
  /**
   * A handler triggered during specific lifecycle of the playbook execution.
   *
   * @param GoogleCloudDialogflowCxV3HandlerLifecycleHandler $lifecycleHandler
   */
  public function setLifecycleHandler(GoogleCloudDialogflowCxV3HandlerLifecycleHandler $lifecycleHandler)
  {
    $this->lifecycleHandler = $lifecycleHandler;
  }
  /**
   * @return GoogleCloudDialogflowCxV3HandlerLifecycleHandler
   */
  public function getLifecycleHandler()
  {
    return $this->lifecycleHandler;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Handler::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Handler');

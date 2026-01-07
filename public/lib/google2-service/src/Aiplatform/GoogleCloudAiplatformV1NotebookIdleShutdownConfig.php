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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1NotebookIdleShutdownConfig extends \Google\Model
{
  /**
   * Whether Idle Shutdown is disabled in this NotebookRuntimeTemplate.
   *
   * @var bool
   */
  public $idleShutdownDisabled;
  /**
   * Required. Duration is accurate to the second. In Notebook, Idle Timeout is
   * accurate to minute so the range of idle_timeout (second) is: 10 * 60 ~ 1440
   * * 60.
   *
   * @var string
   */
  public $idleTimeout;

  /**
   * Whether Idle Shutdown is disabled in this NotebookRuntimeTemplate.
   *
   * @param bool $idleShutdownDisabled
   */
  public function setIdleShutdownDisabled($idleShutdownDisabled)
  {
    $this->idleShutdownDisabled = $idleShutdownDisabled;
  }
  /**
   * @return bool
   */
  public function getIdleShutdownDisabled()
  {
    return $this->idleShutdownDisabled;
  }
  /**
   * Required. Duration is accurate to the second. In Notebook, Idle Timeout is
   * accurate to minute so the range of idle_timeout (second) is: 10 * 60 ~ 1440
   * * 60.
   *
   * @param string $idleTimeout
   */
  public function setIdleTimeout($idleTimeout)
  {
    $this->idleTimeout = $idleTimeout;
  }
  /**
   * @return string
   */
  public function getIdleTimeout()
  {
    return $this->idleTimeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NotebookIdleShutdownConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NotebookIdleShutdownConfig');

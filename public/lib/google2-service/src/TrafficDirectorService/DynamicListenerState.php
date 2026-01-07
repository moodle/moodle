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

namespace Google\Service\TrafficDirectorService;

class DynamicListenerState extends \Google\Model
{
  /**
   * The timestamp when the Listener was last successfully updated.
   *
   * @var string
   */
  public $lastUpdated;
  /**
   * The listener config.
   *
   * @var array[]
   */
  public $listener;
  /**
   * This is the per-resource version information. This version is currently
   * taken from the :ref:`version_info ` field at the time that the listener was
   * loaded. In the future, discrete per-listener versions may be supported by
   * the API.
   *
   * @var string
   */
  public $versionInfo;

  /**
   * The timestamp when the Listener was last successfully updated.
   *
   * @param string $lastUpdated
   */
  public function setLastUpdated($lastUpdated)
  {
    $this->lastUpdated = $lastUpdated;
  }
  /**
   * @return string
   */
  public function getLastUpdated()
  {
    return $this->lastUpdated;
  }
  /**
   * The listener config.
   *
   * @param array[] $listener
   */
  public function setListener($listener)
  {
    $this->listener = $listener;
  }
  /**
   * @return array[]
   */
  public function getListener()
  {
    return $this->listener;
  }
  /**
   * This is the per-resource version information. This version is currently
   * taken from the :ref:`version_info ` field at the time that the listener was
   * loaded. In the future, discrete per-listener versions may be supported by
   * the API.
   *
   * @param string $versionInfo
   */
  public function setVersionInfo($versionInfo)
  {
    $this->versionInfo = $versionInfo;
  }
  /**
   * @return string
   */
  public function getVersionInfo()
  {
    return $this->versionInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicListenerState::class, 'Google_Service_TrafficDirectorService_DynamicListenerState');

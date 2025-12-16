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

class ListenersConfigDump extends \Google\Collection
{
  protected $collection_key = 'staticListeners';
  protected $dynamicListenersType = DynamicListener::class;
  protected $dynamicListenersDataType = 'array';
  protected $staticListenersType = StaticListener::class;
  protected $staticListenersDataType = 'array';
  /**
   * This is the :ref:`version_info ` in the last processed LDS discovery
   * response. If there are only static bootstrap listeners, this field will be
   * "".
   *
   * @var string
   */
  public $versionInfo;

  /**
   * State for any warming, active, or draining listeners.
   *
   * @param DynamicListener[] $dynamicListeners
   */
  public function setDynamicListeners($dynamicListeners)
  {
    $this->dynamicListeners = $dynamicListeners;
  }
  /**
   * @return DynamicListener[]
   */
  public function getDynamicListeners()
  {
    return $this->dynamicListeners;
  }
  /**
   * The statically loaded listener configs.
   *
   * @param StaticListener[] $staticListeners
   */
  public function setStaticListeners($staticListeners)
  {
    $this->staticListeners = $staticListeners;
  }
  /**
   * @return StaticListener[]
   */
  public function getStaticListeners()
  {
    return $this->staticListeners;
  }
  /**
   * This is the :ref:`version_info ` in the last processed LDS discovery
   * response. If there are only static bootstrap listeners, this field will be
   * "".
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
class_alias(ListenersConfigDump::class, 'Google_Service_TrafficDirectorService_ListenersConfigDump');

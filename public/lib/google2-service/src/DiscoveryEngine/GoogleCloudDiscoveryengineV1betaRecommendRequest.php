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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaRecommendRequest extends \Google\Model
{
  /**
   * @var string
   */
  public $filter;
  /**
   * @var int
   */
  public $pageSize;
  /**
   * @var array[]
   */
  public $params;
  protected $userEventType = GoogleCloudDiscoveryengineV1betaUserEvent::class;
  protected $userEventDataType = '';
  /**
   * @var string[]
   */
  public $userLabels;
  /**
   * @var bool
   */
  public $validateOnly;

  /**
   * @param string
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * @param int
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * @param array[]
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return array[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaUserEvent
   */
  public function setUserEvent(GoogleCloudDiscoveryengineV1betaUserEvent $userEvent)
  {
    $this->userEvent = $userEvent;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaUserEvent
   */
  public function getUserEvent()
  {
    return $this->userEvent;
  }
  /**
   * @param string[]
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
  /**
   * @param bool
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaRecommendRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaRecommendRequest');

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

namespace Google\Service\GKEHub;

class PolicyControllerResourceRequirements extends \Google\Model
{
  protected $limitsType = PolicyControllerResourceList::class;
  protected $limitsDataType = '';
  protected $requestsType = PolicyControllerResourceList::class;
  protected $requestsDataType = '';

  /**
   * Limits describes the maximum amount of compute resources allowed for use by
   * the running container.
   *
   * @param PolicyControllerResourceList $limits
   */
  public function setLimits(PolicyControllerResourceList $limits)
  {
    $this->limits = $limits;
  }
  /**
   * @return PolicyControllerResourceList
   */
  public function getLimits()
  {
    return $this->limits;
  }
  /**
   * Requests describes the amount of compute resources reserved for the
   * container by the kube-scheduler.
   *
   * @param PolicyControllerResourceList $requests
   */
  public function setRequests(PolicyControllerResourceList $requests)
  {
    $this->requests = $requests;
  }
  /**
   * @return PolicyControllerResourceList
   */
  public function getRequests()
  {
    return $this->requests;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyControllerResourceRequirements::class, 'Google_Service_GKEHub_PolicyControllerResourceRequirements');

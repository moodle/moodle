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

namespace Google\Service\NetworkServices;

class ServiceLbPolicyAutoCapacityDrain extends \Google\Model
{
  /**
   * Optional. If set to 'True', an unhealthy IG/NEG will be set as drained. -
   * An IG/NEG is considered unhealthy if less than 25% of the
   * instances/endpoints in the IG/NEG are healthy. - This option will never
   * result in draining more than 50% of the configured IGs/NEGs for the Backend
   * Service.
   *
   * @var bool
   */
  public $enable;

  /**
   * Optional. If set to 'True', an unhealthy IG/NEG will be set as drained. -
   * An IG/NEG is considered unhealthy if less than 25% of the
   * instances/endpoints in the IG/NEG are healthy. - This option will never
   * result in draining more than 50% of the configured IGs/NEGs for the Backend
   * Service.
   *
   * @param bool $enable
   */
  public function setEnable($enable)
  {
    $this->enable = $enable;
  }
  /**
   * @return bool
   */
  public function getEnable()
  {
    return $this->enable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceLbPolicyAutoCapacityDrain::class, 'Google_Service_NetworkServices_ServiceLbPolicyAutoCapacityDrain');

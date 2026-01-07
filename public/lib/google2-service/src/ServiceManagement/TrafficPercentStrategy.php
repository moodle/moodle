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

namespace Google\Service\ServiceManagement;

class TrafficPercentStrategy extends \Google\Model
{
  /**
   * Maps service configuration IDs to their corresponding traffic percentage.
   * Key is the service configuration ID, Value is the traffic percentage which
   * must be greater than 0.0 and the sum must equal to 100.0.
   *
   * @var []
   */
  public $percentages;

  public function setPercentages($percentages)
  {
    $this->percentages = $percentages;
  }
  public function getPercentages()
  {
    return $this->percentages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrafficPercentStrategy::class, 'Google_Service_ServiceManagement_TrafficPercentStrategy');

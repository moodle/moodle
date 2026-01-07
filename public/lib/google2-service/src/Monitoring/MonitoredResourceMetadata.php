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

namespace Google\Service\Monitoring;

class MonitoredResourceMetadata extends \Google\Model
{
  /**
   * Output only. Values for predefined system metadata labels. System labels
   * are a kind of metadata extracted by Google, including "machine_image",
   * "vpc", "subnet_id", "security_group", "name", etc. System label values can
   * be only strings, Boolean values, or a list of strings. For example: {
   * "name": "my-test-instance", "security_group": ["a", "b", "c"],
   * "spot_instance": false }
   *
   * @var array[]
   */
  public $systemLabels;
  /**
   * Output only. A map of user-defined metadata labels.
   *
   * @var string[]
   */
  public $userLabels;

  /**
   * Output only. Values for predefined system metadata labels. System labels
   * are a kind of metadata extracted by Google, including "machine_image",
   * "vpc", "subnet_id", "security_group", "name", etc. System label values can
   * be only strings, Boolean values, or a list of strings. For example: {
   * "name": "my-test-instance", "security_group": ["a", "b", "c"],
   * "spot_instance": false }
   *
   * @param array[] $systemLabels
   */
  public function setSystemLabels($systemLabels)
  {
    $this->systemLabels = $systemLabels;
  }
  /**
   * @return array[]
   */
  public function getSystemLabels()
  {
    return $this->systemLabels;
  }
  /**
   * Output only. A map of user-defined metadata labels.
   *
   * @param string[] $userLabels
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MonitoredResourceMetadata::class, 'Google_Service_Monitoring_MonitoredResourceMetadata');

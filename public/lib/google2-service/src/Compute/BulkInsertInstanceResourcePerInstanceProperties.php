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

namespace Google\Service\Compute;

class BulkInsertInstanceResourcePerInstanceProperties extends \Google\Model
{
  /**
   * Specifies the hostname of the instance. More details in:
   * https://cloud.google.com/compute/docs/instances/custom-hostname-
   * vm#naming_convention
   *
   * @var string
   */
  public $hostname;
  /**
   * Output only. This field is only temporary. It will be removed. Do not use
   * it.
   *
   * @var string
   */
  public $name;

  /**
   * Specifies the hostname of the instance. More details in:
   * https://cloud.google.com/compute/docs/instances/custom-hostname-
   * vm#naming_convention
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * Output only. This field is only temporary. It will be removed. Do not use
   * it.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkInsertInstanceResourcePerInstanceProperties::class, 'Google_Service_Compute_BulkInsertInstanceResourcePerInstanceProperties');

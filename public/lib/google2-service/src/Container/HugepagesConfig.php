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

namespace Google\Service\Container;

class HugepagesConfig extends \Google\Model
{
  /**
   * Optional. Amount of 1G hugepages
   *
   * @var int
   */
  public $hugepageSize1g;
  /**
   * Optional. Amount of 2M hugepages
   *
   * @var int
   */
  public $hugepageSize2m;

  /**
   * Optional. Amount of 1G hugepages
   *
   * @param int $hugepageSize1g
   */
  public function setHugepageSize1g($hugepageSize1g)
  {
    $this->hugepageSize1g = $hugepageSize1g;
  }
  /**
   * @return int
   */
  public function getHugepageSize1g()
  {
    return $this->hugepageSize1g;
  }
  /**
   * Optional. Amount of 2M hugepages
   *
   * @param int $hugepageSize2m
   */
  public function setHugepageSize2m($hugepageSize2m)
  {
    $this->hugepageSize2m = $hugepageSize2m;
  }
  /**
   * @return int
   */
  public function getHugepageSize2m()
  {
    return $this->hugepageSize2m;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HugepagesConfig::class, 'Google_Service_Container_HugepagesConfig');

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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2ProductAttributeInterval extends \Google\Model
{
  protected $intervalType = GoogleCloudRetailV2Interval::class;
  protected $intervalDataType = '';
  /**
   * The attribute name (e.g. "length")
   *
   * @var string
   */
  public $name;

  /**
   * The numeric interval (e.g. [10, 20))
   *
   * @param GoogleCloudRetailV2Interval $interval
   */
  public function setInterval(GoogleCloudRetailV2Interval $interval)
  {
    $this->interval = $interval;
  }
  /**
   * @return GoogleCloudRetailV2Interval
   */
  public function getInterval()
  {
    return $this->interval;
  }
  /**
   * The attribute name (e.g. "length")
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
class_alias(GoogleCloudRetailV2ProductAttributeInterval::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ProductAttributeInterval');

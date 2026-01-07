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

namespace Google\Service\DisplayVideo;

class CounterEvent extends \Google\Model
{
  /**
   * Required. The name of the counter event.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The name used to identify this counter event in reports.
   *
   * @var string
   */
  public $reportingName;

  /**
   * Required. The name of the counter event.
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
  /**
   * Required. The name used to identify this counter event in reports.
   *
   * @param string $reportingName
   */
  public function setReportingName($reportingName)
  {
    $this->reportingName = $reportingName;
  }
  /**
   * @return string
   */
  public function getReportingName()
  {
    return $this->reportingName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CounterEvent::class, 'Google_Service_DisplayVideo_CounterEvent');

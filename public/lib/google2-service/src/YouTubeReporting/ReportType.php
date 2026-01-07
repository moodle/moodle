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

namespace Google\Service\YouTubeReporting;

class ReportType extends \Google\Model
{
  /**
   * The date/time when this report type was/will be deprecated.
   *
   * @var string
   */
  public $deprecateTime;
  /**
   * The ID of the report type (max. 100 characters).
   *
   * @var string
   */
  public $id;
  /**
   * The name of the report type (max. 100 characters).
   *
   * @var string
   */
  public $name;
  /**
   * True if this a system-managed report type; otherwise false. Reporting jobs
   * for system-managed report types are created automatically and can thus not
   * be used in the `CreateJob` method.
   *
   * @var bool
   */
  public $systemManaged;

  /**
   * The date/time when this report type was/will be deprecated.
   *
   * @param string $deprecateTime
   */
  public function setDeprecateTime($deprecateTime)
  {
    $this->deprecateTime = $deprecateTime;
  }
  /**
   * @return string
   */
  public function getDeprecateTime()
  {
    return $this->deprecateTime;
  }
  /**
   * The ID of the report type (max. 100 characters).
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The name of the report type (max. 100 characters).
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
   * True if this a system-managed report type; otherwise false. Reporting jobs
   * for system-managed report types are created automatically and can thus not
   * be used in the `CreateJob` method.
   *
   * @param bool $systemManaged
   */
  public function setSystemManaged($systemManaged)
  {
    $this->systemManaged = $systemManaged;
  }
  /**
   * @return bool
   */
  public function getSystemManaged()
  {
    return $this->systemManaged;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportType::class, 'Google_Service_YouTubeReporting_ReportType');

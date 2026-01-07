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

class Job extends \Google\Model
{
  /**
   * The creation date/time of the job.
   *
   * @var string
   */
  public $createTime;
  /**
   * The date/time when this job will expire/expired. After a job expired, no
   * new reports are generated.
   *
   * @var string
   */
  public $expireTime;
  /**
   * The server-generated ID of the job (max. 40 characters).
   *
   * @var string
   */
  public $id;
  /**
   * The name of the job (max. 100 characters).
   *
   * @var string
   */
  public $name;
  /**
   * The type of reports this job creates. Corresponds to the ID of a
   * ReportType.
   *
   * @var string
   */
  public $reportTypeId;
  /**
   * True if this a system-managed job that cannot be modified by the user;
   * otherwise false.
   *
   * @var bool
   */
  public $systemManaged;

  /**
   * The creation date/time of the job.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The date/time when this job will expire/expired. After a job expired, no
   * new reports are generated.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * The server-generated ID of the job (max. 40 characters).
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
   * The name of the job (max. 100 characters).
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
   * The type of reports this job creates. Corresponds to the ID of a
   * ReportType.
   *
   * @param string $reportTypeId
   */
  public function setReportTypeId($reportTypeId)
  {
    $this->reportTypeId = $reportTypeId;
  }
  /**
   * @return string
   */
  public function getReportTypeId()
  {
    return $this->reportTypeId;
  }
  /**
   * True if this a system-managed job that cannot be modified by the user;
   * otherwise false.
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
class_alias(Job::class, 'Google_Service_YouTubeReporting_Job');

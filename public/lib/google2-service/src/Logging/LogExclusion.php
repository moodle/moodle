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

namespace Google\Service\Logging;

class LogExclusion extends \Google\Model
{
  /**
   * Output only. The creation timestamp of the exclusion.This field may not be
   * present for older exclusions.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A description of this exclusion.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. If set to True, then this exclusion is disabled and it does not
   * exclude any log entries. You can update an exclusion to change the value of
   * this field.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Required. An advanced logs filter
   * (https://cloud.google.com/logging/docs/view/advanced-queries) that matches
   * the log entries to be excluded. By using the sample function
   * (https://cloud.google.com/logging/docs/view/advanced-queries#sample), you
   * can exclude less than 100% of the matching log entries.For example, the
   * following query matches 99% of low-severity log entries from Google Cloud
   * Storage buckets:resource.type=gcs_bucket severity
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. A client-assigned identifier, such as "load-balancer-exclusion".
   * Identifiers are limited to 100 characters and can include only letters,
   * digits, underscores, hyphens, and periods. First character has to be
   * alphanumeric.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The last update timestamp of the exclusion.This field may not
   * be present for older exclusions.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The creation timestamp of the exclusion.This field may not be
   * present for older exclusions.
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
   * Optional. A description of this exclusion.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. If set to True, then this exclusion is disabled and it does not
   * exclude any log entries. You can update an exclusion to change the value of
   * this field.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Required. An advanced logs filter
   * (https://cloud.google.com/logging/docs/view/advanced-queries) that matches
   * the log entries to be excluded. By using the sample function
   * (https://cloud.google.com/logging/docs/view/advanced-queries#sample), you
   * can exclude less than 100% of the matching log entries.For example, the
   * following query matches 99% of low-severity log entries from Google Cloud
   * Storage buckets:resource.type=gcs_bucket severity
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. A client-assigned identifier, such as "load-balancer-exclusion".
   * Identifiers are limited to 100 characters and can include only letters,
   * digits, underscores, hyphens, and periods. First character has to be
   * alphanumeric.
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
   * Output only. The last update timestamp of the exclusion.This field may not
   * be present for older exclusions.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LogExclusion::class, 'Google_Service_Logging_LogExclusion');

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

namespace Google\Service\MigrationCenterAPI;

class Report extends \Google\Model
{
  /**
   * Default Report creation state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Creating Report.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Successfully created Report.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Failed to create Report.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Default Report type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Total cost of ownership Report type.
   */
  public const TYPE_TOTAL_COST_OF_OWNERSHIP = 'TOTAL_COST_OF_OWNERSHIP';
  /**
   * Output only. Creation timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Free-text description.
   *
   * @var string
   */
  public $description;
  /**
   * User-friendly display name. Maximum length is 63 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Name of resource.
   *
   * @var string
   */
  public $name;
  /**
   * Report creation state.
   *
   * @var string
   */
  public $state;
  protected $summaryType = ReportSummary::class;
  protected $summaryDataType = '';
  /**
   * Report type.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Last update timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Creation timestamp.
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
   * Free-text description.
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
   * User-friendly display name. Maximum length is 63 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Name of resource.
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
   * Report creation state.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, SUCCEEDED, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Summary view of the Report.
   *
   * @param ReportSummary $summary
   */
  public function setSummary(ReportSummary $summary)
  {
    $this->summary = $summary;
  }
  /**
   * @return ReportSummary
   */
  public function getSummary()
  {
    return $this->summary;
  }
  /**
   * Report type.
   *
   * Accepted values: TYPE_UNSPECIFIED, TOTAL_COST_OF_OWNERSHIP
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. Last update timestamp.
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
class_alias(Report::class, 'Google_Service_MigrationCenterAPI_Report');

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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1mainDimensionIssueDimensionMetadata extends \Google\Model
{
  /**
   * The issue display name.
   *
   * @var string
   */
  public $issueDisplayName;
  /**
   * The issue ID.
   *
   * @var string
   */
  public $issueId;
  /**
   * The parent issue model ID.
   *
   * @var string
   */
  public $issueModelId;

  /**
   * The issue display name.
   *
   * @param string $issueDisplayName
   */
  public function setIssueDisplayName($issueDisplayName)
  {
    $this->issueDisplayName = $issueDisplayName;
  }
  /**
   * @return string
   */
  public function getIssueDisplayName()
  {
    return $this->issueDisplayName;
  }
  /**
   * The issue ID.
   *
   * @param string $issueId
   */
  public function setIssueId($issueId)
  {
    $this->issueId = $issueId;
  }
  /**
   * @return string
   */
  public function getIssueId()
  {
    return $this->issueId;
  }
  /**
   * The parent issue model ID.
   *
   * @param string $issueModelId
   */
  public function setIssueModelId($issueModelId)
  {
    $this->issueModelId = $issueModelId;
  }
  /**
   * @return string
   */
  public function getIssueModelId()
  {
    return $this->issueModelId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainDimensionIssueDimensionMetadata::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainDimensionIssueDimensionMetadata');

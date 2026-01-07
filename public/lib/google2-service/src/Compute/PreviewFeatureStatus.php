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

class PreviewFeatureStatus extends \Google\Model
{
  /**
   * Output only. [Output Only] The description of the feature.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. [Output Only] Link to the public documentation for the
   * feature.
   *
   * @var string
   */
  public $helpLink;
  protected $releaseStatusType = PreviewFeatureStatusReleaseStatus::class;
  protected $releaseStatusDataType = '';

  /**
   * Output only. [Output Only] The description of the feature.
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
   * Output only. [Output Only] Link to the public documentation for the
   * feature.
   *
   * @param string $helpLink
   */
  public function setHelpLink($helpLink)
  {
    $this->helpLink = $helpLink;
  }
  /**
   * @return string
   */
  public function getHelpLink()
  {
    return $this->helpLink;
  }
  /**
   * @param PreviewFeatureStatusReleaseStatus $releaseStatus
   */
  public function setReleaseStatus(PreviewFeatureStatusReleaseStatus $releaseStatus)
  {
    $this->releaseStatus = $releaseStatus;
  }
  /**
   * @return PreviewFeatureStatusReleaseStatus
   */
  public function getReleaseStatus()
  {
    return $this->releaseStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreviewFeatureStatus::class, 'Google_Service_Compute_PreviewFeatureStatus');

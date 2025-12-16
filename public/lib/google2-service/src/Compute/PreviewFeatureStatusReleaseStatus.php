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

class PreviewFeatureStatusReleaseStatus extends \Google\Model
{
  public const STAGE_DEPRECATED = 'DEPRECATED';
  public const STAGE_GA = 'GA';
  public const STAGE_PREVIEW = 'PREVIEW';
  public const STAGE_STAGE_UNSPECIFIED = 'STAGE_UNSPECIFIED';
  /**
   * Output only. [Output Only] The stage of the feature.
   *
   * @var string
   */
  public $stage;
  protected $updateDateType = Date::class;
  protected $updateDateDataType = '';

  /**
   * Output only. [Output Only] The stage of the feature.
   *
   * Accepted values: DEPRECATED, GA, PREVIEW, STAGE_UNSPECIFIED
   *
   * @param self::STAGE_* $stage
   */
  public function setStage($stage)
  {
    $this->stage = $stage;
  }
  /**
   * @return self::STAGE_*
   */
  public function getStage()
  {
    return $this->stage;
  }
  /**
   * Output only. The last date when a feature transitioned between
   * ReleaseStatuses.
   *
   * @param Date $updateDate
   */
  public function setUpdateDate(Date $updateDate)
  {
    $this->updateDate = $updateDate;
  }
  /**
   * @return Date
   */
  public function getUpdateDate()
  {
    return $this->updateDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreviewFeatureStatusReleaseStatus::class, 'Google_Service_Compute_PreviewFeatureStatusReleaseStatus');

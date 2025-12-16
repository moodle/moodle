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

class UserRewardedContentAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * User rewarded content is not specified or is unknown in this version.
   */
  public const USER_REWARDED_CONTENT_USER_REWARDED_CONTENT_UNSPECIFIED = 'USER_REWARDED_CONTENT_UNSPECIFIED';
  /**
   * Represents ads where the user will see a reward after viewing.
   */
  public const USER_REWARDED_CONTENT_USER_REWARDED_CONTENT_USER_REWARDED = 'USER_REWARDED_CONTENT_USER_REWARDED';
  /**
   * Represents all other ads besides user-rewarded.
   */
  public const USER_REWARDED_CONTENT_USER_REWARDED_CONTENT_NOT_USER_REWARDED = 'USER_REWARDED_CONTENT_NOT_USER_REWARDED';
  /**
   * Required. The targeting_option_id field when targeting_type is
   * `TARGETING_TYPE_USER_REWARDED_CONTENT`.
   *
   * @var string
   */
  public $targetingOptionId;
  /**
   * Output only. User rewarded content status for video ads.
   *
   * @var string
   */
  public $userRewardedContent;

  /**
   * Required. The targeting_option_id field when targeting_type is
   * `TARGETING_TYPE_USER_REWARDED_CONTENT`.
   *
   * @param string $targetingOptionId
   */
  public function setTargetingOptionId($targetingOptionId)
  {
    $this->targetingOptionId = $targetingOptionId;
  }
  /**
   * @return string
   */
  public function getTargetingOptionId()
  {
    return $this->targetingOptionId;
  }
  /**
   * Output only. User rewarded content status for video ads.
   *
   * Accepted values: USER_REWARDED_CONTENT_UNSPECIFIED,
   * USER_REWARDED_CONTENT_USER_REWARDED,
   * USER_REWARDED_CONTENT_NOT_USER_REWARDED
   *
   * @param self::USER_REWARDED_CONTENT_* $userRewardedContent
   */
  public function setUserRewardedContent($userRewardedContent)
  {
    $this->userRewardedContent = $userRewardedContent;
  }
  /**
   * @return self::USER_REWARDED_CONTENT_*
   */
  public function getUserRewardedContent()
  {
    return $this->userRewardedContent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserRewardedContentAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_UserRewardedContentAssignedTargetingOptionDetails');

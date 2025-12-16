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

class AdPolicyTopicAppealInfo extends \Google\Model
{
  /**
   * Unknown or not specified.
   */
  public const APPEAL_TYPE_AD_POLICY_APPEAL_TYPE_UNKNOWN = 'AD_POLICY_APPEAL_TYPE_UNKNOWN';
  /**
   * The decision can be appealed through a self-service appeal.
   */
  public const APPEAL_TYPE_SELF_SERVICE_APPEAL = 'SELF_SERVICE_APPEAL';
  /**
   * The decision can be appealed using an appeal form.
   */
  public const APPEAL_TYPE_APPEAL_FORM = 'APPEAL_FORM';
  /**
   * Only available when appeal_type is `APPEAL_FORM`.
   *
   * @var string
   */
  public $appealFormLink;
  /**
   * Whether the decision can be appealed through a self-service appeal or an
   * appeal form.
   *
   * @var string
   */
  public $appealType;

  /**
   * Only available when appeal_type is `APPEAL_FORM`.
   *
   * @param string $appealFormLink
   */
  public function setAppealFormLink($appealFormLink)
  {
    $this->appealFormLink = $appealFormLink;
  }
  /**
   * @return string
   */
  public function getAppealFormLink()
  {
    return $this->appealFormLink;
  }
  /**
   * Whether the decision can be appealed through a self-service appeal or an
   * appeal form.
   *
   * Accepted values: AD_POLICY_APPEAL_TYPE_UNKNOWN, SELF_SERVICE_APPEAL,
   * APPEAL_FORM
   *
   * @param self::APPEAL_TYPE_* $appealType
   */
  public function setAppealType($appealType)
  {
    $this->appealType = $appealType;
  }
  /**
   * @return self::APPEAL_TYPE_*
   */
  public function getAppealType()
  {
    return $this->appealType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdPolicyTopicAppealInfo::class, 'Google_Service_DisplayVideo_AdPolicyTopicAppealInfo');

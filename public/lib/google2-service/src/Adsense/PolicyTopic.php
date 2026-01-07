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

namespace Google\Service\Adsense;

class PolicyTopic extends \Google\Model
{
  /**
   * The type is unspecified.
   */
  public const TYPE_POLICY_TOPIC_TYPE_UNSPECIFIED = 'POLICY_TOPIC_TYPE_UNSPECIFIED';
  /**
   * Topics that are primarily related to the Google Publisher Policy (GPP)
   * (https://support.google.com/publisherpolicies/answer/10502938) or the
   * Google Publisher Restrictions (GPR) policies
   * (https://support.google.com/publisherpolicies/answer/10437795).
   */
  public const TYPE_POLICY = 'POLICY';
  /**
   * Topics that are related to advertiser preferences. Certain advertisers may
   * choose not to bid on content that are labeled with certain policies.
   */
  public const TYPE_ADVERTISER_PREFERENCE = 'ADVERTISER_PREFERENCE';
  /**
   * Any topics that are a result of a country or regional regulatory
   * requirement body.
   */
  public const TYPE_REGULATORY = 'REGULATORY';
  /**
   * Required. Deprecated. Always set to false.
   *
   * @deprecated
   * @var bool
   */
  public $mustFix;
  /**
   * Required. The policy topic. For example, "sexual-content" or "ads-
   * obscuring-content"."
   *
   * @var string
   */
  public $topic;
  /**
   * Optional. The type of policy topic. For example, "POLICY" represents all
   * the policy topics that are related to the Google Publisher Policy (GPP).
   * See https://support.google.com/adsense/answer/15689616.
   *
   * @var string
   */
  public $type;

  /**
   * Required. Deprecated. Always set to false.
   *
   * @deprecated
   * @param bool $mustFix
   */
  public function setMustFix($mustFix)
  {
    $this->mustFix = $mustFix;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getMustFix()
  {
    return $this->mustFix;
  }
  /**
   * Required. The policy topic. For example, "sexual-content" or "ads-
   * obscuring-content"."
   *
   * @param string $topic
   */
  public function setTopic($topic)
  {
    $this->topic = $topic;
  }
  /**
   * @return string
   */
  public function getTopic()
  {
    return $this->topic;
  }
  /**
   * Optional. The type of policy topic. For example, "POLICY" represents all
   * the policy topics that are related to the Google Publisher Policy (GPP).
   * See https://support.google.com/adsense/answer/15689616.
   *
   * Accepted values: POLICY_TOPIC_TYPE_UNSPECIFIED, POLICY,
   * ADVERTISER_PREFERENCE, REGULATORY
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyTopic::class, 'Google_Service_Adsense_PolicyTopic');

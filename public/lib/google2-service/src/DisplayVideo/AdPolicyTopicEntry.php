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

class AdPolicyTopicEntry extends \Google\Collection
{
  /**
   * Unknown or not specified.
   */
  public const POLICY_DECISION_TYPE_AD_POLICY_DECISION_TYPE_UNKNOWN = 'AD_POLICY_DECISION_TYPE_UNKNOWN';
  /**
   * The decision is from a legal notice, court order, or trademark content
   * owner complaint, etc.
   */
  public const POLICY_DECISION_TYPE_PURSUANT_TO_NOTICE = 'PURSUANT_TO_NOTICE';
  /**
   * The decision is from a Google-owned investigation.
   */
  public const POLICY_DECISION_TYPE_GOOGLE_INVESTIGATION = 'GOOGLE_INVESTIGATION';
  /**
   * Unknown or not specified.
   */
  public const POLICY_ENFORCEMENT_MEANS_AD_POLICY_ENFORCEMENT_MEANS_UNKNOWN = 'AD_POLICY_ENFORCEMENT_MEANS_UNKNOWN';
  /**
   * The enforcement process was fully automated.
   */
  public const POLICY_ENFORCEMENT_MEANS_AUTOMATED = 'AUTOMATED';
  /**
   * A human was partially or fully involved in the decision enforcement
   * process.
   */
  public const POLICY_ENFORCEMENT_MEANS_HUMAN_REVIEW = 'HUMAN_REVIEW';
  /**
   * Unknown or not specified.
   */
  public const POLICY_TOPIC_TYPE_AD_POLICY_TOPIC_ENTRY_TYPE_UNKNOWN = 'AD_POLICY_TOPIC_ENTRY_TYPE_UNKNOWN';
  /**
   * The resource will not serve.
   */
  public const POLICY_TOPIC_TYPE_PROHIBITED = 'PROHIBITED';
  /**
   * The resource will not serve in all targeted countries.
   */
  public const POLICY_TOPIC_TYPE_FULLY_LIMITED = 'FULLY_LIMITED';
  /**
   * The resource cannot serve in some countries.
   */
  public const POLICY_TOPIC_TYPE_LIMITED = 'LIMITED';
  /**
   * The resource can serve.
   */
  public const POLICY_TOPIC_TYPE_DESCRIPTIVE = 'DESCRIPTIVE';
  /**
   * The resource can serve, and may serve beyond normal coverage.
   */
  public const POLICY_TOPIC_TYPE_BROADENING = 'BROADENING';
  /**
   * The resource is constrained for all targeted countries, but may serve for
   * users who are searching for information about the targeted countries.
   */
  public const POLICY_TOPIC_TYPE_AREA_OF_INTEREST_ONLY = 'AREA_OF_INTEREST_ONLY';
  protected $collection_key = 'policyTopicEvidences';
  protected $appealInfoType = AdPolicyTopicAppealInfo::class;
  protected $appealInfoDataType = '';
  /**
   * Ad policy help center link for the policy topic.
   *
   * @var string
   */
  public $helpCenterLink;
  /**
   * The source of the policy decision.
   *
   * @var string
   */
  public $policyDecisionType;
  /**
   * The policy enforcement means used in the policy review.
   *
   * @var string
   */
  public $policyEnforcementMeans;
  /**
   * Localized label text for policy. Examples include "Trademarks in text",
   * "Contains Alcohol", etc.
   *
   * @var string
   */
  public $policyLabel;
  /**
   * The policy topic. Examples include "TRADEMARKS", "ALCOHOL", etc.
   *
   * @var string
   */
  public $policyTopic;
  protected $policyTopicConstraintsType = AdPolicyTopicConstraint::class;
  protected $policyTopicConstraintsDataType = 'array';
  /**
   * A short summary description of the policy topic.
   *
   * @var string
   */
  public $policyTopicDescription;
  protected $policyTopicEvidencesType = AdPolicyTopicEvidence::class;
  protected $policyTopicEvidencesDataType = 'array';
  /**
   * How ad serving will be affected due to the relation to the ad policy topic.
   *
   * @var string
   */
  public $policyTopicType;

  /**
   * Information on how to appeal the policy decision.
   *
   * @param AdPolicyTopicAppealInfo $appealInfo
   */
  public function setAppealInfo(AdPolicyTopicAppealInfo $appealInfo)
  {
    $this->appealInfo = $appealInfo;
  }
  /**
   * @return AdPolicyTopicAppealInfo
   */
  public function getAppealInfo()
  {
    return $this->appealInfo;
  }
  /**
   * Ad policy help center link for the policy topic.
   *
   * @param string $helpCenterLink
   */
  public function setHelpCenterLink($helpCenterLink)
  {
    $this->helpCenterLink = $helpCenterLink;
  }
  /**
   * @return string
   */
  public function getHelpCenterLink()
  {
    return $this->helpCenterLink;
  }
  /**
   * The source of the policy decision.
   *
   * Accepted values: AD_POLICY_DECISION_TYPE_UNKNOWN, PURSUANT_TO_NOTICE,
   * GOOGLE_INVESTIGATION
   *
   * @param self::POLICY_DECISION_TYPE_* $policyDecisionType
   */
  public function setPolicyDecisionType($policyDecisionType)
  {
    $this->policyDecisionType = $policyDecisionType;
  }
  /**
   * @return self::POLICY_DECISION_TYPE_*
   */
  public function getPolicyDecisionType()
  {
    return $this->policyDecisionType;
  }
  /**
   * The policy enforcement means used in the policy review.
   *
   * Accepted values: AD_POLICY_ENFORCEMENT_MEANS_UNKNOWN, AUTOMATED,
   * HUMAN_REVIEW
   *
   * @param self::POLICY_ENFORCEMENT_MEANS_* $policyEnforcementMeans
   */
  public function setPolicyEnforcementMeans($policyEnforcementMeans)
  {
    $this->policyEnforcementMeans = $policyEnforcementMeans;
  }
  /**
   * @return self::POLICY_ENFORCEMENT_MEANS_*
   */
  public function getPolicyEnforcementMeans()
  {
    return $this->policyEnforcementMeans;
  }
  /**
   * Localized label text for policy. Examples include "Trademarks in text",
   * "Contains Alcohol", etc.
   *
   * @param string $policyLabel
   */
  public function setPolicyLabel($policyLabel)
  {
    $this->policyLabel = $policyLabel;
  }
  /**
   * @return string
   */
  public function getPolicyLabel()
  {
    return $this->policyLabel;
  }
  /**
   * The policy topic. Examples include "TRADEMARKS", "ALCOHOL", etc.
   *
   * @param string $policyTopic
   */
  public function setPolicyTopic($policyTopic)
  {
    $this->policyTopic = $policyTopic;
  }
  /**
   * @return string
   */
  public function getPolicyTopic()
  {
    return $this->policyTopic;
  }
  /**
   * The serving constraints relevant to the policy decision.
   *
   * @param AdPolicyTopicConstraint[] $policyTopicConstraints
   */
  public function setPolicyTopicConstraints($policyTopicConstraints)
  {
    $this->policyTopicConstraints = $policyTopicConstraints;
  }
  /**
   * @return AdPolicyTopicConstraint[]
   */
  public function getPolicyTopicConstraints()
  {
    return $this->policyTopicConstraints;
  }
  /**
   * A short summary description of the policy topic.
   *
   * @param string $policyTopicDescription
   */
  public function setPolicyTopicDescription($policyTopicDescription)
  {
    $this->policyTopicDescription = $policyTopicDescription;
  }
  /**
   * @return string
   */
  public function getPolicyTopicDescription()
  {
    return $this->policyTopicDescription;
  }
  /**
   * The evidence used in the policy decision.
   *
   * @param AdPolicyTopicEvidence[] $policyTopicEvidences
   */
  public function setPolicyTopicEvidences($policyTopicEvidences)
  {
    $this->policyTopicEvidences = $policyTopicEvidences;
  }
  /**
   * @return AdPolicyTopicEvidence[]
   */
  public function getPolicyTopicEvidences()
  {
    return $this->policyTopicEvidences;
  }
  /**
   * How ad serving will be affected due to the relation to the ad policy topic.
   *
   * Accepted values: AD_POLICY_TOPIC_ENTRY_TYPE_UNKNOWN, PROHIBITED,
   * FULLY_LIMITED, LIMITED, DESCRIPTIVE, BROADENING, AREA_OF_INTEREST_ONLY
   *
   * @param self::POLICY_TOPIC_TYPE_* $policyTopicType
   */
  public function setPolicyTopicType($policyTopicType)
  {
    $this->policyTopicType = $policyTopicType;
  }
  /**
   * @return self::POLICY_TOPIC_TYPE_*
   */
  public function getPolicyTopicType()
  {
    return $this->policyTopicType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdPolicyTopicEntry::class, 'Google_Service_DisplayVideo_AdPolicyTopicEntry');

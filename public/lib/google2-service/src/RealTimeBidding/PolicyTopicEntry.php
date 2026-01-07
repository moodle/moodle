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

namespace Google\Service\RealTimeBidding;

class PolicyTopicEntry extends \Google\Collection
{
  protected $collection_key = 'evidences';
  protected $evidencesType = PolicyTopicEvidence::class;
  protected $evidencesDataType = 'array';
  /**
   * URL of the help center article describing this policy topic.
   *
   * @var string
   */
  public $helpCenterUrl;
  /**
   * Whether or not the policy topic is missing a certificate. Some policy
   * topics require a certificate to unblock serving in some regions. For more
   * information about creative certification, refer to:
   * https://support.google.com/authorizedbuyers/answer/7450776
   *
   * @var bool
   */
  public $missingCertificate;
  /**
   * Policy topic this entry refers to. For example, "ALCOHOL",
   * "TRADEMARKS_IN_AD_TEXT", or "DESTINATION_NOT_WORKING". The set of possible
   * policy topics is not fixed for a particular API version and may change at
   * any time. Can be used to filter the response of the creatives.list method
   *
   * @var string
   */
  public $policyTopic;

  /**
   * Pieces of evidence associated with this policy topic entry.
   *
   * @param PolicyTopicEvidence[] $evidences
   */
  public function setEvidences($evidences)
  {
    $this->evidences = $evidences;
  }
  /**
   * @return PolicyTopicEvidence[]
   */
  public function getEvidences()
  {
    return $this->evidences;
  }
  /**
   * URL of the help center article describing this policy topic.
   *
   * @param string $helpCenterUrl
   */
  public function setHelpCenterUrl($helpCenterUrl)
  {
    $this->helpCenterUrl = $helpCenterUrl;
  }
  /**
   * @return string
   */
  public function getHelpCenterUrl()
  {
    return $this->helpCenterUrl;
  }
  /**
   * Whether or not the policy topic is missing a certificate. Some policy
   * topics require a certificate to unblock serving in some regions. For more
   * information about creative certification, refer to:
   * https://support.google.com/authorizedbuyers/answer/7450776
   *
   * @param bool $missingCertificate
   */
  public function setMissingCertificate($missingCertificate)
  {
    $this->missingCertificate = $missingCertificate;
  }
  /**
   * @return bool
   */
  public function getMissingCertificate()
  {
    return $this->missingCertificate;
  }
  /**
   * Policy topic this entry refers to. For example, "ALCOHOL",
   * "TRADEMARKS_IN_AD_TEXT", or "DESTINATION_NOT_WORKING". The set of possible
   * policy topics is not fixed for a particular API version and may change at
   * any time. Can be used to filter the response of the creatives.list method
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyTopicEntry::class, 'Google_Service_RealTimeBidding_PolicyTopicEntry');

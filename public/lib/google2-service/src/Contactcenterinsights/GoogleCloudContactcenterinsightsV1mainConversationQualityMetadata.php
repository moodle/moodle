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

class GoogleCloudContactcenterinsightsV1mainConversationQualityMetadata extends \Google\Collection
{
  protected $collection_key = 'feedbackLabels';
  protected $agentInfoType = GoogleCloudContactcenterinsightsV1mainConversationQualityMetadataAgentInfo::class;
  protected $agentInfoDataType = 'array';
  /**
   * An arbitrary integer value indicating the customer's satisfaction rating.
   *
   * @var int
   */
  public $customerSatisfactionRating;
  protected $feedbackLabelsType = GoogleCloudContactcenterinsightsV1mainFeedbackLabel::class;
  protected $feedbackLabelsDataType = 'array';
  /**
   * An arbitrary string value specifying the menu path the customer took.
   *
   * @var string
   */
  public $menuPath;
  /**
   * The amount of time the customer waited to connect with an agent.
   *
   * @var string
   */
  public $waitDuration;

  /**
   * Information about agents involved in the call.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversationQualityMetadataAgentInfo[] $agentInfo
   */
  public function setAgentInfo($agentInfo)
  {
    $this->agentInfo = $agentInfo;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversationQualityMetadataAgentInfo[]
   */
  public function getAgentInfo()
  {
    return $this->agentInfo;
  }
  /**
   * An arbitrary integer value indicating the customer's satisfaction rating.
   *
   * @param int $customerSatisfactionRating
   */
  public function setCustomerSatisfactionRating($customerSatisfactionRating)
  {
    $this->customerSatisfactionRating = $customerSatisfactionRating;
  }
  /**
   * @return int
   */
  public function getCustomerSatisfactionRating()
  {
    return $this->customerSatisfactionRating;
  }
  /**
   * Input only. The feedback labels associated with the conversation.
   *
   * @param GoogleCloudContactcenterinsightsV1mainFeedbackLabel[] $feedbackLabels
   */
  public function setFeedbackLabels($feedbackLabels)
  {
    $this->feedbackLabels = $feedbackLabels;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainFeedbackLabel[]
   */
  public function getFeedbackLabels()
  {
    return $this->feedbackLabels;
  }
  /**
   * An arbitrary string value specifying the menu path the customer took.
   *
   * @param string $menuPath
   */
  public function setMenuPath($menuPath)
  {
    $this->menuPath = $menuPath;
  }
  /**
   * @return string
   */
  public function getMenuPath()
  {
    return $this->menuPath;
  }
  /**
   * The amount of time the customer waited to connect with an agent.
   *
   * @param string $waitDuration
   */
  public function setWaitDuration($waitDuration)
  {
    $this->waitDuration = $waitDuration;
  }
  /**
   * @return string
   */
  public function getWaitDuration()
  {
    return $this->waitDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainConversationQualityMetadata::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainConversationQualityMetadata');

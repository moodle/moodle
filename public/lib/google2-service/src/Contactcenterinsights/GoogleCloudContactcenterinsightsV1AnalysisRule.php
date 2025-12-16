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

class GoogleCloudContactcenterinsightsV1AnalysisRule extends \Google\Model
{
  /**
   * If true, apply this rule to conversations. Otherwise, this rule is inactive
   * and saved as a draft.
   *
   * @var bool
   */
  public $active;
  /**
   * Percentage of conversations that we should apply this analysis setting
   * automatically, between [0, 1]. For example, 0.1 means 10%. Conversations
   * are sampled in a determenestic way. The original runtime_percentage &
   * upload percentage will be replaced by defining filters on the conversation.
   *
   * @var 
   */
  public $analysisPercentage;
  protected $annotatorSelectorType = GoogleCloudContactcenterinsightsV1AnnotatorSelector::class;
  protected $annotatorSelectorDataType = '';
  /**
   * Filter for the conversations that should apply this analysis rule. An empty
   * filter means this analysis rule applies to all conversations. Refer to
   * https://cloud.google.com/contact-center/insights/docs/filtering for
   * details.
   *
   * @var string
   */
  public $conversationFilter;
  /**
   * Output only. The time at which this analysis rule was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Display Name of the analysis rule.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. The resource name of the analysis rule. Format:
   * projects/{project}/locations/{location}/analysisRules/{analysis_rule}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The most recent time at which this analysis rule was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * If true, apply this rule to conversations. Otherwise, this rule is inactive
   * and saved as a draft.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  public function setAnalysisPercentage($analysisPercentage)
  {
    $this->analysisPercentage = $analysisPercentage;
  }
  public function getAnalysisPercentage()
  {
    return $this->analysisPercentage;
  }
  /**
   * Selector of annotators to run and the phrase matchers to use for
   * conversations that matches the conversation_filter. If not specified, NO
   * annotators will be run.
   *
   * @param GoogleCloudContactcenterinsightsV1AnnotatorSelector $annotatorSelector
   */
  public function setAnnotatorSelector(GoogleCloudContactcenterinsightsV1AnnotatorSelector $annotatorSelector)
  {
    $this->annotatorSelector = $annotatorSelector;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AnnotatorSelector
   */
  public function getAnnotatorSelector()
  {
    return $this->annotatorSelector;
  }
  /**
   * Filter for the conversations that should apply this analysis rule. An empty
   * filter means this analysis rule applies to all conversations. Refer to
   * https://cloud.google.com/contact-center/insights/docs/filtering for
   * details.
   *
   * @param string $conversationFilter
   */
  public function setConversationFilter($conversationFilter)
  {
    $this->conversationFilter = $conversationFilter;
  }
  /**
   * @return string
   */
  public function getConversationFilter()
  {
    return $this->conversationFilter;
  }
  /**
   * Output only. The time at which this analysis rule was created.
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
   * Display Name of the analysis rule.
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
   * Identifier. The resource name of the analysis rule. Format:
   * projects/{project}/locations/{location}/analysisRules/{analysis_rule}
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
   * Output only. The most recent time at which this analysis rule was updated.
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
class_alias(GoogleCloudContactcenterinsightsV1AnalysisRule::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1AnalysisRule');

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

class GoogleCloudContactcenterinsightsV1alpha1Dimension extends \Google\Model
{
  /**
   * The key of the dimension is unspecified.
   */
  public const DIMENSION_KEY_DIMENSION_KEY_UNSPECIFIED = 'DIMENSION_KEY_UNSPECIFIED';
  /**
   * The dimension is keyed by issues.
   */
  public const DIMENSION_KEY_ISSUE = 'ISSUE';
  /**
   * The dimension is keyed by issue names.
   */
  public const DIMENSION_KEY_ISSUE_NAME = 'ISSUE_NAME';
  /**
   * The dimension is keyed by agents.
   */
  public const DIMENSION_KEY_AGENT = 'AGENT';
  /**
   * The dimension is keyed by agent teams.
   */
  public const DIMENSION_KEY_AGENT_TEAM = 'AGENT_TEAM';
  /**
   * The dimension is keyed by QaQuestionIds. Note that: We only group by the
   * QuestionId and not the revision-id of the scorecard this question is a part
   * of. This allows for showing stats for the same question across different
   * scorecard revisions.
   */
  public const DIMENSION_KEY_QA_QUESTION_ID = 'QA_QUESTION_ID';
  /**
   * The dimension is keyed by QaQuestionIds-Answer value pairs. Note that: We
   * only group by the QuestionId and not the revision-id of the scorecard this
   * question is a part of. This allows for showing distribution of answers per
   * question across different scorecard revisions.
   */
  public const DIMENSION_KEY_QA_QUESTION_ANSWER_VALUE = 'QA_QUESTION_ANSWER_VALUE';
  /**
   * The dimension is keyed by QaScorecardIds. Note that: We only group by the
   * ScorecardId and not the revision-id of the scorecard. This allows for
   * showing stats for the same scorecard across different revisions. This
   * metric is mostly only useful if querying the average normalized score per
   * scorecard.
   */
  public const DIMENSION_KEY_QA_SCORECARD_ID = 'QA_SCORECARD_ID';
  /**
   * The dimension is keyed by the conversation profile ID.
   */
  public const DIMENSION_KEY_CONVERSATION_PROFILE_ID = 'CONVERSATION_PROFILE_ID';
  /**
   * The dimension is keyed by the conversation medium.
   */
  public const DIMENSION_KEY_MEDIUM = 'MEDIUM';
  /**
   * The dimension is keyed by the Conversational Agents playbook ID.
   */
  public const DIMENSION_KEY_CONVERSATIONAL_AGENTS_PLAYBOOK_ID = 'CONVERSATIONAL_AGENTS_PLAYBOOK_ID';
  /**
   * The dimension is keyed by the Conversational Agents playbook display name.
   */
  public const DIMENSION_KEY_CONVERSATIONAL_AGENTS_PLAYBOOK_NAME = 'CONVERSATIONAL_AGENTS_PLAYBOOK_NAME';
  /**
   * The dimension is keyed by the Conversational Agents tool ID.
   */
  public const DIMENSION_KEY_CONVERSATIONAL_AGENTS_TOOL_ID = 'CONVERSATIONAL_AGENTS_TOOL_ID';
  /**
   * The dimension is keyed by the Conversational Agents tool display name.
   */
  public const DIMENSION_KEY_CONVERSATIONAL_AGENTS_TOOL_NAME = 'CONVERSATIONAL_AGENTS_TOOL_NAME';
  /**
   * The dimension is keyed by the client sentiment category.
   */
  public const DIMENSION_KEY_CLIENT_SENTIMENT_CATEGORY = 'CLIENT_SENTIMENT_CATEGORY';
  /**
   * The dimension is keyed by the agent version ID.
   */
  public const DIMENSION_KEY_AGENT_VERSION_ID = 'AGENT_VERSION_ID';
  /**
   * The dimension is keyed by the agent deployment ID.
   */
  public const DIMENSION_KEY_AGENT_DEPLOYMENT_ID = 'AGENT_DEPLOYMENT_ID';
  /**
   * The dimension is keyed by the supervisor ID of the assigned human
   * supervisor for virtual agents.
   */
  public const DIMENSION_KEY_AGENT_ASSIST_SUPERVISOR_ID = 'AGENT_ASSIST_SUPERVISOR_ID';
  /**
   * The dimension is keyed by label keys.
   */
  public const DIMENSION_KEY_LABEL_KEY = 'LABEL_KEY';
  /**
   * The dimension is keyed by label values.
   */
  public const DIMENSION_KEY_LABEL_VALUE = 'LABEL_VALUE';
  /**
   * The dimension is keyed by label key-value pairs.
   */
  public const DIMENSION_KEY_LABEL_KEY_AND_VALUE = 'LABEL_KEY_AND_VALUE';
  protected $agentDimensionMetadataType = GoogleCloudContactcenterinsightsV1alpha1DimensionAgentDimensionMetadata::class;
  protected $agentDimensionMetadataDataType = '';
  protected $clientSentimentCategoryDimensionMetadataType = GoogleCloudContactcenterinsightsV1alpha1DimensionClientSentimentCategoryDimensionMetadata::class;
  protected $clientSentimentCategoryDimensionMetadataDataType = '';
  protected $conversationProfileDimensionMetadataType = GoogleCloudContactcenterinsightsV1alpha1DimensionConversationProfileDimensionMetadata::class;
  protected $conversationProfileDimensionMetadataDataType = '';
  protected $conversationalAgentsPlaybookDimensionMetadataType = GoogleCloudContactcenterinsightsV1alpha1DimensionConversationalAgentsPlaybookDimensionMetadata::class;
  protected $conversationalAgentsPlaybookDimensionMetadataDataType = '';
  protected $conversationalAgentsToolDimensionMetadataType = GoogleCloudContactcenterinsightsV1alpha1DimensionConversationalAgentsToolDimensionMetadata::class;
  protected $conversationalAgentsToolDimensionMetadataDataType = '';
  /**
   * The key of the dimension.
   *
   * @var string
   */
  public $dimensionKey;
  protected $issueDimensionMetadataType = GoogleCloudContactcenterinsightsV1alpha1DimensionIssueDimensionMetadata::class;
  protected $issueDimensionMetadataDataType = '';
  protected $labelDimensionMetadataType = GoogleCloudContactcenterinsightsV1alpha1DimensionLabelDimensionMetadata::class;
  protected $labelDimensionMetadataDataType = '';
  protected $mediumDimensionMetadataType = GoogleCloudContactcenterinsightsV1alpha1DimensionMediumDimensionMetadata::class;
  protected $mediumDimensionMetadataDataType = '';
  protected $qaQuestionAnswerDimensionMetadataType = GoogleCloudContactcenterinsightsV1alpha1DimensionQaQuestionAnswerDimensionMetadata::class;
  protected $qaQuestionAnswerDimensionMetadataDataType = '';
  protected $qaQuestionDimensionMetadataType = GoogleCloudContactcenterinsightsV1alpha1DimensionQaQuestionDimensionMetadata::class;
  protected $qaQuestionDimensionMetadataDataType = '';
  protected $qaScorecardDimensionMetadataType = GoogleCloudContactcenterinsightsV1alpha1DimensionQaScorecardDimensionMetadata::class;
  protected $qaScorecardDimensionMetadataDataType = '';

  /**
   * Output only. Metadata about the agent dimension.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1DimensionAgentDimensionMetadata $agentDimensionMetadata
   */
  public function setAgentDimensionMetadata(GoogleCloudContactcenterinsightsV1alpha1DimensionAgentDimensionMetadata $agentDimensionMetadata)
  {
    $this->agentDimensionMetadata = $agentDimensionMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1DimensionAgentDimensionMetadata
   */
  public function getAgentDimensionMetadata()
  {
    return $this->agentDimensionMetadata;
  }
  /**
   * Output only. Metadata about the client sentiment category dimension.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1DimensionClientSentimentCategoryDimensionMetadata $clientSentimentCategoryDimensionMetadata
   */
  public function setClientSentimentCategoryDimensionMetadata(GoogleCloudContactcenterinsightsV1alpha1DimensionClientSentimentCategoryDimensionMetadata $clientSentimentCategoryDimensionMetadata)
  {
    $this->clientSentimentCategoryDimensionMetadata = $clientSentimentCategoryDimensionMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1DimensionClientSentimentCategoryDimensionMetadata
   */
  public function getClientSentimentCategoryDimensionMetadata()
  {
    return $this->clientSentimentCategoryDimensionMetadata;
  }
  /**
   * Output only. Metadata about the conversation profile dimension.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1DimensionConversationProfileDimensionMetadata $conversationProfileDimensionMetadata
   */
  public function setConversationProfileDimensionMetadata(GoogleCloudContactcenterinsightsV1alpha1DimensionConversationProfileDimensionMetadata $conversationProfileDimensionMetadata)
  {
    $this->conversationProfileDimensionMetadata = $conversationProfileDimensionMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1DimensionConversationProfileDimensionMetadata
   */
  public function getConversationProfileDimensionMetadata()
  {
    return $this->conversationProfileDimensionMetadata;
  }
  /**
   * Output only. Metadata about the Conversational Agents playbook dimension.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1DimensionConversationalAgentsPlaybookDimensionMetadata $conversationalAgentsPlaybookDimensionMetadata
   */
  public function setConversationalAgentsPlaybookDimensionMetadata(GoogleCloudContactcenterinsightsV1alpha1DimensionConversationalAgentsPlaybookDimensionMetadata $conversationalAgentsPlaybookDimensionMetadata)
  {
    $this->conversationalAgentsPlaybookDimensionMetadata = $conversationalAgentsPlaybookDimensionMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1DimensionConversationalAgentsPlaybookDimensionMetadata
   */
  public function getConversationalAgentsPlaybookDimensionMetadata()
  {
    return $this->conversationalAgentsPlaybookDimensionMetadata;
  }
  /**
   * Output only. Metadata about the Conversational Agents tool dimension.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1DimensionConversationalAgentsToolDimensionMetadata $conversationalAgentsToolDimensionMetadata
   */
  public function setConversationalAgentsToolDimensionMetadata(GoogleCloudContactcenterinsightsV1alpha1DimensionConversationalAgentsToolDimensionMetadata $conversationalAgentsToolDimensionMetadata)
  {
    $this->conversationalAgentsToolDimensionMetadata = $conversationalAgentsToolDimensionMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1DimensionConversationalAgentsToolDimensionMetadata
   */
  public function getConversationalAgentsToolDimensionMetadata()
  {
    return $this->conversationalAgentsToolDimensionMetadata;
  }
  /**
   * The key of the dimension.
   *
   * Accepted values: DIMENSION_KEY_UNSPECIFIED, ISSUE, ISSUE_NAME, AGENT,
   * AGENT_TEAM, QA_QUESTION_ID, QA_QUESTION_ANSWER_VALUE, QA_SCORECARD_ID,
   * CONVERSATION_PROFILE_ID, MEDIUM, CONVERSATIONAL_AGENTS_PLAYBOOK_ID,
   * CONVERSATIONAL_AGENTS_PLAYBOOK_NAME, CONVERSATIONAL_AGENTS_TOOL_ID,
   * CONVERSATIONAL_AGENTS_TOOL_NAME, CLIENT_SENTIMENT_CATEGORY,
   * AGENT_VERSION_ID, AGENT_DEPLOYMENT_ID, AGENT_ASSIST_SUPERVISOR_ID,
   * LABEL_KEY, LABEL_VALUE, LABEL_KEY_AND_VALUE
   *
   * @param self::DIMENSION_KEY_* $dimensionKey
   */
  public function setDimensionKey($dimensionKey)
  {
    $this->dimensionKey = $dimensionKey;
  }
  /**
   * @return self::DIMENSION_KEY_*
   */
  public function getDimensionKey()
  {
    return $this->dimensionKey;
  }
  /**
   * Output only. Metadata about the issue dimension.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1DimensionIssueDimensionMetadata $issueDimensionMetadata
   */
  public function setIssueDimensionMetadata(GoogleCloudContactcenterinsightsV1alpha1DimensionIssueDimensionMetadata $issueDimensionMetadata)
  {
    $this->issueDimensionMetadata = $issueDimensionMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1DimensionIssueDimensionMetadata
   */
  public function getIssueDimensionMetadata()
  {
    return $this->issueDimensionMetadata;
  }
  /**
   * Output only. Metadata about conversation labels.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1DimensionLabelDimensionMetadata $labelDimensionMetadata
   */
  public function setLabelDimensionMetadata(GoogleCloudContactcenterinsightsV1alpha1DimensionLabelDimensionMetadata $labelDimensionMetadata)
  {
    $this->labelDimensionMetadata = $labelDimensionMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1DimensionLabelDimensionMetadata
   */
  public function getLabelDimensionMetadata()
  {
    return $this->labelDimensionMetadata;
  }
  /**
   * Output only. Metadata about the conversation medium dimension.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1DimensionMediumDimensionMetadata $mediumDimensionMetadata
   */
  public function setMediumDimensionMetadata(GoogleCloudContactcenterinsightsV1alpha1DimensionMediumDimensionMetadata $mediumDimensionMetadata)
  {
    $this->mediumDimensionMetadata = $mediumDimensionMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1DimensionMediumDimensionMetadata
   */
  public function getMediumDimensionMetadata()
  {
    return $this->mediumDimensionMetadata;
  }
  /**
   * Output only. Metadata about the QA question-answer dimension.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1DimensionQaQuestionAnswerDimensionMetadata $qaQuestionAnswerDimensionMetadata
   */
  public function setQaQuestionAnswerDimensionMetadata(GoogleCloudContactcenterinsightsV1alpha1DimensionQaQuestionAnswerDimensionMetadata $qaQuestionAnswerDimensionMetadata)
  {
    $this->qaQuestionAnswerDimensionMetadata = $qaQuestionAnswerDimensionMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1DimensionQaQuestionAnswerDimensionMetadata
   */
  public function getQaQuestionAnswerDimensionMetadata()
  {
    return $this->qaQuestionAnswerDimensionMetadata;
  }
  /**
   * Output only. Metadata about the QA question dimension.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1DimensionQaQuestionDimensionMetadata $qaQuestionDimensionMetadata
   */
  public function setQaQuestionDimensionMetadata(GoogleCloudContactcenterinsightsV1alpha1DimensionQaQuestionDimensionMetadata $qaQuestionDimensionMetadata)
  {
    $this->qaQuestionDimensionMetadata = $qaQuestionDimensionMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1DimensionQaQuestionDimensionMetadata
   */
  public function getQaQuestionDimensionMetadata()
  {
    return $this->qaQuestionDimensionMetadata;
  }
  /**
   * Output only. Metadata about the QA scorecard dimension.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1DimensionQaScorecardDimensionMetadata $qaScorecardDimensionMetadata
   */
  public function setQaScorecardDimensionMetadata(GoogleCloudContactcenterinsightsV1alpha1DimensionQaScorecardDimensionMetadata $qaScorecardDimensionMetadata)
  {
    $this->qaScorecardDimensionMetadata = $qaScorecardDimensionMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1DimensionQaScorecardDimensionMetadata
   */
  public function getQaScorecardDimensionMetadata()
  {
    return $this->qaScorecardDimensionMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1Dimension::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1Dimension');

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

class GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequest extends \Google\Collection
{
  /**
   * Unspecified format
   */
  public const FEEDBACK_LABEL_TYPE_FEEDBACK_LABEL_TYPE_UNSPECIFIED = 'FEEDBACK_LABEL_TYPE_UNSPECIFIED';
  /**
   * Downloaded file will contain all Quality AI labels from the latest
   * scorecard revision.
   */
  public const FEEDBACK_LABEL_TYPE_QUALITY_AI = 'QUALITY_AI';
  /**
   * Downloaded file will contain only Topic Modeling labels.
   */
  public const FEEDBACK_LABEL_TYPE_TOPIC_MODELING = 'TOPIC_MODELING';
  /**
   * Agent Assist Summarization labels.
   */
  public const FEEDBACK_LABEL_TYPE_AGENT_ASSIST_SUMMARY = 'AGENT_ASSIST_SUMMARY';
  protected $collection_key = 'templateQaScorecardId';
  /**
   * Optional. Filter parent conversations to download feedback labels for. When
   * specified, the feedback labels will be downloaded for the conversations
   * that match the filter. If `template_qa_scorecard_id` is set, all the
   * conversations that match the filter will be paired with the questions under
   * the scorecard for labeling.
   *
   * @var string
   */
  public $conversationFilter;
  /**
   * Optional. The type of feedback labels that will be downloaded.
   *
   * @var string
   */
  public $feedbackLabelType;
  /**
   * Optional. A filter to reduce results to a specific subset. Supports
   * disjunctions (OR) and conjunctions (AND). Supported fields: *
   * `issue_model_id` * `qa_question_id` * `qa_scorecard_id` * `min_create_time`
   * * `max_create_time` * `min_update_time` * `max_update_time` *
   * `feedback_label_type`: QUALITY_AI, TOPIC_MODELING
   *
   * @var string
   */
  public $filter;
  protected $gcsDestinationType = GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequestGcsDestination::class;
  protected $gcsDestinationDataType = '';
  /**
   * Optional. Limits the maximum number of feedback labels that will be
   * downloaded. The first `N` feedback labels will be downloaded.
   *
   * @var int
   */
  public $maxDownloadCount;
  /**
   * Required. The parent resource for new feedback labels.
   *
   * @var string
   */
  public $parent;
  protected $sheetsDestinationType = GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequestSheetsDestination::class;
  protected $sheetsDestinationDataType = '';
  /**
   * Optional. If set, a template for labeling conversations and scorecard
   * questions will be created from the conversation_filter and the questions
   * under the scorecard(s). The feedback label `filter` will be ignored.
   *
   * @var string[]
   */
  public $templateQaScorecardId;

  /**
   * Optional. Filter parent conversations to download feedback labels for. When
   * specified, the feedback labels will be downloaded for the conversations
   * that match the filter. If `template_qa_scorecard_id` is set, all the
   * conversations that match the filter will be paired with the questions under
   * the scorecard for labeling.
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
   * Optional. The type of feedback labels that will be downloaded.
   *
   * Accepted values: FEEDBACK_LABEL_TYPE_UNSPECIFIED, QUALITY_AI,
   * TOPIC_MODELING, AGENT_ASSIST_SUMMARY
   *
   * @param self::FEEDBACK_LABEL_TYPE_* $feedbackLabelType
   */
  public function setFeedbackLabelType($feedbackLabelType)
  {
    $this->feedbackLabelType = $feedbackLabelType;
  }
  /**
   * @return self::FEEDBACK_LABEL_TYPE_*
   */
  public function getFeedbackLabelType()
  {
    return $this->feedbackLabelType;
  }
  /**
   * Optional. A filter to reduce results to a specific subset. Supports
   * disjunctions (OR) and conjunctions (AND). Supported fields: *
   * `issue_model_id` * `qa_question_id` * `qa_scorecard_id` * `min_create_time`
   * * `max_create_time` * `min_update_time` * `max_update_time` *
   * `feedback_label_type`: QUALITY_AI, TOPIC_MODELING
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * A cloud storage bucket destination.
   *
   * @param GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequestGcsDestination $gcsDestination
   */
  public function setGcsDestination(GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequestGcsDestination $gcsDestination)
  {
    $this->gcsDestination = $gcsDestination;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequestGcsDestination
   */
  public function getGcsDestination()
  {
    return $this->gcsDestination;
  }
  /**
   * Optional. Limits the maximum number of feedback labels that will be
   * downloaded. The first `N` feedback labels will be downloaded.
   *
   * @param int $maxDownloadCount
   */
  public function setMaxDownloadCount($maxDownloadCount)
  {
    $this->maxDownloadCount = $maxDownloadCount;
  }
  /**
   * @return int
   */
  public function getMaxDownloadCount()
  {
    return $this->maxDownloadCount;
  }
  /**
   * Required. The parent resource for new feedback labels.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * A sheets document destination.
   *
   * @param GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequestSheetsDestination $sheetsDestination
   */
  public function setSheetsDestination(GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequestSheetsDestination $sheetsDestination)
  {
    $this->sheetsDestination = $sheetsDestination;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequestSheetsDestination
   */
  public function getSheetsDestination()
  {
    return $this->sheetsDestination;
  }
  /**
   * Optional. If set, a template for labeling conversations and scorecard
   * questions will be created from the conversation_filter and the questions
   * under the scorecard(s). The feedback label `filter` will be ignored.
   *
   * @param string[] $templateQaScorecardId
   */
  public function setTemplateQaScorecardId($templateQaScorecardId)
  {
    $this->templateQaScorecardId = $templateQaScorecardId;
  }
  /**
   * @return string[]
   */
  public function getTemplateQaScorecardId()
  {
    return $this->templateQaScorecardId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequest');

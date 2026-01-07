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

class GoogleCloudContactcenterinsightsV1alpha1BulkDeleteFeedbackLabelsRequest extends \Google\Model
{
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
  /**
   * Required. The parent resource for new feedback labels.
   *
   * @var string
   */
  public $parent;

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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1BulkDeleteFeedbackLabelsRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1BulkDeleteFeedbackLabelsRequest');

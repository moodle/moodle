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

class GoogleCloudContactcenterinsightsV1mainFeedbackLabel extends \Google\Model
{
  /**
   * Output only. Create time of the label.
   *
   * @var string
   */
  public $createTime;
  /**
   * String label used for Topic Modeling.
   *
   * @var string
   */
  public $label;
  /**
   * Name of the resource to be labeled. Supported resources are: * `projects/{p
   * roject}/locations/{location}/qaScorecards/{scorecard}/revisions/{revision}/
   * qaQuestions/{question}` *
   * `projects/{project}/locations/{location}/issueModels/{issue_model}` *
   * `projects/{project}/locations/{location}/generators/{generator_id}`
   *
   * @var string
   */
  public $labeledResource;
  /**
   * Immutable. Resource name of the FeedbackLabel. Format: projects/{project}/l
   * ocations/{location}/conversations/{conversation}/feedbackLabels/{feedback_l
   * abel}
   *
   * @var string
   */
  public $name;
  protected $qaAnswerLabelType = GoogleCloudContactcenterinsightsV1mainQaAnswerAnswerValue::class;
  protected $qaAnswerLabelDataType = '';
  /**
   * Output only. Update time of the label.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Create time of the label.
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
   * String label used for Topic Modeling.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * Name of the resource to be labeled. Supported resources are: * `projects/{p
   * roject}/locations/{location}/qaScorecards/{scorecard}/revisions/{revision}/
   * qaQuestions/{question}` *
   * `projects/{project}/locations/{location}/issueModels/{issue_model}` *
   * `projects/{project}/locations/{location}/generators/{generator_id}`
   *
   * @param string $labeledResource
   */
  public function setLabeledResource($labeledResource)
  {
    $this->labeledResource = $labeledResource;
  }
  /**
   * @return string
   */
  public function getLabeledResource()
  {
    return $this->labeledResource;
  }
  /**
   * Immutable. Resource name of the FeedbackLabel. Format: projects/{project}/l
   * ocations/{location}/conversations/{conversation}/feedbackLabels/{feedback_l
   * abel}
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
   * QaAnswer label used for Quality AI example conversations.
   *
   * @param GoogleCloudContactcenterinsightsV1mainQaAnswerAnswerValue $qaAnswerLabel
   */
  public function setQaAnswerLabel(GoogleCloudContactcenterinsightsV1mainQaAnswerAnswerValue $qaAnswerLabel)
  {
    $this->qaAnswerLabel = $qaAnswerLabel;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainQaAnswerAnswerValue
   */
  public function getQaAnswerLabel()
  {
    return $this->qaAnswerLabel;
  }
  /**
   * Output only. Update time of the label.
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
class_alias(GoogleCloudContactcenterinsightsV1mainFeedbackLabel::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainFeedbackLabel');

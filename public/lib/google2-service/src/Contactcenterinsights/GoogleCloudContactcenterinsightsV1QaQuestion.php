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

class GoogleCloudContactcenterinsightsV1QaQuestion extends \Google\Collection
{
  /**
   * The type of the question is unspecified.
   */
  public const QUESTION_TYPE_QA_QUESTION_TYPE_UNSPECIFIED = 'QA_QUESTION_TYPE_UNSPECIFIED';
  /**
   * The default question type. The question is fully customizable by the user.
   */
  public const QUESTION_TYPE_CUSTOMIZABLE = 'CUSTOMIZABLE';
  /**
   * The question type is using a predefined model provided by CCAI teams. Users
   * are not allowed to edit the question_body, answer_choices, upload feedback
   * labels for the question nor fine-tune the question. However, users may edit
   * other fields like question tags, question order, etc.
   */
  public const QUESTION_TYPE_PREDEFINED = 'PREDEFINED';
  protected $collection_key = 'tags';
  /**
   * Short, descriptive string, used in the UI where it's not practical to
   * display the full question body. E.g., "Greeting".
   *
   * @var string
   */
  public $abbreviation;
  protected $answerChoicesType = GoogleCloudContactcenterinsightsV1QaQuestionAnswerChoice::class;
  protected $answerChoicesDataType = 'array';
  /**
   * Instructions describing how to determine the answer.
   *
   * @var string
   */
  public $answerInstructions;
  /**
   * Output only. The time at which this question was created.
   *
   * @var string
   */
  public $createTime;
  protected $metricsType = GoogleCloudContactcenterinsightsV1QaQuestionMetrics::class;
  protected $metricsDataType = '';
  /**
   * Identifier. The resource name of the question. Format: projects/{project}/l
   * ocations/{location}/qaScorecards/{qa_scorecard}/revisions/{revision}/qaQues
   * tions/{qa_question}
   *
   * @var string
   */
  public $name;
  /**
   * Defines the order of the question within its parent scorecard revision.
   *
   * @var int
   */
  public $order;
  protected $predefinedQuestionConfigType = GoogleCloudContactcenterinsightsV1QaQuestionPredefinedQuestionConfig::class;
  protected $predefinedQuestionConfigDataType = '';
  /**
   * Question text. E.g., "Did the agent greet the customer?"
   *
   * @var string
   */
  public $questionBody;
  /**
   * The type of question.
   *
   * @var string
   */
  public $questionType;
  /**
   * Questions are tagged for categorization and scoring. Tags can either be: -
   * Default Tags: These are predefined categories. They are identified by their
   * string value (e.g., "BUSINESS", "COMPLIANCE", and "CUSTOMER"). - Custom
   * Tags: These are user-defined categories. They are identified by their full
   * resource name (e.g.,
   * projects/{project}/locations/{location}/qaQuestionTags/{qa_question_tag}).
   * Both default and custom tags are used to group questions and to influence
   * the scoring of each question.
   *
   * @var string[]
   */
  public $tags;
  protected $tuningMetadataType = GoogleCloudContactcenterinsightsV1QaQuestionTuningMetadata::class;
  protected $tuningMetadataDataType = '';
  /**
   * Output only. The most recent time at which the question was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Short, descriptive string, used in the UI where it's not practical to
   * display the full question body. E.g., "Greeting".
   *
   * @param string $abbreviation
   */
  public function setAbbreviation($abbreviation)
  {
    $this->abbreviation = $abbreviation;
  }
  /**
   * @return string
   */
  public function getAbbreviation()
  {
    return $this->abbreviation;
  }
  /**
   * A list of valid answers to the question, which the LLM must choose from.
   *
   * @param GoogleCloudContactcenterinsightsV1QaQuestionAnswerChoice[] $answerChoices
   */
  public function setAnswerChoices($answerChoices)
  {
    $this->answerChoices = $answerChoices;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1QaQuestionAnswerChoice[]
   */
  public function getAnswerChoices()
  {
    return $this->answerChoices;
  }
  /**
   * Instructions describing how to determine the answer.
   *
   * @param string $answerInstructions
   */
  public function setAnswerInstructions($answerInstructions)
  {
    $this->answerInstructions = $answerInstructions;
  }
  /**
   * @return string
   */
  public function getAnswerInstructions()
  {
    return $this->answerInstructions;
  }
  /**
   * Output only. The time at which this question was created.
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
   * Metrics of the underlying tuned LLM over a holdout/test set while fine
   * tuning the underlying LLM for the given question. This field will only be
   * populated if and only if the question is part of a scorecard revision that
   * has been tuned.
   *
   * @param GoogleCloudContactcenterinsightsV1QaQuestionMetrics $metrics
   */
  public function setMetrics(GoogleCloudContactcenterinsightsV1QaQuestionMetrics $metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1QaQuestionMetrics
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Identifier. The resource name of the question. Format: projects/{project}/l
   * ocations/{location}/qaScorecards/{qa_scorecard}/revisions/{revision}/qaQues
   * tions/{qa_question}
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
   * Defines the order of the question within its parent scorecard revision.
   *
   * @param int $order
   */
  public function setOrder($order)
  {
    $this->order = $order;
  }
  /**
   * @return int
   */
  public function getOrder()
  {
    return $this->order;
  }
  /**
   * The configuration of the predefined question. This field will only be set
   * if the Question Type is predefined.
   *
   * @param GoogleCloudContactcenterinsightsV1QaQuestionPredefinedQuestionConfig $predefinedQuestionConfig
   */
  public function setPredefinedQuestionConfig(GoogleCloudContactcenterinsightsV1QaQuestionPredefinedQuestionConfig $predefinedQuestionConfig)
  {
    $this->predefinedQuestionConfig = $predefinedQuestionConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1QaQuestionPredefinedQuestionConfig
   */
  public function getPredefinedQuestionConfig()
  {
    return $this->predefinedQuestionConfig;
  }
  /**
   * Question text. E.g., "Did the agent greet the customer?"
   *
   * @param string $questionBody
   */
  public function setQuestionBody($questionBody)
  {
    $this->questionBody = $questionBody;
  }
  /**
   * @return string
   */
  public function getQuestionBody()
  {
    return $this->questionBody;
  }
  /**
   * The type of question.
   *
   * Accepted values: QA_QUESTION_TYPE_UNSPECIFIED, CUSTOMIZABLE, PREDEFINED
   *
   * @param self::QUESTION_TYPE_* $questionType
   */
  public function setQuestionType($questionType)
  {
    $this->questionType = $questionType;
  }
  /**
   * @return self::QUESTION_TYPE_*
   */
  public function getQuestionType()
  {
    return $this->questionType;
  }
  /**
   * Questions are tagged for categorization and scoring. Tags can either be: -
   * Default Tags: These are predefined categories. They are identified by their
   * string value (e.g., "BUSINESS", "COMPLIANCE", and "CUSTOMER"). - Custom
   * Tags: These are user-defined categories. They are identified by their full
   * resource name (e.g.,
   * projects/{project}/locations/{location}/qaQuestionTags/{qa_question_tag}).
   * Both default and custom tags are used to group questions and to influence
   * the scoring of each question.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Metadata about the tuning operation for the question.This field will only
   * be populated if and only if the question is part of a scorecard revision
   * that has been tuned.
   *
   * @param GoogleCloudContactcenterinsightsV1QaQuestionTuningMetadata $tuningMetadata
   */
  public function setTuningMetadata(GoogleCloudContactcenterinsightsV1QaQuestionTuningMetadata $tuningMetadata)
  {
    $this->tuningMetadata = $tuningMetadata;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1QaQuestionTuningMetadata
   */
  public function getTuningMetadata()
  {
    return $this->tuningMetadata;
  }
  /**
   * Output only. The most recent time at which the question was updated.
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
class_alias(GoogleCloudContactcenterinsightsV1QaQuestion::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1QaQuestion');

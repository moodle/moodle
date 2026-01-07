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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1HumanAnnotationConfig extends \Google\Collection
{
  protected $collection_key = 'contributorEmails';
  /**
   * Optional. A human-readable description for AnnotatedDataset. The
   * description can be up to 10000 characters long.
   *
   * @var string
   */
  public $annotatedDatasetDescription;
  /**
   * Required. A human-readable name for AnnotatedDataset defined by users.
   * Maximum of 64 characters .
   *
   * @var string
   */
  public $annotatedDatasetDisplayName;
  /**
   * Optional. If you want your own labeling contributors to manage and work on
   * this labeling request, you can set these contributors here. We will give
   * them access to the question types in crowdcompute. Note that these emails
   * must be registered in crowdcompute worker UI: https://crowd-
   * compute.appspot.com/
   *
   * @var string[]
   */
  public $contributorEmails;
  /**
   * Required. Instruction resource name.
   *
   * @var string
   */
  public $instruction;
  /**
   * Optional. A human-readable label used to logically group labeling tasks.
   * This string must match the regular expression `[a-zA-Z\\d_-]{0,128}`.
   *
   * @var string
   */
  public $labelGroup;
  /**
   * Optional. The Language of this question, as a [BCP-47](https://www.rfc-
   * editor.org/rfc/bcp/bcp47.txt). Default value is en-US. Only need to set
   * this when task is language related. For example, French text
   * classification.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Optional. Maximum duration for contributors to answer a question. Maximum
   * is 3600 seconds. Default is 3600 seconds.
   *
   * @var string
   */
  public $questionDuration;
  /**
   * Optional. Replication of questions. Each question will be sent to up to
   * this number of contributors to label. Aggregated answers will be returned.
   * Default is set to 1. For image related labeling, valid values are 1, 3, 5.
   *
   * @var int
   */
  public $replicaCount;
  /**
   * Email of the user who started the labeling task and should be notified by
   * email. If empty no notification will be sent.
   *
   * @var string
   */
  public $userEmailAddress;

  /**
   * Optional. A human-readable description for AnnotatedDataset. The
   * description can be up to 10000 characters long.
   *
   * @param string $annotatedDatasetDescription
   */
  public function setAnnotatedDatasetDescription($annotatedDatasetDescription)
  {
    $this->annotatedDatasetDescription = $annotatedDatasetDescription;
  }
  /**
   * @return string
   */
  public function getAnnotatedDatasetDescription()
  {
    return $this->annotatedDatasetDescription;
  }
  /**
   * Required. A human-readable name for AnnotatedDataset defined by users.
   * Maximum of 64 characters .
   *
   * @param string $annotatedDatasetDisplayName
   */
  public function setAnnotatedDatasetDisplayName($annotatedDatasetDisplayName)
  {
    $this->annotatedDatasetDisplayName = $annotatedDatasetDisplayName;
  }
  /**
   * @return string
   */
  public function getAnnotatedDatasetDisplayName()
  {
    return $this->annotatedDatasetDisplayName;
  }
  /**
   * Optional. If you want your own labeling contributors to manage and work on
   * this labeling request, you can set these contributors here. We will give
   * them access to the question types in crowdcompute. Note that these emails
   * must be registered in crowdcompute worker UI: https://crowd-
   * compute.appspot.com/
   *
   * @param string[] $contributorEmails
   */
  public function setContributorEmails($contributorEmails)
  {
    $this->contributorEmails = $contributorEmails;
  }
  /**
   * @return string[]
   */
  public function getContributorEmails()
  {
    return $this->contributorEmails;
  }
  /**
   * Required. Instruction resource name.
   *
   * @param string $instruction
   */
  public function setInstruction($instruction)
  {
    $this->instruction = $instruction;
  }
  /**
   * @return string
   */
  public function getInstruction()
  {
    return $this->instruction;
  }
  /**
   * Optional. A human-readable label used to logically group labeling tasks.
   * This string must match the regular expression `[a-zA-Z\\d_-]{0,128}`.
   *
   * @param string $labelGroup
   */
  public function setLabelGroup($labelGroup)
  {
    $this->labelGroup = $labelGroup;
  }
  /**
   * @return string
   */
  public function getLabelGroup()
  {
    return $this->labelGroup;
  }
  /**
   * Optional. The Language of this question, as a [BCP-47](https://www.rfc-
   * editor.org/rfc/bcp/bcp47.txt). Default value is en-US. Only need to set
   * this when task is language related. For example, French text
   * classification.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Optional. Maximum duration for contributors to answer a question. Maximum
   * is 3600 seconds. Default is 3600 seconds.
   *
   * @param string $questionDuration
   */
  public function setQuestionDuration($questionDuration)
  {
    $this->questionDuration = $questionDuration;
  }
  /**
   * @return string
   */
  public function getQuestionDuration()
  {
    return $this->questionDuration;
  }
  /**
   * Optional. Replication of questions. Each question will be sent to up to
   * this number of contributors to label. Aggregated answers will be returned.
   * Default is set to 1. For image related labeling, valid values are 1, 3, 5.
   *
   * @param int $replicaCount
   */
  public function setReplicaCount($replicaCount)
  {
    $this->replicaCount = $replicaCount;
  }
  /**
   * @return int
   */
  public function getReplicaCount()
  {
    return $this->replicaCount;
  }
  /**
   * Email of the user who started the labeling task and should be notified by
   * email. If empty no notification will be sent.
   *
   * @param string $userEmailAddress
   */
  public function setUserEmailAddress($userEmailAddress)
  {
    $this->userEmailAddress = $userEmailAddress;
  }
  /**
   * @return string
   */
  public function getUserEmailAddress()
  {
    return $this->userEmailAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1HumanAnnotationConfig::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1HumanAnnotationConfig');

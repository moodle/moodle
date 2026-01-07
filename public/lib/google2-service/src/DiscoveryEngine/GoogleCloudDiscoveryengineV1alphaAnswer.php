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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaAnswer extends \Google\Collection
{
  /**
   * Unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Answer generation is currently in progress.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Answer generation currently failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Answer generation has succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Answer generation is currently in progress.
   */
  public const STATE_STREAMING = 'STREAMING';
  protected $collection_key = 'steps';
  /**
   * Additional answer-skipped reasons. This provides the reason for ignored
   * cases. If nothing is skipped, this field is not set.
   *
   * @var string[]
   */
  public $answerSkippedReasons;
  /**
   * The textual answer.
   *
   * @var string
   */
  public $answerText;
  protected $blobAttachmentsType = GoogleCloudDiscoveryengineV1alphaAnswerBlobAttachment::class;
  protected $blobAttachmentsDataType = 'array';
  protected $citationsType = GoogleCloudDiscoveryengineV1alphaAnswerCitation::class;
  protected $citationsDataType = 'array';
  /**
   * Output only. Answer completed timestamp.
   *
   * @var string
   */
  public $completeTime;
  /**
   * Output only. Answer creation timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * A score in the range of [0, 1] describing how grounded the answer is by the
   * reference chunks.
   *
   * @var 
   */
  public $groundingScore;
  protected $groundingSupportsType = GoogleCloudDiscoveryengineV1alphaAnswerGroundingSupport::class;
  protected $groundingSupportsDataType = 'array';
  /**
   * Immutable. Fully qualified name `projects/{project}/locations/global/collec
   * tions/{collection}/engines/{engine}/sessions/answers`
   *
   * @var string
   */
  public $name;
  protected $queryUnderstandingInfoType = GoogleCloudDiscoveryengineV1alphaAnswerQueryUnderstandingInfo::class;
  protected $queryUnderstandingInfoDataType = '';
  protected $referencesType = GoogleCloudDiscoveryengineV1alphaAnswerReference::class;
  protected $referencesDataType = 'array';
  /**
   * Suggested related questions.
   *
   * @var string[]
   */
  public $relatedQuestions;
  protected $safetyRatingsType = GoogleCloudDiscoveryengineV1alphaSafetyRating::class;
  protected $safetyRatingsDataType = 'array';
  /**
   * The state of the answer generation.
   *
   * @var string
   */
  public $state;
  protected $stepsType = GoogleCloudDiscoveryengineV1alphaAnswerStep::class;
  protected $stepsDataType = 'array';

  /**
   * Additional answer-skipped reasons. This provides the reason for ignored
   * cases. If nothing is skipped, this field is not set.
   *
   * @param string[] $answerSkippedReasons
   */
  public function setAnswerSkippedReasons($answerSkippedReasons)
  {
    $this->answerSkippedReasons = $answerSkippedReasons;
  }
  /**
   * @return string[]
   */
  public function getAnswerSkippedReasons()
  {
    return $this->answerSkippedReasons;
  }
  /**
   * The textual answer.
   *
   * @param string $answerText
   */
  public function setAnswerText($answerText)
  {
    $this->answerText = $answerText;
  }
  /**
   * @return string
   */
  public function getAnswerText()
  {
    return $this->answerText;
  }
  /**
   * List of blob attachments in the answer.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAnswerBlobAttachment[] $blobAttachments
   */
  public function setBlobAttachments($blobAttachments)
  {
    $this->blobAttachments = $blobAttachments;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAnswerBlobAttachment[]
   */
  public function getBlobAttachments()
  {
    return $this->blobAttachments;
  }
  /**
   * Citations.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAnswerCitation[] $citations
   */
  public function setCitations($citations)
  {
    $this->citations = $citations;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAnswerCitation[]
   */
  public function getCitations()
  {
    return $this->citations;
  }
  /**
   * Output only. Answer completed timestamp.
   *
   * @param string $completeTime
   */
  public function setCompleteTime($completeTime)
  {
    $this->completeTime = $completeTime;
  }
  /**
   * @return string
   */
  public function getCompleteTime()
  {
    return $this->completeTime;
  }
  /**
   * Output only. Answer creation timestamp.
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
  public function setGroundingScore($groundingScore)
  {
    $this->groundingScore = $groundingScore;
  }
  public function getGroundingScore()
  {
    return $this->groundingScore;
  }
  /**
   * Optional. Grounding supports.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAnswerGroundingSupport[] $groundingSupports
   */
  public function setGroundingSupports($groundingSupports)
  {
    $this->groundingSupports = $groundingSupports;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAnswerGroundingSupport[]
   */
  public function getGroundingSupports()
  {
    return $this->groundingSupports;
  }
  /**
   * Immutable. Fully qualified name `projects/{project}/locations/global/collec
   * tions/{collection}/engines/{engine}/sessions/answers`
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
   * Query understanding information.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAnswerQueryUnderstandingInfo $queryUnderstandingInfo
   */
  public function setQueryUnderstandingInfo(GoogleCloudDiscoveryengineV1alphaAnswerQueryUnderstandingInfo $queryUnderstandingInfo)
  {
    $this->queryUnderstandingInfo = $queryUnderstandingInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAnswerQueryUnderstandingInfo
   */
  public function getQueryUnderstandingInfo()
  {
    return $this->queryUnderstandingInfo;
  }
  /**
   * References.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAnswerReference[] $references
   */
  public function setReferences($references)
  {
    $this->references = $references;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAnswerReference[]
   */
  public function getReferences()
  {
    return $this->references;
  }
  /**
   * Suggested related questions.
   *
   * @param string[] $relatedQuestions
   */
  public function setRelatedQuestions($relatedQuestions)
  {
    $this->relatedQuestions = $relatedQuestions;
  }
  /**
   * @return string[]
   */
  public function getRelatedQuestions()
  {
    return $this->relatedQuestions;
  }
  /**
   * Optional. Safety ratings.
   *
   * @param GoogleCloudDiscoveryengineV1alphaSafetyRating[] $safetyRatings
   */
  public function setSafetyRatings($safetyRatings)
  {
    $this->safetyRatings = $safetyRatings;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaSafetyRating[]
   */
  public function getSafetyRatings()
  {
    return $this->safetyRatings;
  }
  /**
   * The state of the answer generation.
   *
   * Accepted values: STATE_UNSPECIFIED, IN_PROGRESS, FAILED, SUCCEEDED,
   * STREAMING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Answer generation steps.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAnswerStep[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAnswerStep[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaAnswer::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaAnswer');

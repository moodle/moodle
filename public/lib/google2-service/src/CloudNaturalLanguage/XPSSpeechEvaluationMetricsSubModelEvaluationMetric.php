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

namespace Google\Service\CloudNaturalLanguage;

class XPSSpeechEvaluationMetricsSubModelEvaluationMetric extends \Google\Model
{
  public const BIASING_MODEL_TYPE_BIASING_MODEL_TYPE_UNSPECIFIED = 'BIASING_MODEL_TYPE_UNSPECIFIED';
  /**
   * Build biasing model on top of COMMAND_AND_SEARCH model
   */
  public const BIASING_MODEL_TYPE_COMMAND_AND_SEARCH = 'COMMAND_AND_SEARCH';
  /**
   * Build biasing model on top of PHONE_CALL model
   */
  public const BIASING_MODEL_TYPE_PHONE_CALL = 'PHONE_CALL';
  /**
   * Build biasing model on top of VIDEO model
   */
  public const BIASING_MODEL_TYPE_VIDEO = 'VIDEO';
  /**
   * Build biasing model on top of DEFAULT model
   */
  public const BIASING_MODEL_TYPE_DEFAULT = 'DEFAULT';
  /**
   * Type of the biasing model.
   *
   * @var string
   */
  public $biasingModelType;
  /**
   * If true then it means we have an enhanced version of the biasing models.
   *
   * @var bool
   */
  public $isEnhancedModel;
  /**
   * @var int
   */
  public $numDeletions;
  /**
   * @var int
   */
  public $numInsertions;
  /**
   * @var int
   */
  public $numSubstitutions;
  /**
   * Number of utterances used in the wer computation.
   *
   * @var int
   */
  public $numUtterances;
  /**
   * Number of words over which the word error rate was computed.
   *
   * @var int
   */
  public $numWords;
  /**
   * Below fields are used for debugging purposes
   *
   * @var 
   */
  public $sentenceAccuracy;
  /**
   * Word error rate (standard error metric used for speech recognition).
   *
   * @var 
   */
  public $wer;

  /**
   * Type of the biasing model.
   *
   * Accepted values: BIASING_MODEL_TYPE_UNSPECIFIED, COMMAND_AND_SEARCH,
   * PHONE_CALL, VIDEO, DEFAULT
   *
   * @param self::BIASING_MODEL_TYPE_* $biasingModelType
   */
  public function setBiasingModelType($biasingModelType)
  {
    $this->biasingModelType = $biasingModelType;
  }
  /**
   * @return self::BIASING_MODEL_TYPE_*
   */
  public function getBiasingModelType()
  {
    return $this->biasingModelType;
  }
  /**
   * If true then it means we have an enhanced version of the biasing models.
   *
   * @param bool $isEnhancedModel
   */
  public function setIsEnhancedModel($isEnhancedModel)
  {
    $this->isEnhancedModel = $isEnhancedModel;
  }
  /**
   * @return bool
   */
  public function getIsEnhancedModel()
  {
    return $this->isEnhancedModel;
  }
  /**
   * @param int $numDeletions
   */
  public function setNumDeletions($numDeletions)
  {
    $this->numDeletions = $numDeletions;
  }
  /**
   * @return int
   */
  public function getNumDeletions()
  {
    return $this->numDeletions;
  }
  /**
   * @param int $numInsertions
   */
  public function setNumInsertions($numInsertions)
  {
    $this->numInsertions = $numInsertions;
  }
  /**
   * @return int
   */
  public function getNumInsertions()
  {
    return $this->numInsertions;
  }
  /**
   * @param int $numSubstitutions
   */
  public function setNumSubstitutions($numSubstitutions)
  {
    $this->numSubstitutions = $numSubstitutions;
  }
  /**
   * @return int
   */
  public function getNumSubstitutions()
  {
    return $this->numSubstitutions;
  }
  /**
   * Number of utterances used in the wer computation.
   *
   * @param int $numUtterances
   */
  public function setNumUtterances($numUtterances)
  {
    $this->numUtterances = $numUtterances;
  }
  /**
   * @return int
   */
  public function getNumUtterances()
  {
    return $this->numUtterances;
  }
  /**
   * Number of words over which the word error rate was computed.
   *
   * @param int $numWords
   */
  public function setNumWords($numWords)
  {
    $this->numWords = $numWords;
  }
  /**
   * @return int
   */
  public function getNumWords()
  {
    return $this->numWords;
  }
  public function setSentenceAccuracy($sentenceAccuracy)
  {
    $this->sentenceAccuracy = $sentenceAccuracy;
  }
  public function getSentenceAccuracy()
  {
    return $this->sentenceAccuracy;
  }
  public function setWer($wer)
  {
    $this->wer = $wer;
  }
  public function getWer()
  {
    return $this->wer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSSpeechEvaluationMetricsSubModelEvaluationMetric::class, 'Google_Service_CloudNaturalLanguage_XPSSpeechEvaluationMetricsSubModelEvaluationMetric');

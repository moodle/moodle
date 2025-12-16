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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2beta1KnowledgeAnswersAnswer extends \Google\Model
{
  /**
   * Not specified.
   */
  public const MATCH_CONFIDENCE_LEVEL_MATCH_CONFIDENCE_LEVEL_UNSPECIFIED = 'MATCH_CONFIDENCE_LEVEL_UNSPECIFIED';
  /**
   * Indicates that the confidence is low.
   */
  public const MATCH_CONFIDENCE_LEVEL_LOW = 'LOW';
  /**
   * Indicates our confidence is medium.
   */
  public const MATCH_CONFIDENCE_LEVEL_MEDIUM = 'MEDIUM';
  /**
   * Indicates our confidence is high.
   */
  public const MATCH_CONFIDENCE_LEVEL_HIGH = 'HIGH';
  /**
   * The piece of text from the `source` knowledge base document that answers
   * this conversational query.
   *
   * @var string
   */
  public $answer;
  /**
   * The corresponding FAQ question if the answer was extracted from a FAQ
   * Document, empty otherwise.
   *
   * @var string
   */
  public $faqQuestion;
  /**
   * The system's confidence score that this Knowledge answer is a good match
   * for this conversational query. The range is from 0.0 (completely uncertain)
   * to 1.0 (completely certain). Note: The confidence score is likely to vary
   * somewhat (possibly even for identical requests), as the underlying model is
   * under constant improvement. It may be deprecated in the future. We
   * recommend using `match_confidence_level` which should be generally more
   * stable.
   *
   * @var float
   */
  public $matchConfidence;
  /**
   * The system's confidence level that this knowledge answer is a good match
   * for this conversational query. NOTE: The confidence level for a given ``
   * pair may change without notice, as it depends on models that are constantly
   * being improved. However, it will change less frequently than the confidence
   * score below, and should be preferred for referencing the quality of an
   * answer.
   *
   * @var string
   */
  public $matchConfidenceLevel;
  /**
   * Indicates which Knowledge Document this answer was extracted from. Format:
   * `projects//knowledgeBases//documents/`.
   *
   * @var string
   */
  public $source;

  /**
   * The piece of text from the `source` knowledge base document that answers
   * this conversational query.
   *
   * @param string $answer
   */
  public function setAnswer($answer)
  {
    $this->answer = $answer;
  }
  /**
   * @return string
   */
  public function getAnswer()
  {
    return $this->answer;
  }
  /**
   * The corresponding FAQ question if the answer was extracted from a FAQ
   * Document, empty otherwise.
   *
   * @param string $faqQuestion
   */
  public function setFaqQuestion($faqQuestion)
  {
    $this->faqQuestion = $faqQuestion;
  }
  /**
   * @return string
   */
  public function getFaqQuestion()
  {
    return $this->faqQuestion;
  }
  /**
   * The system's confidence score that this Knowledge answer is a good match
   * for this conversational query. The range is from 0.0 (completely uncertain)
   * to 1.0 (completely certain). Note: The confidence score is likely to vary
   * somewhat (possibly even for identical requests), as the underlying model is
   * under constant improvement. It may be deprecated in the future. We
   * recommend using `match_confidence_level` which should be generally more
   * stable.
   *
   * @param float $matchConfidence
   */
  public function setMatchConfidence($matchConfidence)
  {
    $this->matchConfidence = $matchConfidence;
  }
  /**
   * @return float
   */
  public function getMatchConfidence()
  {
    return $this->matchConfidence;
  }
  /**
   * The system's confidence level that this knowledge answer is a good match
   * for this conversational query. NOTE: The confidence level for a given ``
   * pair may change without notice, as it depends on models that are constantly
   * being improved. However, it will change less frequently than the confidence
   * score below, and should be preferred for referencing the quality of an
   * answer.
   *
   * Accepted values: MATCH_CONFIDENCE_LEVEL_UNSPECIFIED, LOW, MEDIUM, HIGH
   *
   * @param self::MATCH_CONFIDENCE_LEVEL_* $matchConfidenceLevel
   */
  public function setMatchConfidenceLevel($matchConfidenceLevel)
  {
    $this->matchConfidenceLevel = $matchConfidenceLevel;
  }
  /**
   * @return self::MATCH_CONFIDENCE_LEVEL_*
   */
  public function getMatchConfidenceLevel()
  {
    return $this->matchConfidenceLevel;
  }
  /**
   * Indicates which Knowledge Document this answer was extracted from. Format:
   * `projects//knowledgeBases//documents/`.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1KnowledgeAnswersAnswer::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1KnowledgeAnswersAnswer');

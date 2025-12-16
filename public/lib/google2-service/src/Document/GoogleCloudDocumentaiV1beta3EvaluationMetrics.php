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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1beta3EvaluationMetrics extends \Google\Model
{
  /**
   * The calculated f1 score.
   *
   * @var float
   */
  public $f1Score;
  /**
   * The amount of false negatives.
   *
   * @var int
   */
  public $falseNegativesCount;
  /**
   * The amount of false positives.
   *
   * @var int
   */
  public $falsePositivesCount;
  /**
   * The amount of documents with a ground truth occurrence.
   *
   * @var int
   */
  public $groundTruthDocumentCount;
  /**
   * The amount of occurrences in ground truth documents.
   *
   * @var int
   */
  public $groundTruthOccurrencesCount;
  /**
   * The calculated precision.
   *
   * @var float
   */
  public $precision;
  /**
   * The amount of documents with a predicted occurrence.
   *
   * @var int
   */
  public $predictedDocumentCount;
  /**
   * The amount of occurrences in predicted documents.
   *
   * @var int
   */
  public $predictedOccurrencesCount;
  /**
   * The calculated recall.
   *
   * @var float
   */
  public $recall;
  /**
   * The amount of documents that had an occurrence of this label.
   *
   * @var int
   */
  public $totalDocumentsCount;
  /**
   * The amount of true positives.
   *
   * @var int
   */
  public $truePositivesCount;

  /**
   * The calculated f1 score.
   *
   * @param float $f1Score
   */
  public function setF1Score($f1Score)
  {
    $this->f1Score = $f1Score;
  }
  /**
   * @return float
   */
  public function getF1Score()
  {
    return $this->f1Score;
  }
  /**
   * The amount of false negatives.
   *
   * @param int $falseNegativesCount
   */
  public function setFalseNegativesCount($falseNegativesCount)
  {
    $this->falseNegativesCount = $falseNegativesCount;
  }
  /**
   * @return int
   */
  public function getFalseNegativesCount()
  {
    return $this->falseNegativesCount;
  }
  /**
   * The amount of false positives.
   *
   * @param int $falsePositivesCount
   */
  public function setFalsePositivesCount($falsePositivesCount)
  {
    $this->falsePositivesCount = $falsePositivesCount;
  }
  /**
   * @return int
   */
  public function getFalsePositivesCount()
  {
    return $this->falsePositivesCount;
  }
  /**
   * The amount of documents with a ground truth occurrence.
   *
   * @param int $groundTruthDocumentCount
   */
  public function setGroundTruthDocumentCount($groundTruthDocumentCount)
  {
    $this->groundTruthDocumentCount = $groundTruthDocumentCount;
  }
  /**
   * @return int
   */
  public function getGroundTruthDocumentCount()
  {
    return $this->groundTruthDocumentCount;
  }
  /**
   * The amount of occurrences in ground truth documents.
   *
   * @param int $groundTruthOccurrencesCount
   */
  public function setGroundTruthOccurrencesCount($groundTruthOccurrencesCount)
  {
    $this->groundTruthOccurrencesCount = $groundTruthOccurrencesCount;
  }
  /**
   * @return int
   */
  public function getGroundTruthOccurrencesCount()
  {
    return $this->groundTruthOccurrencesCount;
  }
  /**
   * The calculated precision.
   *
   * @param float $precision
   */
  public function setPrecision($precision)
  {
    $this->precision = $precision;
  }
  /**
   * @return float
   */
  public function getPrecision()
  {
    return $this->precision;
  }
  /**
   * The amount of documents with a predicted occurrence.
   *
   * @param int $predictedDocumentCount
   */
  public function setPredictedDocumentCount($predictedDocumentCount)
  {
    $this->predictedDocumentCount = $predictedDocumentCount;
  }
  /**
   * @return int
   */
  public function getPredictedDocumentCount()
  {
    return $this->predictedDocumentCount;
  }
  /**
   * The amount of occurrences in predicted documents.
   *
   * @param int $predictedOccurrencesCount
   */
  public function setPredictedOccurrencesCount($predictedOccurrencesCount)
  {
    $this->predictedOccurrencesCount = $predictedOccurrencesCount;
  }
  /**
   * @return int
   */
  public function getPredictedOccurrencesCount()
  {
    return $this->predictedOccurrencesCount;
  }
  /**
   * The calculated recall.
   *
   * @param float $recall
   */
  public function setRecall($recall)
  {
    $this->recall = $recall;
  }
  /**
   * @return float
   */
  public function getRecall()
  {
    return $this->recall;
  }
  /**
   * The amount of documents that had an occurrence of this label.
   *
   * @param int $totalDocumentsCount
   */
  public function setTotalDocumentsCount($totalDocumentsCount)
  {
    $this->totalDocumentsCount = $totalDocumentsCount;
  }
  /**
   * @return int
   */
  public function getTotalDocumentsCount()
  {
    return $this->totalDocumentsCount;
  }
  /**
   * The amount of true positives.
   *
   * @param int $truePositivesCount
   */
  public function setTruePositivesCount($truePositivesCount)
  {
    $this->truePositivesCount = $truePositivesCount;
  }
  /**
   * @return int
   */
  public function getTruePositivesCount()
  {
    return $this->truePositivesCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1beta3EvaluationMetrics::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta3EvaluationMetrics');

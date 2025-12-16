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

namespace Google\Service\Bigquery;

class EvaluationMetrics extends \Google\Model
{
  protected $arimaForecastingMetricsType = ArimaForecastingMetrics::class;
  protected $arimaForecastingMetricsDataType = '';
  protected $binaryClassificationMetricsType = BinaryClassificationMetrics::class;
  protected $binaryClassificationMetricsDataType = '';
  protected $clusteringMetricsType = ClusteringMetrics::class;
  protected $clusteringMetricsDataType = '';
  protected $dimensionalityReductionMetricsType = DimensionalityReductionMetrics::class;
  protected $dimensionalityReductionMetricsDataType = '';
  protected $multiClassClassificationMetricsType = MultiClassClassificationMetrics::class;
  protected $multiClassClassificationMetricsDataType = '';
  protected $rankingMetricsType = RankingMetrics::class;
  protected $rankingMetricsDataType = '';
  protected $regressionMetricsType = RegressionMetrics::class;
  protected $regressionMetricsDataType = '';

  /**
   * Populated for ARIMA models.
   *
   * @param ArimaForecastingMetrics $arimaForecastingMetrics
   */
  public function setArimaForecastingMetrics(ArimaForecastingMetrics $arimaForecastingMetrics)
  {
    $this->arimaForecastingMetrics = $arimaForecastingMetrics;
  }
  /**
   * @return ArimaForecastingMetrics
   */
  public function getArimaForecastingMetrics()
  {
    return $this->arimaForecastingMetrics;
  }
  /**
   * Populated for binary classification/classifier models.
   *
   * @param BinaryClassificationMetrics $binaryClassificationMetrics
   */
  public function setBinaryClassificationMetrics(BinaryClassificationMetrics $binaryClassificationMetrics)
  {
    $this->binaryClassificationMetrics = $binaryClassificationMetrics;
  }
  /**
   * @return BinaryClassificationMetrics
   */
  public function getBinaryClassificationMetrics()
  {
    return $this->binaryClassificationMetrics;
  }
  /**
   * Populated for clustering models.
   *
   * @param ClusteringMetrics $clusteringMetrics
   */
  public function setClusteringMetrics(ClusteringMetrics $clusteringMetrics)
  {
    $this->clusteringMetrics = $clusteringMetrics;
  }
  /**
   * @return ClusteringMetrics
   */
  public function getClusteringMetrics()
  {
    return $this->clusteringMetrics;
  }
  /**
   * Evaluation metrics when the model is a dimensionality reduction model,
   * which currently includes PCA.
   *
   * @param DimensionalityReductionMetrics $dimensionalityReductionMetrics
   */
  public function setDimensionalityReductionMetrics(DimensionalityReductionMetrics $dimensionalityReductionMetrics)
  {
    $this->dimensionalityReductionMetrics = $dimensionalityReductionMetrics;
  }
  /**
   * @return DimensionalityReductionMetrics
   */
  public function getDimensionalityReductionMetrics()
  {
    return $this->dimensionalityReductionMetrics;
  }
  /**
   * Populated for multi-class classification/classifier models.
   *
   * @param MultiClassClassificationMetrics $multiClassClassificationMetrics
   */
  public function setMultiClassClassificationMetrics(MultiClassClassificationMetrics $multiClassClassificationMetrics)
  {
    $this->multiClassClassificationMetrics = $multiClassClassificationMetrics;
  }
  /**
   * @return MultiClassClassificationMetrics
   */
  public function getMultiClassClassificationMetrics()
  {
    return $this->multiClassClassificationMetrics;
  }
  /**
   * Populated for implicit feedback type matrix factorization models.
   *
   * @param RankingMetrics $rankingMetrics
   */
  public function setRankingMetrics(RankingMetrics $rankingMetrics)
  {
    $this->rankingMetrics = $rankingMetrics;
  }
  /**
   * @return RankingMetrics
   */
  public function getRankingMetrics()
  {
    return $this->rankingMetrics;
  }
  /**
   * Populated for regression models and explicit feedback type matrix
   * factorization models.
   *
   * @param RegressionMetrics $regressionMetrics
   */
  public function setRegressionMetrics(RegressionMetrics $regressionMetrics)
  {
    $this->regressionMetrics = $regressionMetrics;
  }
  /**
   * @return RegressionMetrics
   */
  public function getRegressionMetrics()
  {
    return $this->regressionMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EvaluationMetrics::class, 'Google_Service_Bigquery_EvaluationMetrics');

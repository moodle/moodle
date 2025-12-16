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

class MlStatistics extends \Google\Collection
{
  /**
   * Default value.
   */
  public const MODEL_TYPE_MODEL_TYPE_UNSPECIFIED = 'MODEL_TYPE_UNSPECIFIED';
  /**
   * Linear regression model.
   */
  public const MODEL_TYPE_LINEAR_REGRESSION = 'LINEAR_REGRESSION';
  /**
   * Logistic regression based classification model.
   */
  public const MODEL_TYPE_LOGISTIC_REGRESSION = 'LOGISTIC_REGRESSION';
  /**
   * K-means clustering model.
   */
  public const MODEL_TYPE_KMEANS = 'KMEANS';
  /**
   * Matrix factorization model.
   */
  public const MODEL_TYPE_MATRIX_FACTORIZATION = 'MATRIX_FACTORIZATION';
  /**
   * DNN classifier model.
   */
  public const MODEL_TYPE_DNN_CLASSIFIER = 'DNN_CLASSIFIER';
  /**
   * An imported TensorFlow model.
   */
  public const MODEL_TYPE_TENSORFLOW = 'TENSORFLOW';
  /**
   * DNN regressor model.
   */
  public const MODEL_TYPE_DNN_REGRESSOR = 'DNN_REGRESSOR';
  /**
   * An imported XGBoost model.
   */
  public const MODEL_TYPE_XGBOOST = 'XGBOOST';
  /**
   * Boosted tree regressor model.
   */
  public const MODEL_TYPE_BOOSTED_TREE_REGRESSOR = 'BOOSTED_TREE_REGRESSOR';
  /**
   * Boosted tree classifier model.
   */
  public const MODEL_TYPE_BOOSTED_TREE_CLASSIFIER = 'BOOSTED_TREE_CLASSIFIER';
  /**
   * ARIMA model.
   */
  public const MODEL_TYPE_ARIMA = 'ARIMA';
  /**
   * AutoML Tables regression model.
   */
  public const MODEL_TYPE_AUTOML_REGRESSOR = 'AUTOML_REGRESSOR';
  /**
   * AutoML Tables classification model.
   */
  public const MODEL_TYPE_AUTOML_CLASSIFIER = 'AUTOML_CLASSIFIER';
  /**
   * Prinpical Component Analysis model.
   */
  public const MODEL_TYPE_PCA = 'PCA';
  /**
   * Wide-and-deep classifier model.
   */
  public const MODEL_TYPE_DNN_LINEAR_COMBINED_CLASSIFIER = 'DNN_LINEAR_COMBINED_CLASSIFIER';
  /**
   * Wide-and-deep regressor model.
   */
  public const MODEL_TYPE_DNN_LINEAR_COMBINED_REGRESSOR = 'DNN_LINEAR_COMBINED_REGRESSOR';
  /**
   * Autoencoder model.
   */
  public const MODEL_TYPE_AUTOENCODER = 'AUTOENCODER';
  /**
   * New name for the ARIMA model.
   */
  public const MODEL_TYPE_ARIMA_PLUS = 'ARIMA_PLUS';
  /**
   * ARIMA with external regressors.
   */
  public const MODEL_TYPE_ARIMA_PLUS_XREG = 'ARIMA_PLUS_XREG';
  /**
   * Random forest regressor model.
   */
  public const MODEL_TYPE_RANDOM_FOREST_REGRESSOR = 'RANDOM_FOREST_REGRESSOR';
  /**
   * Random forest classifier model.
   */
  public const MODEL_TYPE_RANDOM_FOREST_CLASSIFIER = 'RANDOM_FOREST_CLASSIFIER';
  /**
   * An imported TensorFlow Lite model.
   */
  public const MODEL_TYPE_TENSORFLOW_LITE = 'TENSORFLOW_LITE';
  /**
   * An imported ONNX model.
   */
  public const MODEL_TYPE_ONNX = 'ONNX';
  /**
   * Model to capture the columns and logic in the TRANSFORM clause along with
   * statistics useful for ML analytic functions.
   */
  public const MODEL_TYPE_TRANSFORM_ONLY = 'TRANSFORM_ONLY';
  /**
   * The contribution analysis model.
   */
  public const MODEL_TYPE_CONTRIBUTION_ANALYSIS = 'CONTRIBUTION_ANALYSIS';
  /**
   * Unspecified training type.
   */
  public const TRAINING_TYPE_TRAINING_TYPE_UNSPECIFIED = 'TRAINING_TYPE_UNSPECIFIED';
  /**
   * Single training with fixed parameter space.
   */
  public const TRAINING_TYPE_SINGLE_TRAINING = 'SINGLE_TRAINING';
  /**
   * [Hyperparameter tuning training](https://cloud.google.com/bigquery-
   * ml/docs/reference/standard-sql/bigqueryml-syntax-hp-tuning-overview).
   */
  public const TRAINING_TYPE_HPARAM_TUNING = 'HPARAM_TUNING';
  protected $collection_key = 'iterationResults';
  protected $hparamTrialsType = HparamTuningTrial::class;
  protected $hparamTrialsDataType = 'array';
  protected $iterationResultsType = IterationResult::class;
  protected $iterationResultsDataType = 'array';
  /**
   * Output only. Maximum number of iterations specified as max_iterations in
   * the 'CREATE MODEL' query. The actual number of iterations may be less than
   * this number due to early stop.
   *
   * @var string
   */
  public $maxIterations;
  /**
   * Output only. The type of the model that is being trained.
   *
   * @var string
   */
  public $modelType;
  /**
   * Output only. Training type of the job.
   *
   * @var string
   */
  public $trainingType;

  /**
   * Output only. Trials of a [hyperparameter tuning
   * job](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-hp-tuning-overview) sorted by trial_id.
   *
   * @param HparamTuningTrial[] $hparamTrials
   */
  public function setHparamTrials($hparamTrials)
  {
    $this->hparamTrials = $hparamTrials;
  }
  /**
   * @return HparamTuningTrial[]
   */
  public function getHparamTrials()
  {
    return $this->hparamTrials;
  }
  /**
   * Results for all completed iterations. Empty for [hyperparameter tuning
   * jobs](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-hp-tuning-overview).
   *
   * @param IterationResult[] $iterationResults
   */
  public function setIterationResults($iterationResults)
  {
    $this->iterationResults = $iterationResults;
  }
  /**
   * @return IterationResult[]
   */
  public function getIterationResults()
  {
    return $this->iterationResults;
  }
  /**
   * Output only. Maximum number of iterations specified as max_iterations in
   * the 'CREATE MODEL' query. The actual number of iterations may be less than
   * this number due to early stop.
   *
   * @param string $maxIterations
   */
  public function setMaxIterations($maxIterations)
  {
    $this->maxIterations = $maxIterations;
  }
  /**
   * @return string
   */
  public function getMaxIterations()
  {
    return $this->maxIterations;
  }
  /**
   * Output only. The type of the model that is being trained.
   *
   * Accepted values: MODEL_TYPE_UNSPECIFIED, LINEAR_REGRESSION,
   * LOGISTIC_REGRESSION, KMEANS, MATRIX_FACTORIZATION, DNN_CLASSIFIER,
   * TENSORFLOW, DNN_REGRESSOR, XGBOOST, BOOSTED_TREE_REGRESSOR,
   * BOOSTED_TREE_CLASSIFIER, ARIMA, AUTOML_REGRESSOR, AUTOML_CLASSIFIER, PCA,
   * DNN_LINEAR_COMBINED_CLASSIFIER, DNN_LINEAR_COMBINED_REGRESSOR, AUTOENCODER,
   * ARIMA_PLUS, ARIMA_PLUS_XREG, RANDOM_FOREST_REGRESSOR,
   * RANDOM_FOREST_CLASSIFIER, TENSORFLOW_LITE, ONNX, TRANSFORM_ONLY,
   * CONTRIBUTION_ANALYSIS
   *
   * @param self::MODEL_TYPE_* $modelType
   */
  public function setModelType($modelType)
  {
    $this->modelType = $modelType;
  }
  /**
   * @return self::MODEL_TYPE_*
   */
  public function getModelType()
  {
    return $this->modelType;
  }
  /**
   * Output only. Training type of the job.
   *
   * Accepted values: TRAINING_TYPE_UNSPECIFIED, SINGLE_TRAINING, HPARAM_TUNING
   *
   * @param self::TRAINING_TYPE_* $trainingType
   */
  public function setTrainingType($trainingType)
  {
    $this->trainingType = $trainingType;
  }
  /**
   * @return self::TRAINING_TYPE_*
   */
  public function getTrainingType()
  {
    return $this->trainingType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MlStatistics::class, 'Google_Service_Bigquery_MlStatistics');

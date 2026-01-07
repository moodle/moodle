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

class Model extends \Google\Collection
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
  protected $collection_key = 'transformColumns';
  /**
   * The best trial_id across all training runs.
   *
   * @deprecated
   * @var string
   */
  public $bestTrialId;
  /**
   * Output only. The time when this model was created, in millisecs since the
   * epoch.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Output only. The default trial_id to use in TVFs when the trial_id is not
   * passed in. For single-objective [hyperparameter
   * tuning](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-hp-tuning-overview) models, this is the best trial
   * ID. For multi-objective [hyperparameter
   * tuning](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-hp-tuning-overview) models, this is the smallest
   * trial ID among all Pareto optimal trials.
   *
   * @var string
   */
  public $defaultTrialId;
  /**
   * Optional. A user-friendly description of this model.
   *
   * @var string
   */
  public $description;
  protected $encryptionConfigurationType = EncryptionConfiguration::class;
  protected $encryptionConfigurationDataType = '';
  /**
   * Output only. A hash of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The time when this model expires, in milliseconds since the
   * epoch. If not present, the model will persist indefinitely. Expired models
   * will be deleted and their storage reclaimed. The defaultTableExpirationMs
   * property of the encapsulating dataset can be used to set a default
   * expirationTime on newly created models.
   *
   * @var string
   */
  public $expirationTime;
  protected $featureColumnsType = StandardSqlField::class;
  protected $featureColumnsDataType = 'array';
  /**
   * Optional. A descriptive name for this model.
   *
   * @var string
   */
  public $friendlyName;
  protected $hparamSearchSpacesType = HparamSearchSpaces::class;
  protected $hparamSearchSpacesDataType = '';
  protected $hparamTrialsType = HparamTuningTrial::class;
  protected $hparamTrialsDataType = 'array';
  protected $labelColumnsType = StandardSqlField::class;
  protected $labelColumnsDataType = 'array';
  /**
   * The labels associated with this model. You can use these to organize and
   * group your models. Label keys and values can be no longer than 63
   * characters, can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. Label values
   * are optional. Label keys must start with a letter and each label in the
   * list must have a different key.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The time when this model was last modified, in millisecs since
   * the epoch.
   *
   * @var string
   */
  public $lastModifiedTime;
  /**
   * Output only. The geographic location where the model resides. This value is
   * inherited from the dataset.
   *
   * @var string
   */
  public $location;
  protected $modelReferenceType = ModelReference::class;
  protected $modelReferenceDataType = '';
  /**
   * Output only. Type of the model resource.
   *
   * @var string
   */
  public $modelType;
  /**
   * Output only. For single-objective [hyperparameter
   * tuning](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-hp-tuning-overview) models, it only contains the best
   * trial. For multi-objective [hyperparameter
   * tuning](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-hp-tuning-overview) models, it contains all Pareto
   * optimal trials sorted by trial_id.
   *
   * @var string[]
   */
  public $optimalTrialIds;
  protected $remoteModelInfoType = RemoteModelInfo::class;
  protected $remoteModelInfoDataType = '';
  protected $trainingRunsType = TrainingRun::class;
  protected $trainingRunsDataType = 'array';
  protected $transformColumnsType = TransformColumn::class;
  protected $transformColumnsDataType = 'array';

  /**
   * The best trial_id across all training runs.
   *
   * @deprecated
   * @param string $bestTrialId
   */
  public function setBestTrialId($bestTrialId)
  {
    $this->bestTrialId = $bestTrialId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBestTrialId()
  {
    return $this->bestTrialId;
  }
  /**
   * Output only. The time when this model was created, in millisecs since the
   * epoch.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Output only. The default trial_id to use in TVFs when the trial_id is not
   * passed in. For single-objective [hyperparameter
   * tuning](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-hp-tuning-overview) models, this is the best trial
   * ID. For multi-objective [hyperparameter
   * tuning](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-hp-tuning-overview) models, this is the smallest
   * trial ID among all Pareto optimal trials.
   *
   * @param string $defaultTrialId
   */
  public function setDefaultTrialId($defaultTrialId)
  {
    $this->defaultTrialId = $defaultTrialId;
  }
  /**
   * @return string
   */
  public function getDefaultTrialId()
  {
    return $this->defaultTrialId;
  }
  /**
   * Optional. A user-friendly description of this model.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Custom encryption configuration (e.g., Cloud KMS keys). This shows the
   * encryption configuration of the model data while stored in BigQuery
   * storage. This field can be used with PatchModel to update encryption key
   * for an already encrypted model.
   *
   * @param EncryptionConfiguration $encryptionConfiguration
   */
  public function setEncryptionConfiguration(EncryptionConfiguration $encryptionConfiguration)
  {
    $this->encryptionConfiguration = $encryptionConfiguration;
  }
  /**
   * @return EncryptionConfiguration
   */
  public function getEncryptionConfiguration()
  {
    return $this->encryptionConfiguration;
  }
  /**
   * Output only. A hash of this resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. The time when this model expires, in milliseconds since the
   * epoch. If not present, the model will persist indefinitely. Expired models
   * will be deleted and their storage reclaimed. The defaultTableExpirationMs
   * property of the encapsulating dataset can be used to set a default
   * expirationTime on newly created models.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * Output only. Input feature columns for the model inference. If the model is
   * trained with TRANSFORM clause, these are the input of the TRANSFORM clause.
   *
   * @param StandardSqlField[] $featureColumns
   */
  public function setFeatureColumns($featureColumns)
  {
    $this->featureColumns = $featureColumns;
  }
  /**
   * @return StandardSqlField[]
   */
  public function getFeatureColumns()
  {
    return $this->featureColumns;
  }
  /**
   * Optional. A descriptive name for this model.
   *
   * @param string $friendlyName
   */
  public function setFriendlyName($friendlyName)
  {
    $this->friendlyName = $friendlyName;
  }
  /**
   * @return string
   */
  public function getFriendlyName()
  {
    return $this->friendlyName;
  }
  /**
   * Output only. All hyperparameter search spaces in this model.
   *
   * @param HparamSearchSpaces $hparamSearchSpaces
   */
  public function setHparamSearchSpaces(HparamSearchSpaces $hparamSearchSpaces)
  {
    $this->hparamSearchSpaces = $hparamSearchSpaces;
  }
  /**
   * @return HparamSearchSpaces
   */
  public function getHparamSearchSpaces()
  {
    return $this->hparamSearchSpaces;
  }
  /**
   * Output only. Trials of a [hyperparameter
   * tuning](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-hp-tuning-overview) model sorted by trial_id.
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
   * Output only. Label columns that were used to train this model. The output
   * of the model will have a "predicted_" prefix to these columns.
   *
   * @param StandardSqlField[] $labelColumns
   */
  public function setLabelColumns($labelColumns)
  {
    $this->labelColumns = $labelColumns;
  }
  /**
   * @return StandardSqlField[]
   */
  public function getLabelColumns()
  {
    return $this->labelColumns;
  }
  /**
   * The labels associated with this model. You can use these to organize and
   * group your models. Label keys and values can be no longer than 63
   * characters, can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. Label values
   * are optional. Label keys must start with a letter and each label in the
   * list must have a different key.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The time when this model was last modified, in millisecs since
   * the epoch.
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * Output only. The geographic location where the model resides. This value is
   * inherited from the dataset.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Required. Unique identifier for this model.
   *
   * @param ModelReference $modelReference
   */
  public function setModelReference(ModelReference $modelReference)
  {
    $this->modelReference = $modelReference;
  }
  /**
   * @return ModelReference
   */
  public function getModelReference()
  {
    return $this->modelReference;
  }
  /**
   * Output only. Type of the model resource.
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
   * Output only. For single-objective [hyperparameter
   * tuning](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-hp-tuning-overview) models, it only contains the best
   * trial. For multi-objective [hyperparameter
   * tuning](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-hp-tuning-overview) models, it contains all Pareto
   * optimal trials sorted by trial_id.
   *
   * @param string[] $optimalTrialIds
   */
  public function setOptimalTrialIds($optimalTrialIds)
  {
    $this->optimalTrialIds = $optimalTrialIds;
  }
  /**
   * @return string[]
   */
  public function getOptimalTrialIds()
  {
    return $this->optimalTrialIds;
  }
  /**
   * Output only. Remote model info
   *
   * @param RemoteModelInfo $remoteModelInfo
   */
  public function setRemoteModelInfo(RemoteModelInfo $remoteModelInfo)
  {
    $this->remoteModelInfo = $remoteModelInfo;
  }
  /**
   * @return RemoteModelInfo
   */
  public function getRemoteModelInfo()
  {
    return $this->remoteModelInfo;
  }
  /**
   * Information for all training runs in increasing order of start_time.
   *
   * @param TrainingRun[] $trainingRuns
   */
  public function setTrainingRuns($trainingRuns)
  {
    $this->trainingRuns = $trainingRuns;
  }
  /**
   * @return TrainingRun[]
   */
  public function getTrainingRuns()
  {
    return $this->trainingRuns;
  }
  /**
   * Output only. This field will be populated if a TRANSFORM clause was used to
   * train a model. TRANSFORM clause (if used) takes feature_columns as input
   * and outputs transform_columns. transform_columns then are used to train the
   * model.
   *
   * @param TransformColumn[] $transformColumns
   */
  public function setTransformColumns($transformColumns)
  {
    $this->transformColumns = $transformColumns;
  }
  /**
   * @return TransformColumn[]
   */
  public function getTransformColumns()
  {
    return $this->transformColumns;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Model::class, 'Google_Service_Bigquery_Model');

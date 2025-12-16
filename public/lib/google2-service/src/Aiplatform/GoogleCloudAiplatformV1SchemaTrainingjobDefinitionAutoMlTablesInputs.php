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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputs extends \Google\Collection
{
  protected $collection_key = 'transformations';
  /**
   * Additional experiment flags for the Tables training pipeline.
   *
   * @var string[]
   */
  public $additionalExperiments;
  /**
   * Use the entire training budget. This disables the early stopping feature.
   * By default, the early stopping feature is enabled, which means that AutoML
   * Tables might stop training before the entire training budget has been used.
   *
   * @var bool
   */
  public $disableEarlyStopping;
  protected $exportEvaluatedDataItemsConfigType = GoogleCloudAiplatformV1SchemaTrainingjobDefinitionExportEvaluatedDataItemsConfig::class;
  protected $exportEvaluatedDataItemsConfigDataType = '';
  /**
   * Objective function the model is optimizing towards. The training process
   * creates a model that maximizes/minimizes the value of the objective
   * function over the validation set. The supported optimization objectives
   * depend on the prediction type. If the field is not set, a default objective
   * function is used. classification (binary): "maximize-au-roc" (default) -
   * Maximize the area under the receiver operating characteristic (ROC) curve.
   * "minimize-log-loss" - Minimize log loss. "maximize-au-prc" - Maximize the
   * area under the precision-recall curve. "maximize-precision-at-recall" -
   * Maximize precision for a specified recall value. "maximize-recall-at-
   * precision" - Maximize recall for a specified precision value.
   * classification (multi-class): "minimize-log-loss" (default) - Minimize log
   * loss. regression: "minimize-rmse" (default) - Minimize root-mean-squared
   * error (RMSE). "minimize-mae" - Minimize mean-absolute error (MAE).
   * "minimize-rmsle" - Minimize root-mean-squared log error (RMSLE).
   *
   * @var string
   */
  public $optimizationObjective;
  /**
   * Required when optimization_objective is "maximize-recall-at-precision".
   * Must be between 0 and 1, inclusive.
   *
   * @var float
   */
  public $optimizationObjectivePrecisionValue;
  /**
   * Required when optimization_objective is "maximize-precision-at-recall".
   * Must be between 0 and 1, inclusive.
   *
   * @var float
   */
  public $optimizationObjectiveRecallValue;
  /**
   * The type of prediction the Model is to produce. "classification" - Predict
   * one out of multiple target values is picked for each row. "regression" -
   * Predict a value based on its relation to other values. This type is
   * available only to columns that contain semantically numeric values, i.e.
   * integers or floating point number, even if stored as e.g. strings.
   *
   * @var string
   */
  public $predictionType;
  /**
   * The column name of the target column that the model is to predict.
   *
   * @var string
   */
  public $targetColumn;
  /**
   * Required. The train budget of creating this model, expressed in milli node
   * hours i.e. 1,000 value in this field means 1 node hour. The training cost
   * of the model will not exceed this budget. The final cost will be attempted
   * to be close to the budget, though may end up being (even) noticeably
   * smaller - at the backend's discretion. This especially may happen when
   * further model training ceases to provide any improvements. If the budget is
   * set to a value known to be insufficient to train a model for the given
   * dataset, the training won't be attempted and will error. The train budget
   * must be between 1,000 and 72,000 milli node hours, inclusive.
   *
   * @var string
   */
  public $trainBudgetMilliNodeHours;
  protected $transformationsType = GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputsTransformation::class;
  protected $transformationsDataType = 'array';
  /**
   * Column name that should be used as the weight column. Higher values in this
   * column give more importance to the row during model training. The column
   * must have numeric values between 0 and 10000 inclusively; 0 means the row
   * is ignored for training. If weight column field is not set, then all rows
   * are assumed to have equal weight of 1.
   *
   * @var string
   */
  public $weightColumnName;

  /**
   * Additional experiment flags for the Tables training pipeline.
   *
   * @param string[] $additionalExperiments
   */
  public function setAdditionalExperiments($additionalExperiments)
  {
    $this->additionalExperiments = $additionalExperiments;
  }
  /**
   * @return string[]
   */
  public function getAdditionalExperiments()
  {
    return $this->additionalExperiments;
  }
  /**
   * Use the entire training budget. This disables the early stopping feature.
   * By default, the early stopping feature is enabled, which means that AutoML
   * Tables might stop training before the entire training budget has been used.
   *
   * @param bool $disableEarlyStopping
   */
  public function setDisableEarlyStopping($disableEarlyStopping)
  {
    $this->disableEarlyStopping = $disableEarlyStopping;
  }
  /**
   * @return bool
   */
  public function getDisableEarlyStopping()
  {
    return $this->disableEarlyStopping;
  }
  /**
   * Configuration for exporting test set predictions to a BigQuery table. If
   * this configuration is absent, then the export is not performed.
   *
   * @param GoogleCloudAiplatformV1SchemaTrainingjobDefinitionExportEvaluatedDataItemsConfig $exportEvaluatedDataItemsConfig
   */
  public function setExportEvaluatedDataItemsConfig(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionExportEvaluatedDataItemsConfig $exportEvaluatedDataItemsConfig)
  {
    $this->exportEvaluatedDataItemsConfig = $exportEvaluatedDataItemsConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaTrainingjobDefinitionExportEvaluatedDataItemsConfig
   */
  public function getExportEvaluatedDataItemsConfig()
  {
    return $this->exportEvaluatedDataItemsConfig;
  }
  /**
   * Objective function the model is optimizing towards. The training process
   * creates a model that maximizes/minimizes the value of the objective
   * function over the validation set. The supported optimization objectives
   * depend on the prediction type. If the field is not set, a default objective
   * function is used. classification (binary): "maximize-au-roc" (default) -
   * Maximize the area under the receiver operating characteristic (ROC) curve.
   * "minimize-log-loss" - Minimize log loss. "maximize-au-prc" - Maximize the
   * area under the precision-recall curve. "maximize-precision-at-recall" -
   * Maximize precision for a specified recall value. "maximize-recall-at-
   * precision" - Maximize recall for a specified precision value.
   * classification (multi-class): "minimize-log-loss" (default) - Minimize log
   * loss. regression: "minimize-rmse" (default) - Minimize root-mean-squared
   * error (RMSE). "minimize-mae" - Minimize mean-absolute error (MAE).
   * "minimize-rmsle" - Minimize root-mean-squared log error (RMSLE).
   *
   * @param string $optimizationObjective
   */
  public function setOptimizationObjective($optimizationObjective)
  {
    $this->optimizationObjective = $optimizationObjective;
  }
  /**
   * @return string
   */
  public function getOptimizationObjective()
  {
    return $this->optimizationObjective;
  }
  /**
   * Required when optimization_objective is "maximize-recall-at-precision".
   * Must be between 0 and 1, inclusive.
   *
   * @param float $optimizationObjectivePrecisionValue
   */
  public function setOptimizationObjectivePrecisionValue($optimizationObjectivePrecisionValue)
  {
    $this->optimizationObjectivePrecisionValue = $optimizationObjectivePrecisionValue;
  }
  /**
   * @return float
   */
  public function getOptimizationObjectivePrecisionValue()
  {
    return $this->optimizationObjectivePrecisionValue;
  }
  /**
   * Required when optimization_objective is "maximize-precision-at-recall".
   * Must be between 0 and 1, inclusive.
   *
   * @param float $optimizationObjectiveRecallValue
   */
  public function setOptimizationObjectiveRecallValue($optimizationObjectiveRecallValue)
  {
    $this->optimizationObjectiveRecallValue = $optimizationObjectiveRecallValue;
  }
  /**
   * @return float
   */
  public function getOptimizationObjectiveRecallValue()
  {
    return $this->optimizationObjectiveRecallValue;
  }
  /**
   * The type of prediction the Model is to produce. "classification" - Predict
   * one out of multiple target values is picked for each row. "regression" -
   * Predict a value based on its relation to other values. This type is
   * available only to columns that contain semantically numeric values, i.e.
   * integers or floating point number, even if stored as e.g. strings.
   *
   * @param string $predictionType
   */
  public function setPredictionType($predictionType)
  {
    $this->predictionType = $predictionType;
  }
  /**
   * @return string
   */
  public function getPredictionType()
  {
    return $this->predictionType;
  }
  /**
   * The column name of the target column that the model is to predict.
   *
   * @param string $targetColumn
   */
  public function setTargetColumn($targetColumn)
  {
    $this->targetColumn = $targetColumn;
  }
  /**
   * @return string
   */
  public function getTargetColumn()
  {
    return $this->targetColumn;
  }
  /**
   * Required. The train budget of creating this model, expressed in milli node
   * hours i.e. 1,000 value in this field means 1 node hour. The training cost
   * of the model will not exceed this budget. The final cost will be attempted
   * to be close to the budget, though may end up being (even) noticeably
   * smaller - at the backend's discretion. This especially may happen when
   * further model training ceases to provide any improvements. If the budget is
   * set to a value known to be insufficient to train a model for the given
   * dataset, the training won't be attempted and will error. The train budget
   * must be between 1,000 and 72,000 milli node hours, inclusive.
   *
   * @param string $trainBudgetMilliNodeHours
   */
  public function setTrainBudgetMilliNodeHours($trainBudgetMilliNodeHours)
  {
    $this->trainBudgetMilliNodeHours = $trainBudgetMilliNodeHours;
  }
  /**
   * @return string
   */
  public function getTrainBudgetMilliNodeHours()
  {
    return $this->trainBudgetMilliNodeHours;
  }
  /**
   * Each transformation will apply transform function to given input column.
   * And the result will be used for training. When creating transformation for
   * BigQuery Struct column, the column should be flattened using "." as the
   * delimiter.
   *
   * @param GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputsTransformation[] $transformations
   */
  public function setTransformations($transformations)
  {
    $this->transformations = $transformations;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputsTransformation[]
   */
  public function getTransformations()
  {
    return $this->transformations;
  }
  /**
   * Column name that should be used as the weight column. Higher values in this
   * column give more importance to the row during model training. The column
   * must have numeric values between 0 and 10000 inclusively; 0 means the row
   * is ignored for training. If weight column field is not set, then all rows
   * are assumed to have equal weight of 1.
   *
   * @param string $weightColumnName
   */
  public function setWeightColumnName($weightColumnName)
  {
    $this->weightColumnName = $weightColumnName;
  }
  /**
   * @return string
   */
  public function getWeightColumnName()
  {
    return $this->weightColumnName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputs::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTablesInputs');

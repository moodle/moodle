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

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionTftForecastingInputs extends \Google\Collection
{
  protected $collection_key = 'unavailableAtForecastColumns';
  /**
   * Additional experiment flags for the time series forcasting training.
   *
   * @var string[]
   */
  public $additionalExperiments;
  /**
   * Names of columns that are available and provided when a forecast is
   * requested. These columns contain information for the given entity
   * (identified by the time_series_identifier_column column) that is known at
   * forecast. For example, predicted weather for a specific day.
   *
   * @var string[]
   */
  public $availableAtForecastColumns;
  /**
   * The amount of time into the past training and prediction data is used for
   * model training and prediction respectively. Expressed in number of units
   * defined by the `data_granularity` field.
   *
   * @var string
   */
  public $contextWindow;
  protected $dataGranularityType = GoogleCloudAiplatformV1SchemaTrainingjobDefinitionTftForecastingInputsGranularity::class;
  protected $dataGranularityDataType = '';
  protected $exportEvaluatedDataItemsConfigType = GoogleCloudAiplatformV1SchemaTrainingjobDefinitionExportEvaluatedDataItemsConfig::class;
  protected $exportEvaluatedDataItemsConfigDataType = '';
  /**
   * The amount of time into the future for which forecasted values for the
   * target are returned. Expressed in number of units defined by the
   * `data_granularity` field.
   *
   * @var string
   */
  public $forecastHorizon;
  protected $hierarchyConfigType = GoogleCloudAiplatformV1SchemaTrainingjobDefinitionHierarchyConfig::class;
  protected $hierarchyConfigDataType = '';
  /**
   * The geographical region based on which the holiday effect is applied in
   * modeling by adding holiday categorical array feature that include all
   * holidays matching the date. This option only allowed when data_granularity
   * is day. By default, holiday effect modeling is disabled. To turn it on,
   * specify the holiday region using this option.
   *
   * @var string[]
   */
  public $holidayRegions;
  /**
   * Objective function the model is optimizing towards. The training process
   * creates a model that optimizes the value of the objective function over the
   * validation set. The supported optimization objectives: * "minimize-rmse"
   * (default) - Minimize root-mean-squared error (RMSE). * "minimize-mae" -
   * Minimize mean-absolute error (MAE). * "minimize-rmsle" - Minimize root-
   * mean-squared log error (RMSLE). * "minimize-rmspe" - Minimize root-mean-
   * squared percentage error (RMSPE). * "minimize-wape-mae" - Minimize the
   * combination of weighted absolute percentage error (WAPE) and mean-absolute-
   * error (MAE). * "minimize-quantile-loss" - Minimize the quantile loss at the
   * quantiles defined in `quantiles`. * "minimize-mape" - Minimize the mean
   * absolute percentage error.
   *
   * @var string
   */
  public $optimizationObjective;
  /**
   * Quantiles to use for minimize-quantile-loss `optimization_objective`. Up to
   * 5 quantiles are allowed of values between 0 and 1, exclusive. Required if
   * the value of optimization_objective is minimize-quantile-loss. Represents
   * the percent quantiles to use for that objective. Quantiles must be unique.
   *
   * @var []
   */
  public $quantiles;
  /**
   * The name of the column that the Model is to predict values for. This column
   * must be unavailable at forecast.
   *
   * @var string
   */
  public $targetColumn;
  /**
   * The name of the column that identifies time order in the time series. This
   * column must be available at forecast.
   *
   * @var string
   */
  public $timeColumn;
  /**
   * Column names that should be used as attribute columns. The value of these
   * columns does not vary as a function of time. For example, store ID or item
   * color.
   *
   * @var string[]
   */
  public $timeSeriesAttributeColumns;
  /**
   * The name of the column that identifies the time series.
   *
   * @var string
   */
  public $timeSeriesIdentifierColumn;
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
  protected $transformationsType = GoogleCloudAiplatformV1SchemaTrainingjobDefinitionTftForecastingInputsTransformation::class;
  protected $transformationsDataType = 'array';
  /**
   * Names of columns that are unavailable when a forecast is requested. This
   * column contains information for the given entity (identified by the
   * time_series_identifier_column) that is unknown before the forecast For
   * example, actual weather on a given day.
   *
   * @var string[]
   */
  public $unavailableAtForecastColumns;
  /**
   * Validation options for the data validation component. The available options
   * are: * "fail-pipeline" - default, will validate against the validation and
   * fail the pipeline if it fails. * "ignore-validation" - ignore the results
   * of the validation and continue
   *
   * @var string
   */
  public $validationOptions;
  /**
   * Column name that should be used as the weight column. Higher values in this
   * column give more importance to the row during model training. The column
   * must have numeric values between 0 and 10000 inclusively; 0 means the row
   * is ignored for training. If weight column field is not set, then all rows
   * are assumed to have equal weight of 1. This column must be available at
   * forecast.
   *
   * @var string
   */
  public $weightColumn;
  protected $windowConfigType = GoogleCloudAiplatformV1SchemaTrainingjobDefinitionWindowConfig::class;
  protected $windowConfigDataType = '';

  /**
   * Additional experiment flags for the time series forcasting training.
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
   * Names of columns that are available and provided when a forecast is
   * requested. These columns contain information for the given entity
   * (identified by the time_series_identifier_column column) that is known at
   * forecast. For example, predicted weather for a specific day.
   *
   * @param string[] $availableAtForecastColumns
   */
  public function setAvailableAtForecastColumns($availableAtForecastColumns)
  {
    $this->availableAtForecastColumns = $availableAtForecastColumns;
  }
  /**
   * @return string[]
   */
  public function getAvailableAtForecastColumns()
  {
    return $this->availableAtForecastColumns;
  }
  /**
   * The amount of time into the past training and prediction data is used for
   * model training and prediction respectively. Expressed in number of units
   * defined by the `data_granularity` field.
   *
   * @param string $contextWindow
   */
  public function setContextWindow($contextWindow)
  {
    $this->contextWindow = $contextWindow;
  }
  /**
   * @return string
   */
  public function getContextWindow()
  {
    return $this->contextWindow;
  }
  /**
   * Expected difference in time granularity between rows in the data.
   *
   * @param GoogleCloudAiplatformV1SchemaTrainingjobDefinitionTftForecastingInputsGranularity $dataGranularity
   */
  public function setDataGranularity(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionTftForecastingInputsGranularity $dataGranularity)
  {
    $this->dataGranularity = $dataGranularity;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaTrainingjobDefinitionTftForecastingInputsGranularity
   */
  public function getDataGranularity()
  {
    return $this->dataGranularity;
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
   * The amount of time into the future for which forecasted values for the
   * target are returned. Expressed in number of units defined by the
   * `data_granularity` field.
   *
   * @param string $forecastHorizon
   */
  public function setForecastHorizon($forecastHorizon)
  {
    $this->forecastHorizon = $forecastHorizon;
  }
  /**
   * @return string
   */
  public function getForecastHorizon()
  {
    return $this->forecastHorizon;
  }
  /**
   * Configuration that defines the hierarchical relationship of time series and
   * parameters for hierarchical forecasting strategies.
   *
   * @param GoogleCloudAiplatformV1SchemaTrainingjobDefinitionHierarchyConfig $hierarchyConfig
   */
  public function setHierarchyConfig(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionHierarchyConfig $hierarchyConfig)
  {
    $this->hierarchyConfig = $hierarchyConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaTrainingjobDefinitionHierarchyConfig
   */
  public function getHierarchyConfig()
  {
    return $this->hierarchyConfig;
  }
  /**
   * The geographical region based on which the holiday effect is applied in
   * modeling by adding holiday categorical array feature that include all
   * holidays matching the date. This option only allowed when data_granularity
   * is day. By default, holiday effect modeling is disabled. To turn it on,
   * specify the holiday region using this option.
   *
   * @param string[] $holidayRegions
   */
  public function setHolidayRegions($holidayRegions)
  {
    $this->holidayRegions = $holidayRegions;
  }
  /**
   * @return string[]
   */
  public function getHolidayRegions()
  {
    return $this->holidayRegions;
  }
  /**
   * Objective function the model is optimizing towards. The training process
   * creates a model that optimizes the value of the objective function over the
   * validation set. The supported optimization objectives: * "minimize-rmse"
   * (default) - Minimize root-mean-squared error (RMSE). * "minimize-mae" -
   * Minimize mean-absolute error (MAE). * "minimize-rmsle" - Minimize root-
   * mean-squared log error (RMSLE). * "minimize-rmspe" - Minimize root-mean-
   * squared percentage error (RMSPE). * "minimize-wape-mae" - Minimize the
   * combination of weighted absolute percentage error (WAPE) and mean-absolute-
   * error (MAE). * "minimize-quantile-loss" - Minimize the quantile loss at the
   * quantiles defined in `quantiles`. * "minimize-mape" - Minimize the mean
   * absolute percentage error.
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
  public function setQuantiles($quantiles)
  {
    $this->quantiles = $quantiles;
  }
  public function getQuantiles()
  {
    return $this->quantiles;
  }
  /**
   * The name of the column that the Model is to predict values for. This column
   * must be unavailable at forecast.
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
   * The name of the column that identifies time order in the time series. This
   * column must be available at forecast.
   *
   * @param string $timeColumn
   */
  public function setTimeColumn($timeColumn)
  {
    $this->timeColumn = $timeColumn;
  }
  /**
   * @return string
   */
  public function getTimeColumn()
  {
    return $this->timeColumn;
  }
  /**
   * Column names that should be used as attribute columns. The value of these
   * columns does not vary as a function of time. For example, store ID or item
   * color.
   *
   * @param string[] $timeSeriesAttributeColumns
   */
  public function setTimeSeriesAttributeColumns($timeSeriesAttributeColumns)
  {
    $this->timeSeriesAttributeColumns = $timeSeriesAttributeColumns;
  }
  /**
   * @return string[]
   */
  public function getTimeSeriesAttributeColumns()
  {
    return $this->timeSeriesAttributeColumns;
  }
  /**
   * The name of the column that identifies the time series.
   *
   * @param string $timeSeriesIdentifierColumn
   */
  public function setTimeSeriesIdentifierColumn($timeSeriesIdentifierColumn)
  {
    $this->timeSeriesIdentifierColumn = $timeSeriesIdentifierColumn;
  }
  /**
   * @return string
   */
  public function getTimeSeriesIdentifierColumn()
  {
    return $this->timeSeriesIdentifierColumn;
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
   * @param GoogleCloudAiplatformV1SchemaTrainingjobDefinitionTftForecastingInputsTransformation[] $transformations
   */
  public function setTransformations($transformations)
  {
    $this->transformations = $transformations;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaTrainingjobDefinitionTftForecastingInputsTransformation[]
   */
  public function getTransformations()
  {
    return $this->transformations;
  }
  /**
   * Names of columns that are unavailable when a forecast is requested. This
   * column contains information for the given entity (identified by the
   * time_series_identifier_column) that is unknown before the forecast For
   * example, actual weather on a given day.
   *
   * @param string[] $unavailableAtForecastColumns
   */
  public function setUnavailableAtForecastColumns($unavailableAtForecastColumns)
  {
    $this->unavailableAtForecastColumns = $unavailableAtForecastColumns;
  }
  /**
   * @return string[]
   */
  public function getUnavailableAtForecastColumns()
  {
    return $this->unavailableAtForecastColumns;
  }
  /**
   * Validation options for the data validation component. The available options
   * are: * "fail-pipeline" - default, will validate against the validation and
   * fail the pipeline if it fails. * "ignore-validation" - ignore the results
   * of the validation and continue
   *
   * @param string $validationOptions
   */
  public function setValidationOptions($validationOptions)
  {
    $this->validationOptions = $validationOptions;
  }
  /**
   * @return string
   */
  public function getValidationOptions()
  {
    return $this->validationOptions;
  }
  /**
   * Column name that should be used as the weight column. Higher values in this
   * column give more importance to the row during model training. The column
   * must have numeric values between 0 and 10000 inclusively; 0 means the row
   * is ignored for training. If weight column field is not set, then all rows
   * are assumed to have equal weight of 1. This column must be available at
   * forecast.
   *
   * @param string $weightColumn
   */
  public function setWeightColumn($weightColumn)
  {
    $this->weightColumn = $weightColumn;
  }
  /**
   * @return string
   */
  public function getWeightColumn()
  {
    return $this->weightColumn;
  }
  /**
   * Config containing strategy for generating sliding windows.
   *
   * @param GoogleCloudAiplatformV1SchemaTrainingjobDefinitionWindowConfig $windowConfig
   */
  public function setWindowConfig(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionWindowConfig $windowConfig)
  {
    $this->windowConfig = $windowConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaTrainingjobDefinitionWindowConfig
   */
  public function getWindowConfig()
  {
    return $this->windowConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionTftForecastingInputs::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionTftForecastingInputs');

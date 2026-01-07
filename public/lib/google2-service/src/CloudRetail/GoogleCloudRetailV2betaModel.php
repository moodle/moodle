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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2betaModel extends \Google\Collection
{
  /**
   * Unspecified default value, should never be explicitly set.
   */
  public const DATA_STATE_DATA_STATE_UNSPECIFIED = 'DATA_STATE_UNSPECIFIED';
  /**
   * The model has sufficient training data.
   */
  public const DATA_STATE_DATA_OK = 'DATA_OK';
  /**
   * The model does not have sufficient training data. Error messages can be
   * queried via Stackdriver.
   */
  public const DATA_STATE_DATA_ERROR = 'DATA_ERROR';
  /**
   * Value used when unset. In this case, server behavior defaults to
   * RECOMMENDATIONS_FILTERING_DISABLED.
   */
  public const FILTERING_OPTION_RECOMMENDATIONS_FILTERING_OPTION_UNSPECIFIED = 'RECOMMENDATIONS_FILTERING_OPTION_UNSPECIFIED';
  /**
   * Recommendation filtering is disabled.
   */
  public const FILTERING_OPTION_RECOMMENDATIONS_FILTERING_DISABLED = 'RECOMMENDATIONS_FILTERING_DISABLED';
  /**
   * Recommendation filtering is enabled.
   */
  public const FILTERING_OPTION_RECOMMENDATIONS_FILTERING_ENABLED = 'RECOMMENDATIONS_FILTERING_ENABLED';
  /**
   * Unspecified default value, should never be explicitly set.
   */
  public const PERIODIC_TUNING_STATE_PERIODIC_TUNING_STATE_UNSPECIFIED = 'PERIODIC_TUNING_STATE_UNSPECIFIED';
  /**
   * The model has periodic tuning disabled. Tuning can be reenabled by calling
   * the `EnableModelPeriodicTuning` method or by calling the `TuneModel`
   * method.
   */
  public const PERIODIC_TUNING_STATE_PERIODIC_TUNING_DISABLED = 'PERIODIC_TUNING_DISABLED';
  /**
   * The model cannot be tuned with periodic tuning OR the `TuneModel` method.
   * Hide the options in customer UI and reject any requests through the backend
   * self serve API.
   */
  public const PERIODIC_TUNING_STATE_ALL_TUNING_DISABLED = 'ALL_TUNING_DISABLED';
  /**
   * The model has periodic tuning enabled. Tuning can be disabled by calling
   * the `DisableModelPeriodicTuning` method.
   */
  public const PERIODIC_TUNING_STATE_PERIODIC_TUNING_ENABLED = 'PERIODIC_TUNING_ENABLED';
  /**
   * Unspecified serving state.
   */
  public const SERVING_STATE_SERVING_STATE_UNSPECIFIED = 'SERVING_STATE_UNSPECIFIED';
  /**
   * The model is not serving.
   */
  public const SERVING_STATE_INACTIVE = 'INACTIVE';
  /**
   * The model is serving and can be queried.
   */
  public const SERVING_STATE_ACTIVE = 'ACTIVE';
  /**
   * The model is trained on tuned hyperparameters and can be queried.
   */
  public const SERVING_STATE_TUNED = 'TUNED';
  /**
   * Unspecified training state.
   */
  public const TRAINING_STATE_TRAINING_STATE_UNSPECIFIED = 'TRAINING_STATE_UNSPECIFIED';
  /**
   * The model training is paused.
   */
  public const TRAINING_STATE_PAUSED = 'PAUSED';
  /**
   * The model is training.
   */
  public const TRAINING_STATE_TRAINING = 'TRAINING';
  protected $collection_key = 'servingConfigLists';
  /**
   * Output only. Timestamp the Recommendation Model was created at.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The state of data requirements for this model: `DATA_OK` and
   * `DATA_ERROR`. Recommendation model cannot be trained if the data is in
   * `DATA_ERROR` state. Recommendation model can have `DATA_ERROR` state even
   * if serving state is `ACTIVE`: models were trained successfully before, but
   * cannot be refreshed because model no longer has sufficient data for
   * training.
   *
   * @var string
   */
  public $dataState;
  /**
   * Required. The display name of the model. Should be human readable, used to
   * display Recommendation Models in the Retail Cloud Console Dashboard. UTF-8
   * encoded string with limit of 1024 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. If `RECOMMENDATIONS_FILTERING_ENABLED`, recommendation filtering
   * by attributes is enabled for the model.
   *
   * @var string
   */
  public $filteringOption;
  /**
   * Output only. The timestamp when the latest successful tune finished.
   *
   * @var string
   */
  public $lastTuneTime;
  protected $modelFeaturesConfigType = GoogleCloudRetailV2betaModelModelFeaturesConfig::class;
  protected $modelFeaturesConfigDataType = '';
  /**
   * Required. The fully qualified resource name of the model. Format: `projects
   * /{project_number}/locations/{location_id}/catalogs/{catalog_id}/models/{mod
   * el_id}` catalog_id has char limit of 50. recommendation_model_id has char
   * limit of 40.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The optimization objective e.g. `cvr`. Currently supported
   * values: `ctr`, `cvr`, `revenue-per-order`. If not specified, we choose
   * default based on model type. Default depends on type of recommendation:
   * `recommended-for-you` => `ctr` `others-you-may-like` => `ctr` `frequently-
   * bought-together` => `revenue_per_order` This field together with
   * optimization_objective describe model metadata to use to control model
   * training and serving. See https://cloud.google.com/retail/docs/models for
   * more details on what the model metadata control and which combination of
   * parameters are valid. For invalid combinations of parameters (e.g. type =
   * `frequently-bought-together` and optimization_objective = `ctr`), you
   * receive an error 400 if you try to create/update a recommendation with this
   * set of knobs.
   *
   * @var string
   */
  public $optimizationObjective;
  /**
   * Optional. The state of periodic tuning. The period we use is 3 months - to
   * do a one-off tune earlier use the `TuneModel` method. Default value is
   * `PERIODIC_TUNING_ENABLED`.
   *
   * @var string
   */
  public $periodicTuningState;
  protected $servingConfigListsType = GoogleCloudRetailV2betaModelServingConfigList::class;
  protected $servingConfigListsDataType = 'array';
  /**
   * Output only. The serving state of the model: `ACTIVE`, `NOT_ACTIVE`.
   *
   * @var string
   */
  public $servingState;
  /**
   * Optional. The training state that the model is in (e.g. `TRAINING` or
   * `PAUSED`). Since part of the cost of running the service is frequency of
   * training - this can be used to determine when to train model in order to
   * control cost. If not specified: the default value for `CreateModel` method
   * is `TRAINING`. The default value for `UpdateModel` method is to keep the
   * state the same as before.
   *
   * @var string
   */
  public $trainingState;
  /**
   * Output only. The tune operation associated with the model. Can be used to
   * determine if there is an ongoing tune for this recommendation. Empty field
   * implies no tune is goig on.
   *
   * @var string
   */
  public $tuningOperation;
  /**
   * Required. The type of model e.g. `home-page`. Currently supported values:
   * `recommended-for-you`, `others-you-may-like`, `frequently-bought-together`,
   * `page-optimization`, `similar-items`, `buy-it-again`, `on-sale-items`, and
   * `recently-viewed`(readonly value). This field together with
   * optimization_objective describe model metadata to use to control model
   * training and serving. See https://cloud.google.com/retail/docs/models for
   * more details on what the model metadata control and which combination of
   * parameters are valid. For invalid combinations of parameters (e.g. type =
   * `frequently-bought-together` and optimization_objective = `ctr`), you
   * receive an error 400 if you try to create/update a recommendation with this
   * set of knobs.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Timestamp the Recommendation Model was last updated. E.g. if a
   * Recommendation Model was paused - this would be the time the pause was
   * initiated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp the Recommendation Model was created at.
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
  /**
   * Output only. The state of data requirements for this model: `DATA_OK` and
   * `DATA_ERROR`. Recommendation model cannot be trained if the data is in
   * `DATA_ERROR` state. Recommendation model can have `DATA_ERROR` state even
   * if serving state is `ACTIVE`: models were trained successfully before, but
   * cannot be refreshed because model no longer has sufficient data for
   * training.
   *
   * Accepted values: DATA_STATE_UNSPECIFIED, DATA_OK, DATA_ERROR
   *
   * @param self::DATA_STATE_* $dataState
   */
  public function setDataState($dataState)
  {
    $this->dataState = $dataState;
  }
  /**
   * @return self::DATA_STATE_*
   */
  public function getDataState()
  {
    return $this->dataState;
  }
  /**
   * Required. The display name of the model. Should be human readable, used to
   * display Recommendation Models in the Retail Cloud Console Dashboard. UTF-8
   * encoded string with limit of 1024 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. If `RECOMMENDATIONS_FILTERING_ENABLED`, recommendation filtering
   * by attributes is enabled for the model.
   *
   * Accepted values: RECOMMENDATIONS_FILTERING_OPTION_UNSPECIFIED,
   * RECOMMENDATIONS_FILTERING_DISABLED, RECOMMENDATIONS_FILTERING_ENABLED
   *
   * @param self::FILTERING_OPTION_* $filteringOption
   */
  public function setFilteringOption($filteringOption)
  {
    $this->filteringOption = $filteringOption;
  }
  /**
   * @return self::FILTERING_OPTION_*
   */
  public function getFilteringOption()
  {
    return $this->filteringOption;
  }
  /**
   * Output only. The timestamp when the latest successful tune finished.
   *
   * @param string $lastTuneTime
   */
  public function setLastTuneTime($lastTuneTime)
  {
    $this->lastTuneTime = $lastTuneTime;
  }
  /**
   * @return string
   */
  public function getLastTuneTime()
  {
    return $this->lastTuneTime;
  }
  /**
   * Optional. Additional model features config.
   *
   * @param GoogleCloudRetailV2betaModelModelFeaturesConfig $modelFeaturesConfig
   */
  public function setModelFeaturesConfig(GoogleCloudRetailV2betaModelModelFeaturesConfig $modelFeaturesConfig)
  {
    $this->modelFeaturesConfig = $modelFeaturesConfig;
  }
  /**
   * @return GoogleCloudRetailV2betaModelModelFeaturesConfig
   */
  public function getModelFeaturesConfig()
  {
    return $this->modelFeaturesConfig;
  }
  /**
   * Required. The fully qualified resource name of the model. Format: `projects
   * /{project_number}/locations/{location_id}/catalogs/{catalog_id}/models/{mod
   * el_id}` catalog_id has char limit of 50. recommendation_model_id has char
   * limit of 40.
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
   * Optional. The optimization objective e.g. `cvr`. Currently supported
   * values: `ctr`, `cvr`, `revenue-per-order`. If not specified, we choose
   * default based on model type. Default depends on type of recommendation:
   * `recommended-for-you` => `ctr` `others-you-may-like` => `ctr` `frequently-
   * bought-together` => `revenue_per_order` This field together with
   * optimization_objective describe model metadata to use to control model
   * training and serving. See https://cloud.google.com/retail/docs/models for
   * more details on what the model metadata control and which combination of
   * parameters are valid. For invalid combinations of parameters (e.g. type =
   * `frequently-bought-together` and optimization_objective = `ctr`), you
   * receive an error 400 if you try to create/update a recommendation with this
   * set of knobs.
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
   * Optional. The state of periodic tuning. The period we use is 3 months - to
   * do a one-off tune earlier use the `TuneModel` method. Default value is
   * `PERIODIC_TUNING_ENABLED`.
   *
   * Accepted values: PERIODIC_TUNING_STATE_UNSPECIFIED,
   * PERIODIC_TUNING_DISABLED, ALL_TUNING_DISABLED, PERIODIC_TUNING_ENABLED
   *
   * @param self::PERIODIC_TUNING_STATE_* $periodicTuningState
   */
  public function setPeriodicTuningState($periodicTuningState)
  {
    $this->periodicTuningState = $periodicTuningState;
  }
  /**
   * @return self::PERIODIC_TUNING_STATE_*
   */
  public function getPeriodicTuningState()
  {
    return $this->periodicTuningState;
  }
  /**
   * Output only. The list of valid serving configs associated with the
   * PageOptimizationConfig.
   *
   * @param GoogleCloudRetailV2betaModelServingConfigList[] $servingConfigLists
   */
  public function setServingConfigLists($servingConfigLists)
  {
    $this->servingConfigLists = $servingConfigLists;
  }
  /**
   * @return GoogleCloudRetailV2betaModelServingConfigList[]
   */
  public function getServingConfigLists()
  {
    return $this->servingConfigLists;
  }
  /**
   * Output only. The serving state of the model: `ACTIVE`, `NOT_ACTIVE`.
   *
   * Accepted values: SERVING_STATE_UNSPECIFIED, INACTIVE, ACTIVE, TUNED
   *
   * @param self::SERVING_STATE_* $servingState
   */
  public function setServingState($servingState)
  {
    $this->servingState = $servingState;
  }
  /**
   * @return self::SERVING_STATE_*
   */
  public function getServingState()
  {
    return $this->servingState;
  }
  /**
   * Optional. The training state that the model is in (e.g. `TRAINING` or
   * `PAUSED`). Since part of the cost of running the service is frequency of
   * training - this can be used to determine when to train model in order to
   * control cost. If not specified: the default value for `CreateModel` method
   * is `TRAINING`. The default value for `UpdateModel` method is to keep the
   * state the same as before.
   *
   * Accepted values: TRAINING_STATE_UNSPECIFIED, PAUSED, TRAINING
   *
   * @param self::TRAINING_STATE_* $trainingState
   */
  public function setTrainingState($trainingState)
  {
    $this->trainingState = $trainingState;
  }
  /**
   * @return self::TRAINING_STATE_*
   */
  public function getTrainingState()
  {
    return $this->trainingState;
  }
  /**
   * Output only. The tune operation associated with the model. Can be used to
   * determine if there is an ongoing tune for this recommendation. Empty field
   * implies no tune is goig on.
   *
   * @param string $tuningOperation
   */
  public function setTuningOperation($tuningOperation)
  {
    $this->tuningOperation = $tuningOperation;
  }
  /**
   * @return string
   */
  public function getTuningOperation()
  {
    return $this->tuningOperation;
  }
  /**
   * Required. The type of model e.g. `home-page`. Currently supported values:
   * `recommended-for-you`, `others-you-may-like`, `frequently-bought-together`,
   * `page-optimization`, `similar-items`, `buy-it-again`, `on-sale-items`, and
   * `recently-viewed`(readonly value). This field together with
   * optimization_objective describe model metadata to use to control model
   * training and serving. See https://cloud.google.com/retail/docs/models for
   * more details on what the model metadata control and which combination of
   * parameters are valid. For invalid combinations of parameters (e.g. type =
   * `frequently-bought-together` and optimization_objective = `ctr`), you
   * receive an error 400 if you try to create/update a recommendation with this
   * set of knobs.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. Timestamp the Recommendation Model was last updated. E.g. if a
   * Recommendation Model was paused - this would be the time the pause was
   * initiated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2betaModel::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2betaModel');

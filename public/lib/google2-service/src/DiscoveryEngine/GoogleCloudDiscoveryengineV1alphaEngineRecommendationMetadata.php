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

class GoogleCloudDiscoveryengineV1alphaEngineRecommendationMetadata extends \Google\Model
{
  /**
   * Unspecified default value, should never be explicitly set.
   */
  public const DATA_STATE_DATA_STATE_UNSPECIFIED = 'DATA_STATE_UNSPECIFIED';
  /**
   * The engine has sufficient training data.
   */
  public const DATA_STATE_DATA_OK = 'DATA_OK';
  /**
   * The engine does not have sufficient training data. Error messages can be
   * queried via Stackdriver.
   */
  public const DATA_STATE_DATA_ERROR = 'DATA_ERROR';
  /**
   * Unspecified serving state.
   */
  public const SERVING_STATE_SERVING_STATE_UNSPECIFIED = 'SERVING_STATE_UNSPECIFIED';
  /**
   * The engine is not serving.
   */
  public const SERVING_STATE_INACTIVE = 'INACTIVE';
  /**
   * The engine is serving and can be queried.
   */
  public const SERVING_STATE_ACTIVE = 'ACTIVE';
  /**
   * The engine is trained on tuned hyperparameters and can be queried.
   */
  public const SERVING_STATE_TUNED = 'TUNED';
  /**
   * Output only. The state of data requirements for this engine: `DATA_OK` and
   * `DATA_ERROR`. Engine cannot be trained if the data is in `DATA_ERROR`
   * state. Engine can have `DATA_ERROR` state even if serving state is
   * `ACTIVE`: engines were trained successfully before, but cannot be refreshed
   * because the underlying engine no longer has sufficient data for training.
   *
   * @var string
   */
  public $dataState;
  /**
   * Output only. The timestamp when the latest successful training finished.
   * Only applicable on Media Recommendation engines.
   *
   * @var string
   */
  public $lastTrainTime;
  /**
   * Output only. The timestamp when the latest successful tune finished. Only
   * applicable on Media Recommendation engines.
   *
   * @var string
   */
  public $lastTuneTime;
  /**
   * Output only. The serving state of the engine: `ACTIVE`, `NOT_ACTIVE`.
   *
   * @var string
   */
  public $servingState;
  /**
   * Output only. The latest tune operation id associated with the engine. Only
   * applicable on Media Recommendation engines. If present, this operation id
   * can be used to determine if there is an ongoing tune for this engine. To
   * check the operation status, send the GetOperation request with this
   * operation id in the engine resource format. If no tuning has happened for
   * this engine, the string is empty.
   *
   * @var string
   */
  public $tuningOperation;

  /**
   * Output only. The state of data requirements for this engine: `DATA_OK` and
   * `DATA_ERROR`. Engine cannot be trained if the data is in `DATA_ERROR`
   * state. Engine can have `DATA_ERROR` state even if serving state is
   * `ACTIVE`: engines were trained successfully before, but cannot be refreshed
   * because the underlying engine no longer has sufficient data for training.
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
   * Output only. The timestamp when the latest successful training finished.
   * Only applicable on Media Recommendation engines.
   *
   * @param string $lastTrainTime
   */
  public function setLastTrainTime($lastTrainTime)
  {
    $this->lastTrainTime = $lastTrainTime;
  }
  /**
   * @return string
   */
  public function getLastTrainTime()
  {
    return $this->lastTrainTime;
  }
  /**
   * Output only. The timestamp when the latest successful tune finished. Only
   * applicable on Media Recommendation engines.
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
   * Output only. The serving state of the engine: `ACTIVE`, `NOT_ACTIVE`.
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
   * Output only. The latest tune operation id associated with the engine. Only
   * applicable on Media Recommendation engines. If present, this operation id
   * can be used to determine if there is an ongoing tune for this engine. To
   * check the operation status, send the GetOperation request with this
   * operation id in the engine resource format. If no tuning has happened for
   * this engine, the string is empty.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaEngineRecommendationMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaEngineRecommendationMetadata');

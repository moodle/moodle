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

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutomlImageTrainingTunableParameter extends \Google\Model
{
  /**
   * Default value.
   */
  public const TRAINER_TYPE_TRAINER_TYPE_UNSPECIFIED = 'TRAINER_TYPE_UNSPECIFIED';
  public const TRAINER_TYPE_AUTOML_TRAINER = 'AUTOML_TRAINER';
  public const TRAINER_TYPE_MODEL_GARDEN_TRAINER = 'MODEL_GARDEN_TRAINER';
  /**
   * Optional. An unique name of pretrained model checkpoint provided in model
   * garden, it will be mapped to a GCS location internally.
   *
   * @var string
   */
  public $checkpointName;
  /**
   * Customizable dataset settings, used in the `model_garden_trainer`.
   *
   * @var string[]
   */
  public $datasetConfig;
  protected $studySpecType = GoogleCloudAiplatformV1StudySpec::class;
  protected $studySpecDataType = '';
  /**
   * Customizable trainer settings, used in the `model_garden_trainer`.
   *
   * @var string[]
   */
  public $trainerConfig;
  /**
   * @var string
   */
  public $trainerType;

  /**
   * Optional. An unique name of pretrained model checkpoint provided in model
   * garden, it will be mapped to a GCS location internally.
   *
   * @param string $checkpointName
   */
  public function setCheckpointName($checkpointName)
  {
    $this->checkpointName = $checkpointName;
  }
  /**
   * @return string
   */
  public function getCheckpointName()
  {
    return $this->checkpointName;
  }
  /**
   * Customizable dataset settings, used in the `model_garden_trainer`.
   *
   * @param string[] $datasetConfig
   */
  public function setDatasetConfig($datasetConfig)
  {
    $this->datasetConfig = $datasetConfig;
  }
  /**
   * @return string[]
   */
  public function getDatasetConfig()
  {
    return $this->datasetConfig;
  }
  /**
   * Optioinal. StudySpec of hyperparameter tuning job. Required for
   * `model_garden_trainer`.
   *
   * @param GoogleCloudAiplatformV1StudySpec $studySpec
   */
  public function setStudySpec(GoogleCloudAiplatformV1StudySpec $studySpec)
  {
    $this->studySpec = $studySpec;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpec
   */
  public function getStudySpec()
  {
    return $this->studySpec;
  }
  /**
   * Customizable trainer settings, used in the `model_garden_trainer`.
   *
   * @param string[] $trainerConfig
   */
  public function setTrainerConfig($trainerConfig)
  {
    $this->trainerConfig = $trainerConfig;
  }
  /**
   * @return string[]
   */
  public function getTrainerConfig()
  {
    return $this->trainerConfig;
  }
  /**
   * @param self::TRAINER_TYPE_* $trainerType
   */
  public function setTrainerType($trainerType)
  {
    $this->trainerType = $trainerType;
  }
  /**
   * @return self::TRAINER_TYPE_*
   */
  public function getTrainerType()
  {
    return $this->trainerType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutomlImageTrainingTunableParameter::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutomlImageTrainingTunableParameter');

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

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlImageObjectDetectionInputs extends \Google\Model
{
  /**
   * Should not be set.
   */
  public const MODEL_TYPE_MODEL_TYPE_UNSPECIFIED = 'MODEL_TYPE_UNSPECIFIED';
  /**
   * A model best tailored to be used within Google Cloud, and which cannot be
   * exported. Expected to have a higher latency, but should also have a higher
   * prediction quality than other cloud models.
   */
  public const MODEL_TYPE_CLOUD_HIGH_ACCURACY_1 = 'CLOUD_HIGH_ACCURACY_1';
  /**
   * A model best tailored to be used within Google Cloud, and which cannot be
   * exported. Expected to have a low latency, but may have lower prediction
   * quality than other cloud models.
   */
  public const MODEL_TYPE_CLOUD_LOW_LATENCY_1 = 'CLOUD_LOW_LATENCY_1';
  /**
   * A model best tailored to be used within Google Cloud, and which cannot be
   * exported. Compared to the CLOUD_HIGH_ACCURACY_1 and CLOUD_LOW_LATENCY_1
   * models above, it is expected to have higher prediction quality and lower
   * latency.
   */
  public const MODEL_TYPE_CLOUD_1 = 'CLOUD_1';
  /**
   * A model that, in addition to being available within Google Cloud can also
   * be exported (see ModelService.ExportModel) and used on a mobile or edge
   * device with TensorFlow afterwards. Expected to have low latency, but may
   * have lower prediction quality than other mobile models.
   */
  public const MODEL_TYPE_MOBILE_TF_LOW_LATENCY_1 = 'MOBILE_TF_LOW_LATENCY_1';
  /**
   * A model that, in addition to being available within Google Cloud can also
   * be exported (see ModelService.ExportModel) and used on a mobile or edge
   * device with TensorFlow afterwards.
   */
  public const MODEL_TYPE_MOBILE_TF_VERSATILE_1 = 'MOBILE_TF_VERSATILE_1';
  /**
   * A model that, in addition to being available within Google Cloud, can also
   * be exported (see ModelService.ExportModel) and used on a mobile or edge
   * device with TensorFlow afterwards. Expected to have a higher latency, but
   * should also have a higher prediction quality than other mobile models.
   */
  public const MODEL_TYPE_MOBILE_TF_HIGH_ACCURACY_1 = 'MOBILE_TF_HIGH_ACCURACY_1';
  /**
   * A model best tailored to be used within Google Cloud, and which cannot be
   * exported. Expected to best support predictions in streaming with lower
   * latency and lower prediction quality than other cloud models.
   */
  public const MODEL_TYPE_CLOUD_STREAMING_1 = 'CLOUD_STREAMING_1';
  /**
   * SpineNet for Model Garden training with customizable hyperparameters. Best
   * tailored to be used within Google Cloud, and cannot be exported externally.
   */
  public const MODEL_TYPE_SPINENET = 'SPINENET';
  /**
   * YOLO for Model Garden training with customizable hyperparameters. Best
   * tailored to be used within Google Cloud, and cannot be exported externally.
   */
  public const MODEL_TYPE_YOLO = 'YOLO';
  /**
   * The training budget of creating this model, expressed in milli node hours
   * i.e. 1,000 value in this field means 1 node hour. The actual
   * metadata.costMilliNodeHours will be equal or less than this value. If
   * further model training ceases to provide any improvements, it will stop
   * without using the full budget and the metadata.successfulStopReason will be
   * `model-converged`. Note, node_hour = actual_hour *
   * number_of_nodes_involved. For modelType `cloud`(default), the budget must
   * be between 20,000 and 900,000 milli node hours, inclusive. The default
   * value is 216,000 which represents one day in wall time, considering 9 nodes
   * are used. For model types `mobile-tf-low-latency-1`, `mobile-tf-
   * versatile-1`, `mobile-tf-high-accuracy-1` the training budget must be
   * between 1,000 and 100,000 milli node hours, inclusive. The default value is
   * 24,000 which represents one day in wall time on a single node that is used.
   *
   * @var string
   */
  public $budgetMilliNodeHours;
  /**
   * Use the entire training budget. This disables the early stopping feature.
   * When false the early stopping feature is enabled, which means that AutoML
   * Image Object Detection might stop training before the entire training
   * budget has been used.
   *
   * @var bool
   */
  public $disableEarlyStopping;
  /**
   * @var string
   */
  public $modelType;
  protected $tunableParameterType = GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutomlImageTrainingTunableParameter::class;
  protected $tunableParameterDataType = '';
  /**
   * The ID of `base` model for upTraining. If it is specified, the new model
   * will be upTrained based on the `base` model for upTraining. Otherwise, the
   * new model will be trained from scratch. The `base` model for upTraining
   * must be in the same Project and Location as the new Model to train, and
   * have the same modelType.
   *
   * @var string
   */
  public $uptrainBaseModelId;

  /**
   * The training budget of creating this model, expressed in milli node hours
   * i.e. 1,000 value in this field means 1 node hour. The actual
   * metadata.costMilliNodeHours will be equal or less than this value. If
   * further model training ceases to provide any improvements, it will stop
   * without using the full budget and the metadata.successfulStopReason will be
   * `model-converged`. Note, node_hour = actual_hour *
   * number_of_nodes_involved. For modelType `cloud`(default), the budget must
   * be between 20,000 and 900,000 milli node hours, inclusive. The default
   * value is 216,000 which represents one day in wall time, considering 9 nodes
   * are used. For model types `mobile-tf-low-latency-1`, `mobile-tf-
   * versatile-1`, `mobile-tf-high-accuracy-1` the training budget must be
   * between 1,000 and 100,000 milli node hours, inclusive. The default value is
   * 24,000 which represents one day in wall time on a single node that is used.
   *
   * @param string $budgetMilliNodeHours
   */
  public function setBudgetMilliNodeHours($budgetMilliNodeHours)
  {
    $this->budgetMilliNodeHours = $budgetMilliNodeHours;
  }
  /**
   * @return string
   */
  public function getBudgetMilliNodeHours()
  {
    return $this->budgetMilliNodeHours;
  }
  /**
   * Use the entire training budget. This disables the early stopping feature.
   * When false the early stopping feature is enabled, which means that AutoML
   * Image Object Detection might stop training before the entire training
   * budget has been used.
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
   * Trainer type for Vision TrainRequest.
   *
   * @param GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutomlImageTrainingTunableParameter $tunableParameter
   */
  public function setTunableParameter(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutomlImageTrainingTunableParameter $tunableParameter)
  {
    $this->tunableParameter = $tunableParameter;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutomlImageTrainingTunableParameter
   */
  public function getTunableParameter()
  {
    return $this->tunableParameter;
  }
  /**
   * The ID of `base` model for upTraining. If it is specified, the new model
   * will be upTrained based on the `base` model for upTraining. Otherwise, the
   * new model will be trained from scratch. The `base` model for upTraining
   * must be in the same Project and Location as the new Model to train, and
   * have the same modelType.
   *
   * @param string $uptrainBaseModelId
   */
  public function setUptrainBaseModelId($uptrainBaseModelId)
  {
    $this->uptrainBaseModelId = $uptrainBaseModelId;
  }
  /**
   * @return string
   */
  public function getUptrainBaseModelId()
  {
    return $this->uptrainBaseModelId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlImageObjectDetectionInputs::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlImageObjectDetectionInputs');

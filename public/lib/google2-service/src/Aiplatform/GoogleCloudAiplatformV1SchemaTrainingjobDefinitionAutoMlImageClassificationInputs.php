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

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlImageClassificationInputs extends \Google\Model
{
  /**
   * Should not be set.
   */
  public const MODEL_TYPE_MODEL_TYPE_UNSPECIFIED = 'MODEL_TYPE_UNSPECIFIED';
  /**
   * A Model best tailored to be used within Google Cloud, and which cannot be
   * exported. Default.
   */
  public const MODEL_TYPE_CLOUD = 'CLOUD';
  /**
   * A model type best tailored to be used within Google Cloud, which cannot be
   * exported externally. Compared to the CLOUD model above, it is expected to
   * have higher prediction accuracy.
   */
  public const MODEL_TYPE_CLOUD_1 = 'CLOUD_1';
  /**
   * A model that, in addition to being available within Google Cloud, can also
   * be exported (see ModelService.ExportModel) as TensorFlow or Core ML model
   * and used on a mobile or edge device afterwards. Expected to have low
   * latency, but may have lower prediction quality than other mobile models.
   */
  public const MODEL_TYPE_MOBILE_TF_LOW_LATENCY_1 = 'MOBILE_TF_LOW_LATENCY_1';
  /**
   * A model that, in addition to being available within Google Cloud, can also
   * be exported (see ModelService.ExportModel) as TensorFlow or Core ML model
   * and used on a mobile or edge device with afterwards.
   */
  public const MODEL_TYPE_MOBILE_TF_VERSATILE_1 = 'MOBILE_TF_VERSATILE_1';
  /**
   * A model that, in addition to being available within Google Cloud, can also
   * be exported (see ModelService.ExportModel) as TensorFlow or Core ML model
   * and used on a mobile or edge device afterwards. Expected to have a higher
   * latency, but should also have a higher prediction quality than other mobile
   * models.
   */
  public const MODEL_TYPE_MOBILE_TF_HIGH_ACCURACY_1 = 'MOBILE_TF_HIGH_ACCURACY_1';
  /**
   * EfficientNet model for Model Garden training with customizable
   * hyperparameters. Best tailored to be used within Google Cloud, and cannot
   * be exported externally.
   */
  public const MODEL_TYPE_EFFICIENTNET = 'EFFICIENTNET';
  /**
   * MaxViT model for Model Garden training with customizable hyperparameters.
   * Best tailored to be used within Google Cloud, and cannot be exported
   * externally.
   */
  public const MODEL_TYPE_MAXVIT = 'MAXVIT';
  /**
   * ViT model for Model Garden training with customizable hyperparameters. Best
   * tailored to be used within Google Cloud, and cannot be exported externally.
   */
  public const MODEL_TYPE_VIT = 'VIT';
  /**
   * CoCa model for Model Garden training with customizable hyperparameters.
   * Best tailored to be used within Google Cloud, and cannot be exported
   * externally.
   */
  public const MODEL_TYPE_COCA = 'COCA';
  /**
   * The ID of the `base` model. If it is specified, the new model will be
   * trained based on the `base` model. Otherwise, the new model will be trained
   * from scratch. The `base` model must be in the same Project and Location as
   * the new Model to train, and have the same modelType.
   *
   * @var string
   */
  public $baseModelId;
  /**
   * The training budget of creating this model, expressed in milli node hours
   * i.e. 1,000 value in this field means 1 node hour. The actual
   * metadata.costMilliNodeHours will be equal or less than this value. If
   * further model training ceases to provide any improvements, it will stop
   * without using the full budget and the metadata.successfulStopReason will be
   * `model-converged`. Note, node_hour = actual_hour *
   * number_of_nodes_involved. For modelType `cloud`(default), the budget must
   * be between 8,000 and 800,000 milli node hours, inclusive. The default value
   * is 192,000 which represents one day in wall time, considering 8 nodes are
   * used. For model types `mobile-tf-low-latency-1`, `mobile-tf-versatile-1`,
   * `mobile-tf-high-accuracy-1`, the training budget must be between 1,000 and
   * 100,000 milli node hours, inclusive. The default value is 24,000 which
   * represents one day in wall time on a single node that is used.
   *
   * @var string
   */
  public $budgetMilliNodeHours;
  /**
   * Use the entire training budget. This disables the early stopping feature.
   * When false the early stopping feature is enabled, which means that AutoML
   * Image Classification might stop training before the entire training budget
   * has been used.
   *
   * @var bool
   */
  public $disableEarlyStopping;
  /**
   * @var string
   */
  public $modelType;
  /**
   * If false, a single-label (multi-class) Model will be trained (i.e. assuming
   * that for each image just up to one annotation may be applicable). If true,
   * a multi-label Model will be trained (i.e. assuming that for each image
   * multiple annotations may be applicable).
   *
   * @var bool
   */
  public $multiLabel;
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
   * The ID of the `base` model. If it is specified, the new model will be
   * trained based on the `base` model. Otherwise, the new model will be trained
   * from scratch. The `base` model must be in the same Project and Location as
   * the new Model to train, and have the same modelType.
   *
   * @param string $baseModelId
   */
  public function setBaseModelId($baseModelId)
  {
    $this->baseModelId = $baseModelId;
  }
  /**
   * @return string
   */
  public function getBaseModelId()
  {
    return $this->baseModelId;
  }
  /**
   * The training budget of creating this model, expressed in milli node hours
   * i.e. 1,000 value in this field means 1 node hour. The actual
   * metadata.costMilliNodeHours will be equal or less than this value. If
   * further model training ceases to provide any improvements, it will stop
   * without using the full budget and the metadata.successfulStopReason will be
   * `model-converged`. Note, node_hour = actual_hour *
   * number_of_nodes_involved. For modelType `cloud`(default), the budget must
   * be between 8,000 and 800,000 milli node hours, inclusive. The default value
   * is 192,000 which represents one day in wall time, considering 8 nodes are
   * used. For model types `mobile-tf-low-latency-1`, `mobile-tf-versatile-1`,
   * `mobile-tf-high-accuracy-1`, the training budget must be between 1,000 and
   * 100,000 milli node hours, inclusive. The default value is 24,000 which
   * represents one day in wall time on a single node that is used.
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
   * Image Classification might stop training before the entire training budget
   * has been used.
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
   * If false, a single-label (multi-class) Model will be trained (i.e. assuming
   * that for each image just up to one annotation may be applicable). If true,
   * a multi-label Model will be trained (i.e. assuming that for each image
   * multiple annotations may be applicable).
   *
   * @param bool $multiLabel
   */
  public function setMultiLabel($multiLabel)
  {
    $this->multiLabel = $multiLabel;
  }
  /**
   * @return bool
   */
  public function getMultiLabel()
  {
    return $this->multiLabel;
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
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlImageClassificationInputs::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlImageClassificationInputs');

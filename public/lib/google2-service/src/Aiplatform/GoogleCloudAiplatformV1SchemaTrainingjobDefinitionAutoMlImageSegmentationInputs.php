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

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlImageSegmentationInputs extends \Google\Model
{
  /**
   * Should not be set.
   */
  public const MODEL_TYPE_MODEL_TYPE_UNSPECIFIED = 'MODEL_TYPE_UNSPECIFIED';
  /**
   * A model to be used via prediction calls to uCAIP API. Expected to have a
   * higher latency, but should also have a higher prediction quality than other
   * models.
   */
  public const MODEL_TYPE_CLOUD_HIGH_ACCURACY_1 = 'CLOUD_HIGH_ACCURACY_1';
  /**
   * A model to be used via prediction calls to uCAIP API. Expected to have a
   * lower latency but relatively lower prediction quality.
   */
  public const MODEL_TYPE_CLOUD_LOW_ACCURACY_1 = 'CLOUD_LOW_ACCURACY_1';
  /**
   * A model that, in addition to being available within Google Cloud, can also
   * be exported (see ModelService.ExportModel) as TensorFlow model and used on
   * a mobile or edge device afterwards. Expected to have low latency, but may
   * have lower prediction quality than other mobile models.
   */
  public const MODEL_TYPE_MOBILE_TF_LOW_LATENCY_1 = 'MOBILE_TF_LOW_LATENCY_1';
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
   * number_of_nodes_involved. Or actual_wall_clock_hours =
   * train_budget_milli_node_hours / (number_of_nodes_involved * 1000) For
   * modelType `cloud-high-accuracy-1`(default), the budget must be between
   * 20,000 and 2,000,000 milli node hours, inclusive. The default value is
   * 192,000 which represents one day in wall time (1000 milli * 24 hours * 8
   * nodes).
   *
   * @var string
   */
  public $budgetMilliNodeHours;
  /**
   * @var string
   */
  public $modelType;

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
   * number_of_nodes_involved. Or actual_wall_clock_hours =
   * train_budget_milli_node_hours / (number_of_nodes_involved * 1000) For
   * modelType `cloud-high-accuracy-1`(default), the budget must be between
   * 20,000 and 2,000,000 milli node hours, inclusive. The default value is
   * 192,000 which represents one day in wall time (1000 milli * 24 hours * 8
   * nodes).
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlImageSegmentationInputs::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlImageSegmentationInputs');

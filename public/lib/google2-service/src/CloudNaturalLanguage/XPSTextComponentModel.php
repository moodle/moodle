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

namespace Google\Service\CloudNaturalLanguage;

class XPSTextComponentModel extends \Google\Model
{
  public const PARTITION_PARTITION_TYPE_UNSPECIFIED = 'PARTITION_TYPE_UNSPECIFIED';
  /**
   * The default partition.
   */
  public const PARTITION_PARTITION_ZERO = 'PARTITION_ZERO';
  /**
   * It has significantly lower replication than partition-0 and is located in
   * the US only. It also has a larger model size limit and higher default RAM
   * quota than partition-0. Customers with batch traffic, US-based traffic, or
   * very large models should use this partition. Capacity in this partition is
   * significantly cheaper than partition-0.
   */
  public const PARTITION_PARTITION_REDUCED_HOMING = 'PARTITION_REDUCED_HOMING';
  /**
   * To be used by customers with Jellyfish-accelerated ops.
   */
  public const PARTITION_PARTITION_JELLYFISH = 'PARTITION_JELLYFISH';
  /**
   * The partition used by regionalized servomatic cloud regions.
   */
  public const PARTITION_PARTITION_CPU = 'PARTITION_CPU';
  /**
   * The partition used for loading models from custom storage.
   */
  public const PARTITION_PARTITION_CUSTOM_STORAGE_CPU = 'PARTITION_CUSTOM_STORAGE_CPU';
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_UNSPECIFIED = 'TEXT_MODEL_TYPE_UNSPECIFIED';
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_DEFAULT = 'TEXT_MODEL_TYPE_DEFAULT';
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_META_ARCHITECT = 'TEXT_MODEL_TYPE_META_ARCHITECT';
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_ATC = 'TEXT_MODEL_TYPE_ATC';
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_CLARA2 = 'TEXT_MODEL_TYPE_CLARA2';
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_CHATBASE = 'TEXT_MODEL_TYPE_CHATBASE';
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_SAFT_SPAN_LABELING = 'TEXT_MODEL_TYPE_SAFT_SPAN_LABELING';
  /**
   * Model type for entity extraction.
   */
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_TEXT_EXTRACTION = 'TEXT_MODEL_TYPE_TEXT_EXTRACTION';
  /**
   * Model type for relationship extraction.
   */
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_RELATIONSHIP_EXTRACTION = 'TEXT_MODEL_TYPE_RELATIONSHIP_EXTRACTION';
  /**
   * A composite model represents a set of component models that have to be used
   * together for prediction. A composite model appears to be a single model to
   * the model user. It may contain only one component model.
   */
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_COMPOSITE = 'TEXT_MODEL_TYPE_COMPOSITE';
  /**
   * Model type used to train default, MA, and ATC models in a single batch
   * worker pipeline.
   */
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_ALL_MODELS = 'TEXT_MODEL_TYPE_ALL_MODELS';
  /**
   * BERT pipeline needs a specific model type, since it uses a different TFX
   * configuration compared with DEFAULT (despite sharing most of the code).
   */
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_BERT = 'TEXT_MODEL_TYPE_BERT';
  /**
   * Model type for EncPaLM.
   */
  public const SUBMODEL_TYPE_TEXT_MODEL_TYPE_ENC_PALM = 'TEXT_MODEL_TYPE_ENC_PALM';
  /**
   * The Cloud Storage resource path to hold batch prediction model.
   *
   * @var string
   */
  public $batchPredictionModelGcsUri;
  /**
   * The Cloud Storage resource path to hold online prediction model.
   *
   * @var string
   */
  public $onlinePredictionModelGcsUri;
  /**
   * The partition where the model is deployed. Populated by uCAIP BE as part of
   * online PredictRequest.
   *
   * @var string
   */
  public $partition;
  protected $servingArtifactType = XPSModelArtifactItem::class;
  protected $servingArtifactDataType = '';
  /**
   * The name of servo model. Populated by uCAIP BE as part of online
   * PredictRequest.
   *
   * @var string
   */
  public $servoModelName;
  /**
   * The name of the trained NL submodel.
   *
   * @var string
   */
  public $submodelName;
  /**
   * The type of trained NL submodel
   *
   * @var string
   */
  public $submodelType;
  /**
   * ## The fields below are only populated under uCAIP request scope.
   * https://cloud.google.com/ml-engine/docs/runtime-version-list
   *
   * @var string
   */
  public $tfRuntimeVersion;
  /**
   * The servomatic model version number. Populated by uCAIP BE as part of
   * online PredictRequest.
   *
   * @var string
   */
  public $versionNumber;

  /**
   * The Cloud Storage resource path to hold batch prediction model.
   *
   * @param string $batchPredictionModelGcsUri
   */
  public function setBatchPredictionModelGcsUri($batchPredictionModelGcsUri)
  {
    $this->batchPredictionModelGcsUri = $batchPredictionModelGcsUri;
  }
  /**
   * @return string
   */
  public function getBatchPredictionModelGcsUri()
  {
    return $this->batchPredictionModelGcsUri;
  }
  /**
   * The Cloud Storage resource path to hold online prediction model.
   *
   * @param string $onlinePredictionModelGcsUri
   */
  public function setOnlinePredictionModelGcsUri($onlinePredictionModelGcsUri)
  {
    $this->onlinePredictionModelGcsUri = $onlinePredictionModelGcsUri;
  }
  /**
   * @return string
   */
  public function getOnlinePredictionModelGcsUri()
  {
    return $this->onlinePredictionModelGcsUri;
  }
  /**
   * The partition where the model is deployed. Populated by uCAIP BE as part of
   * online PredictRequest.
   *
   * Accepted values: PARTITION_TYPE_UNSPECIFIED, PARTITION_ZERO,
   * PARTITION_REDUCED_HOMING, PARTITION_JELLYFISH, PARTITION_CPU,
   * PARTITION_CUSTOM_STORAGE_CPU
   *
   * @param self::PARTITION_* $partition
   */
  public function setPartition($partition)
  {
    $this->partition = $partition;
  }
  /**
   * @return self::PARTITION_*
   */
  public function getPartition()
  {
    return $this->partition;
  }
  /**
   * The default model binary file used for serving (e.g. online predict, batch
   * predict) via public Cloud Ai Platform API.
   *
   * @param XPSModelArtifactItem $servingArtifact
   */
  public function setServingArtifact(XPSModelArtifactItem $servingArtifact)
  {
    $this->servingArtifact = $servingArtifact;
  }
  /**
   * @return XPSModelArtifactItem
   */
  public function getServingArtifact()
  {
    return $this->servingArtifact;
  }
  /**
   * The name of servo model. Populated by uCAIP BE as part of online
   * PredictRequest.
   *
   * @param string $servoModelName
   */
  public function setServoModelName($servoModelName)
  {
    $this->servoModelName = $servoModelName;
  }
  /**
   * @return string
   */
  public function getServoModelName()
  {
    return $this->servoModelName;
  }
  /**
   * The name of the trained NL submodel.
   *
   * @param string $submodelName
   */
  public function setSubmodelName($submodelName)
  {
    $this->submodelName = $submodelName;
  }
  /**
   * @return string
   */
  public function getSubmodelName()
  {
    return $this->submodelName;
  }
  /**
   * The type of trained NL submodel
   *
   * Accepted values: TEXT_MODEL_TYPE_UNSPECIFIED, TEXT_MODEL_TYPE_DEFAULT,
   * TEXT_MODEL_TYPE_META_ARCHITECT, TEXT_MODEL_TYPE_ATC,
   * TEXT_MODEL_TYPE_CLARA2, TEXT_MODEL_TYPE_CHATBASE,
   * TEXT_MODEL_TYPE_SAFT_SPAN_LABELING, TEXT_MODEL_TYPE_TEXT_EXTRACTION,
   * TEXT_MODEL_TYPE_RELATIONSHIP_EXTRACTION, TEXT_MODEL_TYPE_COMPOSITE,
   * TEXT_MODEL_TYPE_ALL_MODELS, TEXT_MODEL_TYPE_BERT, TEXT_MODEL_TYPE_ENC_PALM
   *
   * @param self::SUBMODEL_TYPE_* $submodelType
   */
  public function setSubmodelType($submodelType)
  {
    $this->submodelType = $submodelType;
  }
  /**
   * @return self::SUBMODEL_TYPE_*
   */
  public function getSubmodelType()
  {
    return $this->submodelType;
  }
  /**
   * ## The fields below are only populated under uCAIP request scope.
   * https://cloud.google.com/ml-engine/docs/runtime-version-list
   *
   * @param string $tfRuntimeVersion
   */
  public function setTfRuntimeVersion($tfRuntimeVersion)
  {
    $this->tfRuntimeVersion = $tfRuntimeVersion;
  }
  /**
   * @return string
   */
  public function getTfRuntimeVersion()
  {
    return $this->tfRuntimeVersion;
  }
  /**
   * The servomatic model version number. Populated by uCAIP BE as part of
   * online PredictRequest.
   *
   * @param string $versionNumber
   */
  public function setVersionNumber($versionNumber)
  {
    $this->versionNumber = $versionNumber;
  }
  /**
   * @return string
   */
  public function getVersionNumber()
  {
    return $this->versionNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSTextComponentModel::class, 'Google_Service_CloudNaturalLanguage_XPSTextComponentModel');

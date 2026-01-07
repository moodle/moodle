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

class GoogleCloudAiplatformV1TrainingPipeline extends \Google\Model
{
  /**
   * The pipeline state is unspecified.
   */
  public const STATE_PIPELINE_STATE_UNSPECIFIED = 'PIPELINE_STATE_UNSPECIFIED';
  /**
   * The pipeline has been created or resumed, and processing has not yet begun.
   */
  public const STATE_PIPELINE_STATE_QUEUED = 'PIPELINE_STATE_QUEUED';
  /**
   * The service is preparing to run the pipeline.
   */
  public const STATE_PIPELINE_STATE_PENDING = 'PIPELINE_STATE_PENDING';
  /**
   * The pipeline is in progress.
   */
  public const STATE_PIPELINE_STATE_RUNNING = 'PIPELINE_STATE_RUNNING';
  /**
   * The pipeline completed successfully.
   */
  public const STATE_PIPELINE_STATE_SUCCEEDED = 'PIPELINE_STATE_SUCCEEDED';
  /**
   * The pipeline failed.
   */
  public const STATE_PIPELINE_STATE_FAILED = 'PIPELINE_STATE_FAILED';
  /**
   * The pipeline is being cancelled. From this state, the pipeline may only go
   * to either PIPELINE_STATE_SUCCEEDED, PIPELINE_STATE_FAILED or
   * PIPELINE_STATE_CANCELLED.
   */
  public const STATE_PIPELINE_STATE_CANCELLING = 'PIPELINE_STATE_CANCELLING';
  /**
   * The pipeline has been cancelled.
   */
  public const STATE_PIPELINE_STATE_CANCELLED = 'PIPELINE_STATE_CANCELLED';
  /**
   * The pipeline has been stopped, and can be resumed.
   */
  public const STATE_PIPELINE_STATE_PAUSED = 'PIPELINE_STATE_PAUSED';
  /**
   * Output only. Time when the TrainingPipeline was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The user-defined name of this TrainingPipeline.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Output only. Time when the TrainingPipeline entered any of the following
   * states: `PIPELINE_STATE_SUCCEEDED`, `PIPELINE_STATE_FAILED`,
   * `PIPELINE_STATE_CANCELLED`.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $inputDataConfigType = GoogleCloudAiplatformV1InputDataConfig::class;
  protected $inputDataConfigDataType = '';
  /**
   * The labels with user-defined metadata to organize TrainingPipelines. Label
   * keys and values can be no longer than 64 characters (Unicode codepoints),
   * can only contain lowercase letters, numeric characters, underscores and
   * dashes. International characters are allowed. See https://goo.gl/xmQnxf for
   * more information and examples of labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. The ID to use for the uploaded Model, which will become the final
   * component of the model resource name. This value may be up to 63
   * characters, and valid characters are `[a-z0-9_-]`. The first character
   * cannot be a number or hyphen.
   *
   * @var string
   */
  public $modelId;
  protected $modelToUploadType = GoogleCloudAiplatformV1Model::class;
  protected $modelToUploadDataType = '';
  /**
   * Output only. Resource name of the TrainingPipeline.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. When specify this field, the `model_to_upload` will not be
   * uploaded as a new model, instead, it will become a new version of this
   * `parent_model`.
   *
   * @var string
   */
  public $parentModel;
  /**
   * Output only. Time when the TrainingPipeline for the first time entered the
   * `PIPELINE_STATE_RUNNING` state.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The detailed state of the pipeline.
   *
   * @var string
   */
  public $state;
  /**
   * Required. A Google Cloud Storage path to the YAML file that defines the
   * training task which is responsible for producing the model artifact, and
   * may also include additional auxiliary work. The definition files that can
   * be used here are found in gs://google-cloud-
   * aiplatform/schema/trainingjob/definition/. Note: The URI given on output
   * will be immutable and probably different, including the URI scheme, than
   * the one given on input. The output URI will point to a location where the
   * user only has a read access.
   *
   * @var string
   */
  public $trainingTaskDefinition;
  /**
   * Required. The training task's parameter(s), as specified in the
   * training_task_definition's `inputs`.
   *
   * @var array
   */
  public $trainingTaskInputs;
  /**
   * Output only. The metadata information as specified in the
   * training_task_definition's `metadata`. This metadata is an auxiliary
   * runtime and final information about the training task. While the pipeline
   * is running this information is populated only at a best effort basis. Only
   * present if the pipeline's training_task_definition contains `metadata`
   * object.
   *
   * @var array
   */
  public $trainingTaskMetadata;
  /**
   * Output only. Time when the TrainingPipeline was most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Time when the TrainingPipeline was created.
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
   * Required. The user-defined name of this TrainingPipeline.
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
   * Customer-managed encryption key spec for a TrainingPipeline. If set, this
   * TrainingPipeline will be secured by this key. Note: Model trained by this
   * TrainingPipeline is also secured by this key if model_to_upload is not set
   * separately.
   *
   * @param GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
  /**
   * Output only. Time when the TrainingPipeline entered any of the following
   * states: `PIPELINE_STATE_SUCCEEDED`, `PIPELINE_STATE_FAILED`,
   * `PIPELINE_STATE_CANCELLED`.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. Only populated when the pipeline's state is
   * `PIPELINE_STATE_FAILED` or `PIPELINE_STATE_CANCELLED`.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Specifies Vertex AI owned input data that may be used for training the
   * Model. The TrainingPipeline's training_task_definition should make clear
   * whether this config is used and if there are any special requirements on
   * how it should be filled. If nothing about this config is mentioned in the
   * training_task_definition, then it should be assumed that the
   * TrainingPipeline does not depend on this configuration.
   *
   * @param GoogleCloudAiplatformV1InputDataConfig $inputDataConfig
   */
  public function setInputDataConfig(GoogleCloudAiplatformV1InputDataConfig $inputDataConfig)
  {
    $this->inputDataConfig = $inputDataConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1InputDataConfig
   */
  public function getInputDataConfig()
  {
    return $this->inputDataConfig;
  }
  /**
   * The labels with user-defined metadata to organize TrainingPipelines. Label
   * keys and values can be no longer than 64 characters (Unicode codepoints),
   * can only contain lowercase letters, numeric characters, underscores and
   * dashes. International characters are allowed. See https://goo.gl/xmQnxf for
   * more information and examples of labels.
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
   * Optional. The ID to use for the uploaded Model, which will become the final
   * component of the model resource name. This value may be up to 63
   * characters, and valid characters are `[a-z0-9_-]`. The first character
   * cannot be a number or hyphen.
   *
   * @param string $modelId
   */
  public function setModelId($modelId)
  {
    $this->modelId = $modelId;
  }
  /**
   * @return string
   */
  public function getModelId()
  {
    return $this->modelId;
  }
  /**
   * Describes the Model that may be uploaded (via ModelService.UploadModel) by
   * this TrainingPipeline. The TrainingPipeline's training_task_definition
   * should make clear whether this Model description should be populated, and
   * if there are any special requirements regarding how it should be filled. If
   * nothing is mentioned in the training_task_definition, then it should be
   * assumed that this field should not be filled and the training task either
   * uploads the Model without a need of this information, or that training task
   * does not support uploading a Model as part of the pipeline. When the
   * Pipeline's state becomes `PIPELINE_STATE_SUCCEEDED` and the trained Model
   * had been uploaded into Vertex AI, then the model_to_upload's resource name
   * is populated. The Model is always uploaded into the Project and Location in
   * which this pipeline is.
   *
   * @param GoogleCloudAiplatformV1Model $modelToUpload
   */
  public function setModelToUpload(GoogleCloudAiplatformV1Model $modelToUpload)
  {
    $this->modelToUpload = $modelToUpload;
  }
  /**
   * @return GoogleCloudAiplatformV1Model
   */
  public function getModelToUpload()
  {
    return $this->modelToUpload;
  }
  /**
   * Output only. Resource name of the TrainingPipeline.
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
   * Optional. When specify this field, the `model_to_upload` will not be
   * uploaded as a new model, instead, it will become a new version of this
   * `parent_model`.
   *
   * @param string $parentModel
   */
  public function setParentModel($parentModel)
  {
    $this->parentModel = $parentModel;
  }
  /**
   * @return string
   */
  public function getParentModel()
  {
    return $this->parentModel;
  }
  /**
   * Output only. Time when the TrainingPipeline for the first time entered the
   * `PIPELINE_STATE_RUNNING` state.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The detailed state of the pipeline.
   *
   * Accepted values: PIPELINE_STATE_UNSPECIFIED, PIPELINE_STATE_QUEUED,
   * PIPELINE_STATE_PENDING, PIPELINE_STATE_RUNNING, PIPELINE_STATE_SUCCEEDED,
   * PIPELINE_STATE_FAILED, PIPELINE_STATE_CANCELLING, PIPELINE_STATE_CANCELLED,
   * PIPELINE_STATE_PAUSED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Required. A Google Cloud Storage path to the YAML file that defines the
   * training task which is responsible for producing the model artifact, and
   * may also include additional auxiliary work. The definition files that can
   * be used here are found in gs://google-cloud-
   * aiplatform/schema/trainingjob/definition/. Note: The URI given on output
   * will be immutable and probably different, including the URI scheme, than
   * the one given on input. The output URI will point to a location where the
   * user only has a read access.
   *
   * @param string $trainingTaskDefinition
   */
  public function setTrainingTaskDefinition($trainingTaskDefinition)
  {
    $this->trainingTaskDefinition = $trainingTaskDefinition;
  }
  /**
   * @return string
   */
  public function getTrainingTaskDefinition()
  {
    return $this->trainingTaskDefinition;
  }
  /**
   * Required. The training task's parameter(s), as specified in the
   * training_task_definition's `inputs`.
   *
   * @param array $trainingTaskInputs
   */
  public function setTrainingTaskInputs($trainingTaskInputs)
  {
    $this->trainingTaskInputs = $trainingTaskInputs;
  }
  /**
   * @return array
   */
  public function getTrainingTaskInputs()
  {
    return $this->trainingTaskInputs;
  }
  /**
   * Output only. The metadata information as specified in the
   * training_task_definition's `metadata`. This metadata is an auxiliary
   * runtime and final information about the training task. While the pipeline
   * is running this information is populated only at a best effort basis. Only
   * present if the pipeline's training_task_definition contains `metadata`
   * object.
   *
   * @param array $trainingTaskMetadata
   */
  public function setTrainingTaskMetadata($trainingTaskMetadata)
  {
    $this->trainingTaskMetadata = $trainingTaskMetadata;
  }
  /**
   * @return array
   */
  public function getTrainingTaskMetadata()
  {
    return $this->trainingTaskMetadata;
  }
  /**
   * Output only. Time when the TrainingPipeline was most recently updated.
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
class_alias(GoogleCloudAiplatformV1TrainingPipeline::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TrainingPipeline');

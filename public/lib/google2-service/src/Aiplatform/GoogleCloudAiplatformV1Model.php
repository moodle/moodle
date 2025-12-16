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

class GoogleCloudAiplatformV1Model extends \Google\Collection
{
  protected $collection_key = 'versionAliases';
  /**
   * Immutable. The path to the directory containing the Model artifact and any
   * of its supporting files. Not required for AutoML Models.
   *
   * @var string
   */
  public $artifactUri;
  protected $baseModelSourceType = GoogleCloudAiplatformV1ModelBaseModelSource::class;
  protected $baseModelSourceDataType = '';
  protected $checkpointsType = GoogleCloudAiplatformV1Checkpoint::class;
  protected $checkpointsDataType = 'array';
  protected $containerSpecType = GoogleCloudAiplatformV1ModelContainerSpec::class;
  protected $containerSpecDataType = '';
  /**
   * Output only. Timestamp when this Model was uploaded into Vertex AI.
   *
   * @var string
   */
  public $createTime;
  protected $dataStatsType = GoogleCloudAiplatformV1ModelDataStats::class;
  protected $dataStatsDataType = '';
  /**
   * The default checkpoint id of a model version.
   *
   * @var string
   */
  public $defaultCheckpointId;
  protected $deployedModelsType = GoogleCloudAiplatformV1DeployedModelRef::class;
  protected $deployedModelsDataType = 'array';
  /**
   * The description of the Model.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the Model. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Used to perform consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  protected $explanationSpecType = GoogleCloudAiplatformV1ExplanationSpec::class;
  protected $explanationSpecDataType = '';
  /**
   * The labels with user-defined metadata to organize your Models. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. See https://goo.gl/xmQnxf for more
   * information and examples of labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. An additional information about the Model; the schema of the
   * metadata can be found in metadata_schema. Unset if the Model does not have
   * any additional information.
   *
   * @var array
   */
  public $metadata;
  /**
   * Output only. The resource name of the Artifact that was created in
   * MetadataStore when creating the Model. The Artifact resource name pattern
   * is `projects/{project}/locations/{location}/metadataStores/{metadata_store}
   * /artifacts/{artifact}`.
   *
   * @var string
   */
  public $metadataArtifact;
  /**
   * Immutable. Points to a YAML file stored on Google Cloud Storage describing
   * additional information about the Model, that is specific to it. Unset if
   * the Model does not have any additional information. The schema is defined
   * as an OpenAPI 3.0.2 [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). AutoML Models
   * always have this field populated by Vertex AI, if no additional metadata is
   * needed, this field is set to an empty string. Note: The URI given on output
   * will be immutable and probably different, including the URI scheme, than
   * the one given on input. The output URI will point to a location where the
   * user only has a read access.
   *
   * @var string
   */
  public $metadataSchemaUri;
  protected $modelSourceInfoType = GoogleCloudAiplatformV1ModelSourceInfo::class;
  protected $modelSourceInfoDataType = '';
  /**
   * The resource name of the Model.
   *
   * @var string
   */
  public $name;
  protected $originalModelInfoType = GoogleCloudAiplatformV1ModelOriginalModelInfo::class;
  protected $originalModelInfoDataType = '';
  /**
   * Optional. This field is populated if the model is produced by a pipeline
   * job.
   *
   * @var string
   */
  public $pipelineJob;
  protected $predictSchemataType = GoogleCloudAiplatformV1PredictSchemata::class;
  protected $predictSchemataDataType = '';
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. When this Model is deployed, its prediction resources are
   * described by the `prediction_resources` field of the
   * Endpoint.deployed_models object. Because not all Models support all
   * resource configuration types, the configuration types this Model supports
   * are listed here. If no configuration types are listed, the Model cannot be
   * deployed to an Endpoint and does not support online predictions
   * (PredictionService.Predict or PredictionService.Explain). Such a Model can
   * serve predictions by using a BatchPredictionJob, if it has at least one
   * entry each in supported_input_storage_formats and
   * supported_output_storage_formats.
   *
   * @var string[]
   */
  public $supportedDeploymentResourcesTypes;
  protected $supportedExportFormatsType = GoogleCloudAiplatformV1ModelExportFormat::class;
  protected $supportedExportFormatsDataType = 'array';
  /**
   * Output only. The formats this Model supports in
   * BatchPredictionJob.input_config. If PredictSchemata.instance_schema_uri
   * exists, the instances should be given as per that schema. The possible
   * formats are: * `jsonl` The JSON Lines format, where each instance is a
   * single line. Uses GcsSource. * `csv` The CSV format, where each instance is
   * a single comma-separated line. The first line in the file is the header,
   * containing comma-separated field names. Uses GcsSource. * `tf-record` The
   * TFRecord format, where each instance is a single record in tfrecord syntax.
   * Uses GcsSource. * `tf-record-gzip` Similar to `tf-record`, but the file is
   * gzipped. Uses GcsSource. * `bigquery` Each instance is a single row in
   * BigQuery. Uses BigQuerySource. * `file-list` Each line of the file is the
   * location of an instance to process, uses `gcs_source` field of the
   * InputConfig object. If this Model doesn't support any of these formats it
   * means it cannot be used with a BatchPredictionJob. However, if it has
   * supported_deployment_resources_types, it could serve online predictions by
   * using PredictionService.Predict or PredictionService.Explain.
   *
   * @var string[]
   */
  public $supportedInputStorageFormats;
  /**
   * Output only. The formats this Model supports in
   * BatchPredictionJob.output_config. If both
   * PredictSchemata.instance_schema_uri and
   * PredictSchemata.prediction_schema_uri exist, the predictions are returned
   * together with their instances. In other words, the prediction has the
   * original instance data first, followed by the actual prediction content (as
   * per the schema). The possible formats are: * `jsonl` The JSON Lines format,
   * where each prediction is a single line. Uses GcsDestination. * `csv` The
   * CSV format, where each prediction is a single comma-separated line. The
   * first line in the file is the header, containing comma-separated field
   * names. Uses GcsDestination. * `bigquery` Each prediction is a single row in
   * a BigQuery table, uses BigQueryDestination . If this Model doesn't support
   * any of these formats it means it cannot be used with a BatchPredictionJob.
   * However, if it has supported_deployment_resources_types, it could serve
   * online predictions by using PredictionService.Predict or
   * PredictionService.Explain.
   *
   * @var string[]
   */
  public $supportedOutputStorageFormats;
  /**
   * Output only. The resource name of the TrainingPipeline that uploaded this
   * Model, if any.
   *
   * @var string
   */
  public $trainingPipeline;
  /**
   * Output only. Timestamp when this Model was most recently updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * User provided version aliases so that a model version can be referenced via
   * alias (i.e.
   * `projects/{project}/locations/{location}/models/{model_id}@{version_alias}`
   * instead of auto-generated version id (i.e.
   * `projects/{project}/locations/{location}/models/{model_id}@{version_id})`.
   * The format is a-z{0,126}[a-z0-9] to distinguish from version_id. A default
   * version alias will be created for the first version of the model, and there
   * must be exactly one default version alias for a model.
   *
   * @var string[]
   */
  public $versionAliases;
  /**
   * Output only. Timestamp when this version was created.
   *
   * @var string
   */
  public $versionCreateTime;
  /**
   * The description of this version.
   *
   * @var string
   */
  public $versionDescription;
  /**
   * Output only. Immutable. The version ID of the model. A new version is
   * committed when a new model version is uploaded or trained under an existing
   * model id. It is an auto-incrementing decimal number in string
   * representation.
   *
   * @var string
   */
  public $versionId;
  /**
   * Output only. Timestamp when this version was most recently updated.
   *
   * @var string
   */
  public $versionUpdateTime;

  /**
   * Immutable. The path to the directory containing the Model artifact and any
   * of its supporting files. Not required for AutoML Models.
   *
   * @param string $artifactUri
   */
  public function setArtifactUri($artifactUri)
  {
    $this->artifactUri = $artifactUri;
  }
  /**
   * @return string
   */
  public function getArtifactUri()
  {
    return $this->artifactUri;
  }
  /**
   * Optional. User input field to specify the base model source. Currently it
   * only supports specifing the Model Garden models and Genie models.
   *
   * @param GoogleCloudAiplatformV1ModelBaseModelSource $baseModelSource
   */
  public function setBaseModelSource(GoogleCloudAiplatformV1ModelBaseModelSource $baseModelSource)
  {
    $this->baseModelSource = $baseModelSource;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelBaseModelSource
   */
  public function getBaseModelSource()
  {
    return $this->baseModelSource;
  }
  /**
   * Optional. Output only. The checkpoints of the model.
   *
   * @param GoogleCloudAiplatformV1Checkpoint[] $checkpoints
   */
  public function setCheckpoints($checkpoints)
  {
    $this->checkpoints = $checkpoints;
  }
  /**
   * @return GoogleCloudAiplatformV1Checkpoint[]
   */
  public function getCheckpoints()
  {
    return $this->checkpoints;
  }
  /**
   * Input only. The specification of the container that is to be used when
   * deploying this Model. The specification is ingested upon
   * ModelService.UploadModel, and all binaries it contains are copied and
   * stored internally by Vertex AI. Not required for AutoML Models.
   *
   * @param GoogleCloudAiplatformV1ModelContainerSpec $containerSpec
   */
  public function setContainerSpec(GoogleCloudAiplatformV1ModelContainerSpec $containerSpec)
  {
    $this->containerSpec = $containerSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelContainerSpec
   */
  public function getContainerSpec()
  {
    return $this->containerSpec;
  }
  /**
   * Output only. Timestamp when this Model was uploaded into Vertex AI.
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
   * Stats of data used for training or evaluating the Model. Only populated
   * when the Model is trained by a TrainingPipeline with data_input_config.
   *
   * @param GoogleCloudAiplatformV1ModelDataStats $dataStats
   */
  public function setDataStats(GoogleCloudAiplatformV1ModelDataStats $dataStats)
  {
    $this->dataStats = $dataStats;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelDataStats
   */
  public function getDataStats()
  {
    return $this->dataStats;
  }
  /**
   * The default checkpoint id of a model version.
   *
   * @param string $defaultCheckpointId
   */
  public function setDefaultCheckpointId($defaultCheckpointId)
  {
    $this->defaultCheckpointId = $defaultCheckpointId;
  }
  /**
   * @return string
   */
  public function getDefaultCheckpointId()
  {
    return $this->defaultCheckpointId;
  }
  /**
   * Output only. The pointers to DeployedModels created from this Model. Note
   * that Model could have been deployed to Endpoints in different Locations.
   *
   * @param GoogleCloudAiplatformV1DeployedModelRef[] $deployedModels
   */
  public function setDeployedModels($deployedModels)
  {
    $this->deployedModels = $deployedModels;
  }
  /**
   * @return GoogleCloudAiplatformV1DeployedModelRef[]
   */
  public function getDeployedModels()
  {
    return $this->deployedModels;
  }
  /**
   * The description of the Model.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The display name of the Model. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
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
   * Customer-managed encryption key spec for a Model. If set, this Model and
   * all sub-resources of this Model will be secured by this key.
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
   * Used to perform consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The default explanation specification for this Model. The Model can be used
   * for requesting explanation after being deployed if it is populated. The
   * Model can be used for batch explanation if it is populated. All fields of
   * the explanation_spec can be overridden by explanation_spec of
   * DeployModelRequest.deployed_model, or explanation_spec of
   * BatchPredictionJob. If the default explanation specification is not set for
   * this Model, this Model can still be used for requesting explanation by
   * setting explanation_spec of DeployModelRequest.deployed_model and for batch
   * explanation by setting explanation_spec of BatchPredictionJob.
   *
   * @param GoogleCloudAiplatformV1ExplanationSpec $explanationSpec
   */
  public function setExplanationSpec(GoogleCloudAiplatformV1ExplanationSpec $explanationSpec)
  {
    $this->explanationSpec = $explanationSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ExplanationSpec
   */
  public function getExplanationSpec()
  {
    return $this->explanationSpec;
  }
  /**
   * The labels with user-defined metadata to organize your Models. Label keys
   * and values can be no longer than 64 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. See https://goo.gl/xmQnxf for more
   * information and examples of labels.
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
   * Immutable. An additional information about the Model; the schema of the
   * metadata can be found in metadata_schema. Unset if the Model does not have
   * any additional information.
   *
   * @param array $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. The resource name of the Artifact that was created in
   * MetadataStore when creating the Model. The Artifact resource name pattern
   * is `projects/{project}/locations/{location}/metadataStores/{metadata_store}
   * /artifacts/{artifact}`.
   *
   * @param string $metadataArtifact
   */
  public function setMetadataArtifact($metadataArtifact)
  {
    $this->metadataArtifact = $metadataArtifact;
  }
  /**
   * @return string
   */
  public function getMetadataArtifact()
  {
    return $this->metadataArtifact;
  }
  /**
   * Immutable. Points to a YAML file stored on Google Cloud Storage describing
   * additional information about the Model, that is specific to it. Unset if
   * the Model does not have any additional information. The schema is defined
   * as an OpenAPI 3.0.2 [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). AutoML Models
   * always have this field populated by Vertex AI, if no additional metadata is
   * needed, this field is set to an empty string. Note: The URI given on output
   * will be immutable and probably different, including the URI scheme, than
   * the one given on input. The output URI will point to a location where the
   * user only has a read access.
   *
   * @param string $metadataSchemaUri
   */
  public function setMetadataSchemaUri($metadataSchemaUri)
  {
    $this->metadataSchemaUri = $metadataSchemaUri;
  }
  /**
   * @return string
   */
  public function getMetadataSchemaUri()
  {
    return $this->metadataSchemaUri;
  }
  /**
   * Output only. Source of a model. It can either be automl training pipeline,
   * custom training pipeline, BigQuery ML, or saved and tuned from Genie or
   * Model Garden.
   *
   * @param GoogleCloudAiplatformV1ModelSourceInfo $modelSourceInfo
   */
  public function setModelSourceInfo(GoogleCloudAiplatformV1ModelSourceInfo $modelSourceInfo)
  {
    $this->modelSourceInfo = $modelSourceInfo;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelSourceInfo
   */
  public function getModelSourceInfo()
  {
    return $this->modelSourceInfo;
  }
  /**
   * The resource name of the Model.
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
   * Output only. If this Model is a copy of another Model, this contains info
   * about the original.
   *
   * @param GoogleCloudAiplatformV1ModelOriginalModelInfo $originalModelInfo
   */
  public function setOriginalModelInfo(GoogleCloudAiplatformV1ModelOriginalModelInfo $originalModelInfo)
  {
    $this->originalModelInfo = $originalModelInfo;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelOriginalModelInfo
   */
  public function getOriginalModelInfo()
  {
    return $this->originalModelInfo;
  }
  /**
   * Optional. This field is populated if the model is produced by a pipeline
   * job.
   *
   * @param string $pipelineJob
   */
  public function setPipelineJob($pipelineJob)
  {
    $this->pipelineJob = $pipelineJob;
  }
  /**
   * @return string
   */
  public function getPipelineJob()
  {
    return $this->pipelineJob;
  }
  /**
   * The schemata that describe formats of the Model's predictions and
   * explanations as given and returned via PredictionService.Predict and
   * PredictionService.Explain.
   *
   * @param GoogleCloudAiplatformV1PredictSchemata $predictSchemata
   */
  public function setPredictSchemata(GoogleCloudAiplatformV1PredictSchemata $predictSchemata)
  {
    $this->predictSchemata = $predictSchemata;
  }
  /**
   * @return GoogleCloudAiplatformV1PredictSchemata
   */
  public function getPredictSchemata()
  {
    return $this->predictSchemata;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. When this Model is deployed, its prediction resources are
   * described by the `prediction_resources` field of the
   * Endpoint.deployed_models object. Because not all Models support all
   * resource configuration types, the configuration types this Model supports
   * are listed here. If no configuration types are listed, the Model cannot be
   * deployed to an Endpoint and does not support online predictions
   * (PredictionService.Predict or PredictionService.Explain). Such a Model can
   * serve predictions by using a BatchPredictionJob, if it has at least one
   * entry each in supported_input_storage_formats and
   * supported_output_storage_formats.
   *
   * @param string[] $supportedDeploymentResourcesTypes
   */
  public function setSupportedDeploymentResourcesTypes($supportedDeploymentResourcesTypes)
  {
    $this->supportedDeploymentResourcesTypes = $supportedDeploymentResourcesTypes;
  }
  /**
   * @return string[]
   */
  public function getSupportedDeploymentResourcesTypes()
  {
    return $this->supportedDeploymentResourcesTypes;
  }
  /**
   * Output only. The formats in which this Model may be exported. If empty,
   * this Model is not available for export.
   *
   * @param GoogleCloudAiplatformV1ModelExportFormat[] $supportedExportFormats
   */
  public function setSupportedExportFormats($supportedExportFormats)
  {
    $this->supportedExportFormats = $supportedExportFormats;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelExportFormat[]
   */
  public function getSupportedExportFormats()
  {
    return $this->supportedExportFormats;
  }
  /**
   * Output only. The formats this Model supports in
   * BatchPredictionJob.input_config. If PredictSchemata.instance_schema_uri
   * exists, the instances should be given as per that schema. The possible
   * formats are: * `jsonl` The JSON Lines format, where each instance is a
   * single line. Uses GcsSource. * `csv` The CSV format, where each instance is
   * a single comma-separated line. The first line in the file is the header,
   * containing comma-separated field names. Uses GcsSource. * `tf-record` The
   * TFRecord format, where each instance is a single record in tfrecord syntax.
   * Uses GcsSource. * `tf-record-gzip` Similar to `tf-record`, but the file is
   * gzipped. Uses GcsSource. * `bigquery` Each instance is a single row in
   * BigQuery. Uses BigQuerySource. * `file-list` Each line of the file is the
   * location of an instance to process, uses `gcs_source` field of the
   * InputConfig object. If this Model doesn't support any of these formats it
   * means it cannot be used with a BatchPredictionJob. However, if it has
   * supported_deployment_resources_types, it could serve online predictions by
   * using PredictionService.Predict or PredictionService.Explain.
   *
   * @param string[] $supportedInputStorageFormats
   */
  public function setSupportedInputStorageFormats($supportedInputStorageFormats)
  {
    $this->supportedInputStorageFormats = $supportedInputStorageFormats;
  }
  /**
   * @return string[]
   */
  public function getSupportedInputStorageFormats()
  {
    return $this->supportedInputStorageFormats;
  }
  /**
   * Output only. The formats this Model supports in
   * BatchPredictionJob.output_config. If both
   * PredictSchemata.instance_schema_uri and
   * PredictSchemata.prediction_schema_uri exist, the predictions are returned
   * together with their instances. In other words, the prediction has the
   * original instance data first, followed by the actual prediction content (as
   * per the schema). The possible formats are: * `jsonl` The JSON Lines format,
   * where each prediction is a single line. Uses GcsDestination. * `csv` The
   * CSV format, where each prediction is a single comma-separated line. The
   * first line in the file is the header, containing comma-separated field
   * names. Uses GcsDestination. * `bigquery` Each prediction is a single row in
   * a BigQuery table, uses BigQueryDestination . If this Model doesn't support
   * any of these formats it means it cannot be used with a BatchPredictionJob.
   * However, if it has supported_deployment_resources_types, it could serve
   * online predictions by using PredictionService.Predict or
   * PredictionService.Explain.
   *
   * @param string[] $supportedOutputStorageFormats
   */
  public function setSupportedOutputStorageFormats($supportedOutputStorageFormats)
  {
    $this->supportedOutputStorageFormats = $supportedOutputStorageFormats;
  }
  /**
   * @return string[]
   */
  public function getSupportedOutputStorageFormats()
  {
    return $this->supportedOutputStorageFormats;
  }
  /**
   * Output only. The resource name of the TrainingPipeline that uploaded this
   * Model, if any.
   *
   * @param string $trainingPipeline
   */
  public function setTrainingPipeline($trainingPipeline)
  {
    $this->trainingPipeline = $trainingPipeline;
  }
  /**
   * @return string
   */
  public function getTrainingPipeline()
  {
    return $this->trainingPipeline;
  }
  /**
   * Output only. Timestamp when this Model was most recently updated.
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
  /**
   * User provided version aliases so that a model version can be referenced via
   * alias (i.e.
   * `projects/{project}/locations/{location}/models/{model_id}@{version_alias}`
   * instead of auto-generated version id (i.e.
   * `projects/{project}/locations/{location}/models/{model_id}@{version_id})`.
   * The format is a-z{0,126}[a-z0-9] to distinguish from version_id. A default
   * version alias will be created for the first version of the model, and there
   * must be exactly one default version alias for a model.
   *
   * @param string[] $versionAliases
   */
  public function setVersionAliases($versionAliases)
  {
    $this->versionAliases = $versionAliases;
  }
  /**
   * @return string[]
   */
  public function getVersionAliases()
  {
    return $this->versionAliases;
  }
  /**
   * Output only. Timestamp when this version was created.
   *
   * @param string $versionCreateTime
   */
  public function setVersionCreateTime($versionCreateTime)
  {
    $this->versionCreateTime = $versionCreateTime;
  }
  /**
   * @return string
   */
  public function getVersionCreateTime()
  {
    return $this->versionCreateTime;
  }
  /**
   * The description of this version.
   *
   * @param string $versionDescription
   */
  public function setVersionDescription($versionDescription)
  {
    $this->versionDescription = $versionDescription;
  }
  /**
   * @return string
   */
  public function getVersionDescription()
  {
    return $this->versionDescription;
  }
  /**
   * Output only. Immutable. The version ID of the model. A new version is
   * committed when a new model version is uploaded or trained under an existing
   * model id. It is an auto-incrementing decimal number in string
   * representation.
   *
   * @param string $versionId
   */
  public function setVersionId($versionId)
  {
    $this->versionId = $versionId;
  }
  /**
   * @return string
   */
  public function getVersionId()
  {
    return $this->versionId;
  }
  /**
   * Output only. Timestamp when this version was most recently updated.
   *
   * @param string $versionUpdateTime
   */
  public function setVersionUpdateTime($versionUpdateTime)
  {
    $this->versionUpdateTime = $versionUpdateTime;
  }
  /**
   * @return string
   */
  public function getVersionUpdateTime()
  {
    return $this->versionUpdateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Model::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Model');

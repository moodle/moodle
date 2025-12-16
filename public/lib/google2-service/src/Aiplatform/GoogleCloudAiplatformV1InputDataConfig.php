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

class GoogleCloudAiplatformV1InputDataConfig extends \Google\Model
{
  /**
   * Applicable only to custom training with Datasets that have DataItems and
   * Annotations. Cloud Storage URI that points to a YAML file describing the
   * annotation schema. The schema is defined as an OpenAPI 3.0.2 [Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). The schema files
   * that can be used here are found in gs://google-cloud-
   * aiplatform/schema/dataset/annotation/ , note that the chosen schema must be
   * consistent with metadata of the Dataset specified by dataset_id. Only
   * Annotations that both match this schema and belong to DataItems not ignored
   * by the split method are used in respectively training, validation or test
   * role, depending on the role of the DataItem they are on. When used in
   * conjunction with annotations_filter, the Annotations used for training are
   * filtered by both annotations_filter and annotation_schema_uri.
   *
   * @var string
   */
  public $annotationSchemaUri;
  /**
   * Applicable only to Datasets that have DataItems and Annotations. A filter
   * on Annotations of the Dataset. Only Annotations that both match this filter
   * and belong to DataItems not ignored by the split method are used in
   * respectively training, validation or test role, depending on the role of
   * the DataItem they are on (for the auto-assigned that role is decided by
   * Vertex AI). A filter with same syntax as the one used in ListAnnotations
   * may be used, but note here it filters across all Annotations of the
   * Dataset, and not just within a single DataItem.
   *
   * @var string
   */
  public $annotationsFilter;
  protected $bigqueryDestinationType = GoogleCloudAiplatformV1BigQueryDestination::class;
  protected $bigqueryDestinationDataType = '';
  /**
   * Required. The ID of the Dataset in the same Project and Location which data
   * will be used to train the Model. The Dataset must use schema compatible
   * with Model being trained, and what is compatible should be described in the
   * used TrainingPipeline's training_task_definition. For tabular Datasets, all
   * their data is exported to training, to pick and choose from.
   *
   * @var string
   */
  public $datasetId;
  protected $filterSplitType = GoogleCloudAiplatformV1FilterSplit::class;
  protected $filterSplitDataType = '';
  protected $fractionSplitType = GoogleCloudAiplatformV1FractionSplit::class;
  protected $fractionSplitDataType = '';
  protected $gcsDestinationType = GoogleCloudAiplatformV1GcsDestination::class;
  protected $gcsDestinationDataType = '';
  /**
   * Whether to persist the ML use assignment to data item system labels.
   *
   * @var bool
   */
  public $persistMlUseAssignment;
  protected $predefinedSplitType = GoogleCloudAiplatformV1PredefinedSplit::class;
  protected $predefinedSplitDataType = '';
  /**
   * Only applicable to Datasets that have SavedQueries. The ID of a SavedQuery
   * (annotation set) under the Dataset specified by dataset_id used for
   * filtering Annotations for training. Only Annotations that are associated
   * with this SavedQuery are used in respectively training. When used in
   * conjunction with annotations_filter, the Annotations used for training are
   * filtered by both saved_query_id and annotations_filter. Only one of
   * saved_query_id and annotation_schema_uri should be specified as both of
   * them represent the same thing: problem type.
   *
   * @var string
   */
  public $savedQueryId;
  protected $stratifiedSplitType = GoogleCloudAiplatformV1StratifiedSplit::class;
  protected $stratifiedSplitDataType = '';
  protected $timestampSplitType = GoogleCloudAiplatformV1TimestampSplit::class;
  protected $timestampSplitDataType = '';

  /**
   * Applicable only to custom training with Datasets that have DataItems and
   * Annotations. Cloud Storage URI that points to a YAML file describing the
   * annotation schema. The schema is defined as an OpenAPI 3.0.2 [Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). The schema files
   * that can be used here are found in gs://google-cloud-
   * aiplatform/schema/dataset/annotation/ , note that the chosen schema must be
   * consistent with metadata of the Dataset specified by dataset_id. Only
   * Annotations that both match this schema and belong to DataItems not ignored
   * by the split method are used in respectively training, validation or test
   * role, depending on the role of the DataItem they are on. When used in
   * conjunction with annotations_filter, the Annotations used for training are
   * filtered by both annotations_filter and annotation_schema_uri.
   *
   * @param string $annotationSchemaUri
   */
  public function setAnnotationSchemaUri($annotationSchemaUri)
  {
    $this->annotationSchemaUri = $annotationSchemaUri;
  }
  /**
   * @return string
   */
  public function getAnnotationSchemaUri()
  {
    return $this->annotationSchemaUri;
  }
  /**
   * Applicable only to Datasets that have DataItems and Annotations. A filter
   * on Annotations of the Dataset. Only Annotations that both match this filter
   * and belong to DataItems not ignored by the split method are used in
   * respectively training, validation or test role, depending on the role of
   * the DataItem they are on (for the auto-assigned that role is decided by
   * Vertex AI). A filter with same syntax as the one used in ListAnnotations
   * may be used, but note here it filters across all Annotations of the
   * Dataset, and not just within a single DataItem.
   *
   * @param string $annotationsFilter
   */
  public function setAnnotationsFilter($annotationsFilter)
  {
    $this->annotationsFilter = $annotationsFilter;
  }
  /**
   * @return string
   */
  public function getAnnotationsFilter()
  {
    return $this->annotationsFilter;
  }
  /**
   * Only applicable to custom training with tabular Dataset with BigQuery
   * source. The BigQuery project location where the training data is to be
   * written to. In the given project a new dataset is created with name
   * `dataset___` where timestamp is in YYYY_MM_DDThh_mm_ss_sssZ format. All
   * training input data is written into that dataset. In the dataset three
   * tables are created, `training`, `validation` and `test`. * AIP_DATA_FORMAT
   * = "bigquery". * AIP_TRAINING_DATA_URI =
   * "bigquery_destination.dataset___.training" * AIP_VALIDATION_DATA_URI =
   * "bigquery_destination.dataset___.validation" * AIP_TEST_DATA_URI =
   * "bigquery_destination.dataset___.test"
   *
   * @param GoogleCloudAiplatformV1BigQueryDestination $bigqueryDestination
   */
  public function setBigqueryDestination(GoogleCloudAiplatformV1BigQueryDestination $bigqueryDestination)
  {
    $this->bigqueryDestination = $bigqueryDestination;
  }
  /**
   * @return GoogleCloudAiplatformV1BigQueryDestination
   */
  public function getBigqueryDestination()
  {
    return $this->bigqueryDestination;
  }
  /**
   * Required. The ID of the Dataset in the same Project and Location which data
   * will be used to train the Model. The Dataset must use schema compatible
   * with Model being trained, and what is compatible should be described in the
   * used TrainingPipeline's training_task_definition. For tabular Datasets, all
   * their data is exported to training, to pick and choose from.
   *
   * @param string $datasetId
   */
  public function setDatasetId($datasetId)
  {
    $this->datasetId = $datasetId;
  }
  /**
   * @return string
   */
  public function getDatasetId()
  {
    return $this->datasetId;
  }
  /**
   * Split based on the provided filters for each set.
   *
   * @param GoogleCloudAiplatformV1FilterSplit $filterSplit
   */
  public function setFilterSplit(GoogleCloudAiplatformV1FilterSplit $filterSplit)
  {
    $this->filterSplit = $filterSplit;
  }
  /**
   * @return GoogleCloudAiplatformV1FilterSplit
   */
  public function getFilterSplit()
  {
    return $this->filterSplit;
  }
  /**
   * Split based on fractions defining the size of each set.
   *
   * @param GoogleCloudAiplatformV1FractionSplit $fractionSplit
   */
  public function setFractionSplit(GoogleCloudAiplatformV1FractionSplit $fractionSplit)
  {
    $this->fractionSplit = $fractionSplit;
  }
  /**
   * @return GoogleCloudAiplatformV1FractionSplit
   */
  public function getFractionSplit()
  {
    return $this->fractionSplit;
  }
  /**
   * The Cloud Storage location where the training data is to be written to. In
   * the given directory a new directory is created with name: `dataset---`
   * where timestamp is in YYYY-MM-DDThh:mm:ss.sssZ ISO-8601 format. All
   * training input data is written into that directory. The Vertex AI
   * environment variables representing Cloud Storage data URIs are represented
   * in the Cloud Storage wildcard format to support sharded data. e.g.:
   * "gs://.../training-*.jsonl" * AIP_DATA_FORMAT = "jsonl" for non-tabular
   * data, "csv" for tabular data * AIP_TRAINING_DATA_URI =
   * "gcs_destination/dataset---/training-*.${AIP_DATA_FORMAT}" *
   * AIP_VALIDATION_DATA_URI =
   * "gcs_destination/dataset---/validation-*.${AIP_DATA_FORMAT}" *
   * AIP_TEST_DATA_URI = "gcs_destination/dataset---/test-*.${AIP_DATA_FORMAT}"
   *
   * @param GoogleCloudAiplatformV1GcsDestination $gcsDestination
   */
  public function setGcsDestination(GoogleCloudAiplatformV1GcsDestination $gcsDestination)
  {
    $this->gcsDestination = $gcsDestination;
  }
  /**
   * @return GoogleCloudAiplatformV1GcsDestination
   */
  public function getGcsDestination()
  {
    return $this->gcsDestination;
  }
  /**
   * Whether to persist the ML use assignment to data item system labels.
   *
   * @param bool $persistMlUseAssignment
   */
  public function setPersistMlUseAssignment($persistMlUseAssignment)
  {
    $this->persistMlUseAssignment = $persistMlUseAssignment;
  }
  /**
   * @return bool
   */
  public function getPersistMlUseAssignment()
  {
    return $this->persistMlUseAssignment;
  }
  /**
   * Supported only for tabular Datasets. Split based on a predefined key.
   *
   * @param GoogleCloudAiplatformV1PredefinedSplit $predefinedSplit
   */
  public function setPredefinedSplit(GoogleCloudAiplatformV1PredefinedSplit $predefinedSplit)
  {
    $this->predefinedSplit = $predefinedSplit;
  }
  /**
   * @return GoogleCloudAiplatformV1PredefinedSplit
   */
  public function getPredefinedSplit()
  {
    return $this->predefinedSplit;
  }
  /**
   * Only applicable to Datasets that have SavedQueries. The ID of a SavedQuery
   * (annotation set) under the Dataset specified by dataset_id used for
   * filtering Annotations for training. Only Annotations that are associated
   * with this SavedQuery are used in respectively training. When used in
   * conjunction with annotations_filter, the Annotations used for training are
   * filtered by both saved_query_id and annotations_filter. Only one of
   * saved_query_id and annotation_schema_uri should be specified as both of
   * them represent the same thing: problem type.
   *
   * @param string $savedQueryId
   */
  public function setSavedQueryId($savedQueryId)
  {
    $this->savedQueryId = $savedQueryId;
  }
  /**
   * @return string
   */
  public function getSavedQueryId()
  {
    return $this->savedQueryId;
  }
  /**
   * Supported only for tabular Datasets. Split based on the distribution of the
   * specified column.
   *
   * @param GoogleCloudAiplatformV1StratifiedSplit $stratifiedSplit
   */
  public function setStratifiedSplit(GoogleCloudAiplatformV1StratifiedSplit $stratifiedSplit)
  {
    $this->stratifiedSplit = $stratifiedSplit;
  }
  /**
   * @return GoogleCloudAiplatformV1StratifiedSplit
   */
  public function getStratifiedSplit()
  {
    return $this->stratifiedSplit;
  }
  /**
   * Supported only for tabular Datasets. Split based on the timestamp of the
   * input data pieces.
   *
   * @param GoogleCloudAiplatformV1TimestampSplit $timestampSplit
   */
  public function setTimestampSplit(GoogleCloudAiplatformV1TimestampSplit $timestampSplit)
  {
    $this->timestampSplit = $timestampSplit;
  }
  /**
   * @return GoogleCloudAiplatformV1TimestampSplit
   */
  public function getTimestampSplit()
  {
    return $this->timestampSplit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1InputDataConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1InputDataConfig');

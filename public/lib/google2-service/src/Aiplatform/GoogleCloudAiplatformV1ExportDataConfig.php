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

class GoogleCloudAiplatformV1ExportDataConfig extends \Google\Model
{
  /**
   * Regular user export.
   */
  public const EXPORT_USE_EXPORT_USE_UNSPECIFIED = 'EXPORT_USE_UNSPECIFIED';
  /**
   * Export for custom code training.
   */
  public const EXPORT_USE_CUSTOM_CODE_TRAINING = 'CUSTOM_CODE_TRAINING';
  /**
   * The Cloud Storage URI that points to a YAML file describing the annotation
   * schema. The schema is defined as an OpenAPI 3.0.2 [Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). The schema files
   * that can be used here are found in gs://google-cloud-
   * aiplatform/schema/dataset/annotation/, note that the chosen schema must be
   * consistent with metadata of the Dataset specified by
   * ExportDataRequest.name. Only used for custom training data export use
   * cases. Only applicable to Datasets that have DataItems and Annotations.
   * Only Annotations that both match this schema and belong to DataItems not
   * ignored by the split method are used in respectively training, validation
   * or test role, depending on the role of the DataItem they are on. When used
   * in conjunction with annotations_filter, the Annotations used for training
   * are filtered by both annotations_filter and annotation_schema_uri.
   *
   * @var string
   */
  public $annotationSchemaUri;
  /**
   * An expression for filtering what part of the Dataset is to be exported.
   * Only Annotations that match this filter will be exported. The filter syntax
   * is the same as in ListAnnotations.
   *
   * @var string
   */
  public $annotationsFilter;
  /**
   * Indicates the usage of the exported files.
   *
   * @var string
   */
  public $exportUse;
  protected $filterSplitType = GoogleCloudAiplatformV1ExportFilterSplit::class;
  protected $filterSplitDataType = '';
  protected $fractionSplitType = GoogleCloudAiplatformV1ExportFractionSplit::class;
  protected $fractionSplitDataType = '';
  protected $gcsDestinationType = GoogleCloudAiplatformV1GcsDestination::class;
  protected $gcsDestinationDataType = '';
  /**
   * The ID of a SavedQuery (annotation set) under the Dataset specified by
   * ExportDataRequest.name used for filtering Annotations for training. Only
   * used for custom training data export use cases. Only applicable to Datasets
   * that have SavedQueries. Only Annotations that are associated with this
   * SavedQuery are used in respectively training. When used in conjunction with
   * annotations_filter, the Annotations used for training are filtered by both
   * saved_query_id and annotations_filter. Only one of saved_query_id and
   * annotation_schema_uri should be specified as both of them represent the
   * same thing: problem type.
   *
   * @var string
   */
  public $savedQueryId;

  /**
   * The Cloud Storage URI that points to a YAML file describing the annotation
   * schema. The schema is defined as an OpenAPI 3.0.2 [Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). The schema files
   * that can be used here are found in gs://google-cloud-
   * aiplatform/schema/dataset/annotation/, note that the chosen schema must be
   * consistent with metadata of the Dataset specified by
   * ExportDataRequest.name. Only used for custom training data export use
   * cases. Only applicable to Datasets that have DataItems and Annotations.
   * Only Annotations that both match this schema and belong to DataItems not
   * ignored by the split method are used in respectively training, validation
   * or test role, depending on the role of the DataItem they are on. When used
   * in conjunction with annotations_filter, the Annotations used for training
   * are filtered by both annotations_filter and annotation_schema_uri.
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
   * An expression for filtering what part of the Dataset is to be exported.
   * Only Annotations that match this filter will be exported. The filter syntax
   * is the same as in ListAnnotations.
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
   * Indicates the usage of the exported files.
   *
   * Accepted values: EXPORT_USE_UNSPECIFIED, CUSTOM_CODE_TRAINING
   *
   * @param self::EXPORT_USE_* $exportUse
   */
  public function setExportUse($exportUse)
  {
    $this->exportUse = $exportUse;
  }
  /**
   * @return self::EXPORT_USE_*
   */
  public function getExportUse()
  {
    return $this->exportUse;
  }
  /**
   * Split based on the provided filters for each set.
   *
   * @param GoogleCloudAiplatformV1ExportFilterSplit $filterSplit
   */
  public function setFilterSplit(GoogleCloudAiplatformV1ExportFilterSplit $filterSplit)
  {
    $this->filterSplit = $filterSplit;
  }
  /**
   * @return GoogleCloudAiplatformV1ExportFilterSplit
   */
  public function getFilterSplit()
  {
    return $this->filterSplit;
  }
  /**
   * Split based on fractions defining the size of each set.
   *
   * @param GoogleCloudAiplatformV1ExportFractionSplit $fractionSplit
   */
  public function setFractionSplit(GoogleCloudAiplatformV1ExportFractionSplit $fractionSplit)
  {
    $this->fractionSplit = $fractionSplit;
  }
  /**
   * @return GoogleCloudAiplatformV1ExportFractionSplit
   */
  public function getFractionSplit()
  {
    return $this->fractionSplit;
  }
  /**
   * The Google Cloud Storage location where the output is to be written to. In
   * the given directory a new directory will be created with name: `export-
   * data--` where timestamp is in YYYY-MM-DDThh:mm:ss.sssZ ISO-8601 format. All
   * export output will be written into that directory. Inside that directory,
   * annotations with the same schema will be grouped into sub directories which
   * are named with the corresponding annotations' schema title. Inside these
   * sub directories, a schema.yaml will be created to describe the output
   * format.
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
   * The ID of a SavedQuery (annotation set) under the Dataset specified by
   * ExportDataRequest.name used for filtering Annotations for training. Only
   * used for custom training data export use cases. Only applicable to Datasets
   * that have SavedQueries. Only Annotations that are associated with this
   * SavedQuery are used in respectively training. When used in conjunction with
   * annotations_filter, the Annotations used for training are filtered by both
   * saved_query_id and annotations_filter. Only one of saved_query_id and
   * annotation_schema_uri should be specified as both of them represent the
   * same thing: problem type.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExportDataConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExportDataConfig');

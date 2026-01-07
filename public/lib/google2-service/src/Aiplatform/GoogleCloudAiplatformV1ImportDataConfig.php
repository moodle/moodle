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

class GoogleCloudAiplatformV1ImportDataConfig extends \Google\Model
{
  /**
   * Labels that will be applied to newly imported Annotations. If two
   * Annotations are identical, one of them will be deduped. Two Annotations are
   * considered identical if their payload, payload_schema_uri and all of their
   * labels are the same. These labels will be overridden by Annotation labels
   * specified inside index file referenced by import_schema_uri, e.g. jsonl
   * file.
   *
   * @var string[]
   */
  public $annotationLabels;
  /**
   * Labels that will be applied to newly imported DataItems. If an identical
   * DataItem as one being imported already exists in the Dataset, then these
   * labels will be appended to these of the already existing one, and if labels
   * with identical key is imported before, the old label value will be
   * overwritten. If two DataItems are identical in the same import data
   * operation, the labels will be combined and if key collision happens in this
   * case, one of the values will be picked randomly. Two DataItems are
   * considered identical if their content bytes are identical (e.g. image bytes
   * or pdf bytes). These labels will be overridden by Annotation labels
   * specified inside index file referenced by import_schema_uri, e.g. jsonl
   * file.
   *
   * @var string[]
   */
  public $dataItemLabels;
  protected $gcsSourceType = GoogleCloudAiplatformV1GcsSource::class;
  protected $gcsSourceDataType = '';
  /**
   * Required. Points to a YAML file stored on Google Cloud Storage describing
   * the import format. Validation will be done against the schema. The schema
   * is defined as an [OpenAPI 3.0.2 Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject).
   *
   * @var string
   */
  public $importSchemaUri;

  /**
   * Labels that will be applied to newly imported Annotations. If two
   * Annotations are identical, one of them will be deduped. Two Annotations are
   * considered identical if their payload, payload_schema_uri and all of their
   * labels are the same. These labels will be overridden by Annotation labels
   * specified inside index file referenced by import_schema_uri, e.g. jsonl
   * file.
   *
   * @param string[] $annotationLabels
   */
  public function setAnnotationLabels($annotationLabels)
  {
    $this->annotationLabels = $annotationLabels;
  }
  /**
   * @return string[]
   */
  public function getAnnotationLabels()
  {
    return $this->annotationLabels;
  }
  /**
   * Labels that will be applied to newly imported DataItems. If an identical
   * DataItem as one being imported already exists in the Dataset, then these
   * labels will be appended to these of the already existing one, and if labels
   * with identical key is imported before, the old label value will be
   * overwritten. If two DataItems are identical in the same import data
   * operation, the labels will be combined and if key collision happens in this
   * case, one of the values will be picked randomly. Two DataItems are
   * considered identical if their content bytes are identical (e.g. image bytes
   * or pdf bytes). These labels will be overridden by Annotation labels
   * specified inside index file referenced by import_schema_uri, e.g. jsonl
   * file.
   *
   * @param string[] $dataItemLabels
   */
  public function setDataItemLabels($dataItemLabels)
  {
    $this->dataItemLabels = $dataItemLabels;
  }
  /**
   * @return string[]
   */
  public function getDataItemLabels()
  {
    return $this->dataItemLabels;
  }
  /**
   * The Google Cloud Storage location for the input content.
   *
   * @param GoogleCloudAiplatformV1GcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudAiplatformV1GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudAiplatformV1GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * Required. Points to a YAML file stored on Google Cloud Storage describing
   * the import format. Validation will be done against the schema. The schema
   * is defined as an [OpenAPI 3.0.2 Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject).
   *
   * @param string $importSchemaUri
   */
  public function setImportSchemaUri($importSchemaUri)
  {
    $this->importSchemaUri = $importSchemaUri;
  }
  /**
   * @return string
   */
  public function getImportSchemaUri()
  {
    return $this->importSchemaUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ImportDataConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ImportDataConfig');

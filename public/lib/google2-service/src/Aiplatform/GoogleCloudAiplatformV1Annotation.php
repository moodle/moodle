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

class GoogleCloudAiplatformV1Annotation extends \Google\Model
{
  protected $annotationSourceType = GoogleCloudAiplatformV1UserActionReference::class;
  protected $annotationSourceDataType = '';
  /**
   * Output only. Timestamp when this Annotation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Used to perform consistent read-modify-write updates. If not set,
   * a blind "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The labels with user-defined metadata to organize your
   * Annotations. Label keys and values can be no longer than 64 characters
   * (Unicode codepoints), can only contain lowercase letters, numeric
   * characters, underscores and dashes. International characters are allowed.
   * No more than 64 user labels can be associated with one Annotation(System
   * labels are excluded). See https://goo.gl/xmQnxf for more information and
   * examples of labels. System reserved label keys are prefixed with
   * "aiplatform.googleapis.com/" and are immutable. Following system labels
   * exist for each Annotation: *
   * "aiplatform.googleapis.com/annotation_set_name": optional, name of the UI's
   * annotation set this Annotation belongs to. If not set, the Annotation is
   * not visible in the UI. * "aiplatform.googleapis.com/payload_schema": output
   * only, its value is the payload_schema's title.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Resource name of the Annotation.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The schema of the payload can be found in payload_schema.
   *
   * @var array
   */
  public $payload;
  /**
   * Required. Google Cloud Storage URI points to a YAML file describing
   * payload. The schema is defined as an [OpenAPI 3.0.2 Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). The schema files
   * that can be used here are found in gs://google-cloud-
   * aiplatform/schema/dataset/annotation/, note that the chosen schema must be
   * consistent with the parent Dataset's metadata.
   *
   * @var string
   */
  public $payloadSchemaUri;
  /**
   * Output only. Timestamp when this Annotation was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The source of the Annotation.
   *
   * @param GoogleCloudAiplatformV1UserActionReference $annotationSource
   */
  public function setAnnotationSource(GoogleCloudAiplatformV1UserActionReference $annotationSource)
  {
    $this->annotationSource = $annotationSource;
  }
  /**
   * @return GoogleCloudAiplatformV1UserActionReference
   */
  public function getAnnotationSource()
  {
    return $this->annotationSource;
  }
  /**
   * Output only. Timestamp when this Annotation was created.
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
   * Optional. Used to perform consistent read-modify-write updates. If not set,
   * a blind "overwrite" update happens.
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
   * Optional. The labels with user-defined metadata to organize your
   * Annotations. Label keys and values can be no longer than 64 characters
   * (Unicode codepoints), can only contain lowercase letters, numeric
   * characters, underscores and dashes. International characters are allowed.
   * No more than 64 user labels can be associated with one Annotation(System
   * labels are excluded). See https://goo.gl/xmQnxf for more information and
   * examples of labels. System reserved label keys are prefixed with
   * "aiplatform.googleapis.com/" and are immutable. Following system labels
   * exist for each Annotation: *
   * "aiplatform.googleapis.com/annotation_set_name": optional, name of the UI's
   * annotation set this Annotation belongs to. If not set, the Annotation is
   * not visible in the UI. * "aiplatform.googleapis.com/payload_schema": output
   * only, its value is the payload_schema's title.
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
   * Output only. Resource name of the Annotation.
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
   * Required. The schema of the payload can be found in payload_schema.
   *
   * @param array $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return array
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * Required. Google Cloud Storage URI points to a YAML file describing
   * payload. The schema is defined as an [OpenAPI 3.0.2 Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). The schema files
   * that can be used here are found in gs://google-cloud-
   * aiplatform/schema/dataset/annotation/, note that the chosen schema must be
   * consistent with the parent Dataset's metadata.
   *
   * @param string $payloadSchemaUri
   */
  public function setPayloadSchemaUri($payloadSchemaUri)
  {
    $this->payloadSchemaUri = $payloadSchemaUri;
  }
  /**
   * @return string
   */
  public function getPayloadSchemaUri()
  {
    return $this->payloadSchemaUri;
  }
  /**
   * Output only. Timestamp when this Annotation was last updated.
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
class_alias(GoogleCloudAiplatformV1Annotation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Annotation');

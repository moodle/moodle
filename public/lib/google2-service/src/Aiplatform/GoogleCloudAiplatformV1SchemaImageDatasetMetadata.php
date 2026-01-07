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

class GoogleCloudAiplatformV1SchemaImageDatasetMetadata extends \Google\Model
{
  /**
   * Points to a YAML file stored on Google Cloud Storage describing payload of
   * the Image DataItems that belong to this Dataset.
   *
   * @var string
   */
  public $dataItemSchemaUri;
  /**
   * Google Cloud Storage Bucket name that contains the blob data of this
   * Dataset.
   *
   * @var string
   */
  public $gcsBucket;

  /**
   * Points to a YAML file stored on Google Cloud Storage describing payload of
   * the Image DataItems that belong to this Dataset.
   *
   * @param string $dataItemSchemaUri
   */
  public function setDataItemSchemaUri($dataItemSchemaUri)
  {
    $this->dataItemSchemaUri = $dataItemSchemaUri;
  }
  /**
   * @return string
   */
  public function getDataItemSchemaUri()
  {
    return $this->dataItemSchemaUri;
  }
  /**
   * Google Cloud Storage Bucket name that contains the blob data of this
   * Dataset.
   *
   * @param string $gcsBucket
   */
  public function setGcsBucket($gcsBucket)
  {
    $this->gcsBucket = $gcsBucket;
  }
  /**
   * @return string
   */
  public function getGcsBucket()
  {
    return $this->gcsBucket;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaImageDatasetMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaImageDatasetMetadata');

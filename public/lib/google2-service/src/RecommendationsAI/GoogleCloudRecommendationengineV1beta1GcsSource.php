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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1GcsSource extends \Google\Collection
{
  protected $collection_key = 'inputUris';
  /**
   * Required. Google Cloud Storage URIs to input files. URI can be up to 2000
   * characters long. URIs can match the full object path (for example,
   * `gs://bucket/directory/object.json`) or a pattern matching one or more
   * files, such as `gs://bucket/directory.json`. A request can contain at most
   * 100 files, and each file can be up to 2 GB. See [Importing catalog
   * information](/recommendations-ai/docs/upload-catalog) for the expected file
   * format and setup instructions.
   *
   * @var string[]
   */
  public $inputUris;
  /**
   * Optional. The schema to use when parsing the data from the source.
   * Supported values for catalog imports: 1: "catalog_recommendations_ai" using
   * https://cloud.google.com/recommendations-ai/docs/upload-catalog#json
   * (Default for catalogItems.import) 2: "catalog_merchant_center" using
   * https://cloud.google.com/recommendations-ai/docs/upload-catalog#mc
   * Supported values for user events imports: 1:
   * "user_events_recommendations_ai" using
   * https://cloud.google.com/recommendations-ai/docs/manage-user-events#import
   * (Default for userEvents.import) 2. "user_events_ga360" using
   * https://support.google.com/analytics/answer/3437719?hl=en
   *
   * @var string
   */
  public $jsonSchema;

  /**
   * Required. Google Cloud Storage URIs to input files. URI can be up to 2000
   * characters long. URIs can match the full object path (for example,
   * `gs://bucket/directory/object.json`) or a pattern matching one or more
   * files, such as `gs://bucket/directory.json`. A request can contain at most
   * 100 files, and each file can be up to 2 GB. See [Importing catalog
   * information](/recommendations-ai/docs/upload-catalog) for the expected file
   * format and setup instructions.
   *
   * @param string[] $inputUris
   */
  public function setInputUris($inputUris)
  {
    $this->inputUris = $inputUris;
  }
  /**
   * @return string[]
   */
  public function getInputUris()
  {
    return $this->inputUris;
  }
  /**
   * Optional. The schema to use when parsing the data from the source.
   * Supported values for catalog imports: 1: "catalog_recommendations_ai" using
   * https://cloud.google.com/recommendations-ai/docs/upload-catalog#json
   * (Default for catalogItems.import) 2: "catalog_merchant_center" using
   * https://cloud.google.com/recommendations-ai/docs/upload-catalog#mc
   * Supported values for user events imports: 1:
   * "user_events_recommendations_ai" using
   * https://cloud.google.com/recommendations-ai/docs/manage-user-events#import
   * (Default for userEvents.import) 2. "user_events_ga360" using
   * https://support.google.com/analytics/answer/3437719?hl=en
   *
   * @param string $jsonSchema
   */
  public function setJsonSchema($jsonSchema)
  {
    $this->jsonSchema = $jsonSchema;
  }
  /**
   * @return string
   */
  public function getJsonSchema()
  {
    return $this->jsonSchema;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1GcsSource::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1GcsSource');

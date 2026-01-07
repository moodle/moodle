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

namespace Google\Service\Datalineage;

class GoogleCloudDatacatalogLineageV1Origin extends \Google\Model
{
  /**
   * Source is Unspecified
   */
  public const SOURCE_TYPE_SOURCE_TYPE_UNSPECIFIED = 'SOURCE_TYPE_UNSPECIFIED';
  /**
   * A custom source
   */
  public const SOURCE_TYPE_CUSTOM = 'CUSTOM';
  /**
   * BigQuery
   */
  public const SOURCE_TYPE_BIGQUERY = 'BIGQUERY';
  /**
   * Data Fusion
   */
  public const SOURCE_TYPE_DATA_FUSION = 'DATA_FUSION';
  /**
   * Composer
   */
  public const SOURCE_TYPE_COMPOSER = 'COMPOSER';
  /**
   * Looker Studio
   */
  public const SOURCE_TYPE_LOOKER_STUDIO = 'LOOKER_STUDIO';
  /**
   * Dataproc
   */
  public const SOURCE_TYPE_DATAPROC = 'DATAPROC';
  /**
   * Vertex AI
   */
  public const SOURCE_TYPE_VERTEX_AI = 'VERTEX_AI';
  /**
   * If the source_type isn't CUSTOM, the value of this field should be a Google
   * Cloud resource name of the system, which reports lineage. The project and
   * location parts of the resource name must match the project and location of
   * the lineage resource being created. Examples: - `{source_type: COMPOSER,
   * name: "projects/foo/locations/us/environments/bar"}` - `{source_type:
   * BIGQUERY, name: "projects/foo/locations/eu"}` - `{source_type: CUSTOM,
   * name: "myCustomIntegration"}`
   *
   * @var string
   */
  public $name;
  /**
   * Type of the source. Use of a source_type other than `CUSTOM` for process
   * creation or updating is highly discouraged. It might be restricted in the
   * future without notice. There will be increase in cost if you use any of the
   * source types other than `CUSTOM`.
   *
   * @var string
   */
  public $sourceType;

  /**
   * If the source_type isn't CUSTOM, the value of this field should be a Google
   * Cloud resource name of the system, which reports lineage. The project and
   * location parts of the resource name must match the project and location of
   * the lineage resource being created. Examples: - `{source_type: COMPOSER,
   * name: "projects/foo/locations/us/environments/bar"}` - `{source_type:
   * BIGQUERY, name: "projects/foo/locations/eu"}` - `{source_type: CUSTOM,
   * name: "myCustomIntegration"}`
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
   * Type of the source. Use of a source_type other than `CUSTOM` for process
   * creation or updating is highly discouraged. It might be restricted in the
   * future without notice. There will be increase in cost if you use any of the
   * source types other than `CUSTOM`.
   *
   * Accepted values: SOURCE_TYPE_UNSPECIFIED, CUSTOM, BIGQUERY, DATA_FUSION,
   * COMPOSER, LOOKER_STUDIO, DATAPROC, VERTEX_AI
   *
   * @param self::SOURCE_TYPE_* $sourceType
   */
  public function setSourceType($sourceType)
  {
    $this->sourceType = $sourceType;
  }
  /**
   * @return self::SOURCE_TYPE_*
   */
  public function getSourceType()
  {
    return $this->sourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogLineageV1Origin::class, 'Google_Service_Datalineage_GoogleCloudDatacatalogLineageV1Origin');

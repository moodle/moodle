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

namespace Google\Service\CloudHealthcare;

class SchemaConfig extends \Google\Model
{
  /**
   * No schema type specified. This type is unsupported.
   */
  public const SCHEMA_TYPE_SCHEMA_TYPE_UNSPECIFIED = 'SCHEMA_TYPE_UNSPECIFIED';
  /**
   * Analytics schema defined by the FHIR community. See
   * https://github.com/FHIR/sql-on-fhir/blob/master/sql-on-fhir.md. BigQuery
   * only allows a maximum of 10,000 columns per table. Due to this limitation,
   * the server will not generate schemas for fields of type `Resource`, which
   * can hold any resource type. The affected fields are
   * `Parameters.parameter.resource`, `Bundle.entry.resource`, and
   * `Bundle.entry.response.outcome`. Analytics schema does not gracefully
   * handle extensions with one or more occurrences, anaytics schema also does
   * not handle contained resource. Additionally, extensions with a URL ending
   * in "/{existing_resource_field_name}" may cause undefined behavior.
   */
  public const SCHEMA_TYPE_ANALYTICS = 'ANALYTICS';
  /**
   * Analytics V2, similar to schema defined by the FHIR community, with added
   * support for extensions with one or more occurrences and contained resources
   * in stringified JSON. Extensions with a URL ending in
   * "/{existing_resource_field_name}" will cause conflict and prevent the
   * resource from being sent to BigQuery. Analytics V2 uses more space in the
   * destination table than Analytics V1. It is generally recommended to use
   * Analytics V2 over Analytics.
   */
  public const SCHEMA_TYPE_ANALYTICS_V2 = 'ANALYTICS_V2';
  protected $lastUpdatedPartitionConfigType = TimePartitioning::class;
  protected $lastUpdatedPartitionConfigDataType = '';
  /**
   * The depth for all recursive structures in the output analytics schema. For
   * example, `concept` in the CodeSystem resource is a recursive structure;
   * when the depth is 2, the CodeSystem table will have a column called
   * `concept.concept` but not `concept.concept.concept`. If not specified or
   * set to 0, the server will use the default value 2. The maximum depth
   * allowed is 5.
   *
   * @var string
   */
  public $recursiveStructureDepth;
  /**
   * Specifies the output schema type. Schema type is required.
   *
   * @var string
   */
  public $schemaType;

  /**
   * The configuration for exported BigQuery tables to be partitioned by FHIR
   * resource's last updated time column.
   *
   * @param TimePartitioning $lastUpdatedPartitionConfig
   */
  public function setLastUpdatedPartitionConfig(TimePartitioning $lastUpdatedPartitionConfig)
  {
    $this->lastUpdatedPartitionConfig = $lastUpdatedPartitionConfig;
  }
  /**
   * @return TimePartitioning
   */
  public function getLastUpdatedPartitionConfig()
  {
    return $this->lastUpdatedPartitionConfig;
  }
  /**
   * The depth for all recursive structures in the output analytics schema. For
   * example, `concept` in the CodeSystem resource is a recursive structure;
   * when the depth is 2, the CodeSystem table will have a column called
   * `concept.concept` but not `concept.concept.concept`. If not specified or
   * set to 0, the server will use the default value 2. The maximum depth
   * allowed is 5.
   *
   * @param string $recursiveStructureDepth
   */
  public function setRecursiveStructureDepth($recursiveStructureDepth)
  {
    $this->recursiveStructureDepth = $recursiveStructureDepth;
  }
  /**
   * @return string
   */
  public function getRecursiveStructureDepth()
  {
    return $this->recursiveStructureDepth;
  }
  /**
   * Specifies the output schema type. Schema type is required.
   *
   * Accepted values: SCHEMA_TYPE_UNSPECIFIED, ANALYTICS, ANALYTICS_V2
   *
   * @param self::SCHEMA_TYPE_* $schemaType
   */
  public function setSchemaType($schemaType)
  {
    $this->schemaType = $schemaType;
  }
  /**
   * @return self::SCHEMA_TYPE_*
   */
  public function getSchemaType()
  {
    return $this->schemaType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SchemaConfig::class, 'Google_Service_CloudHealthcare_SchemaConfig');

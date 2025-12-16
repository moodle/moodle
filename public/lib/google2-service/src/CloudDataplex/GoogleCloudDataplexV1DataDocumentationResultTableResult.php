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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataDocumentationResultTableResult extends \Google\Collection
{
  protected $collection_key = 'queries';
  /**
   * Output only. The service-qualified full resource name of the cloud
   * resource. Ex: //bigquery.googleapis.com/projects/PROJECT_ID/datasets/DATASE
   * T_ID/tables/TABLE_ID
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Generated description of the table.
   *
   * @var string
   */
  public $overview;
  protected $queriesType = GoogleCloudDataplexV1DataDocumentationResultQuery::class;
  protected $queriesDataType = 'array';
  protected $schemaType = GoogleCloudDataplexV1DataDocumentationResultSchema::class;
  protected $schemaDataType = '';

  /**
   * Output only. The service-qualified full resource name of the cloud
   * resource. Ex: //bigquery.googleapis.com/projects/PROJECT_ID/datasets/DATASE
   * T_ID/tables/TABLE_ID
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
   * Output only. Generated description of the table.
   *
   * @param string $overview
   */
  public function setOverview($overview)
  {
    $this->overview = $overview;
  }
  /**
   * @return string
   */
  public function getOverview()
  {
    return $this->overview;
  }
  /**
   * Output only. Sample SQL queries for the table.
   *
   * @param GoogleCloudDataplexV1DataDocumentationResultQuery[] $queries
   */
  public function setQueries($queries)
  {
    $this->queries = $queries;
  }
  /**
   * @return GoogleCloudDataplexV1DataDocumentationResultQuery[]
   */
  public function getQueries()
  {
    return $this->queries;
  }
  /**
   * Output only. Schema of the table with generated metadata of the columns in
   * the schema.
   *
   * @param GoogleCloudDataplexV1DataDocumentationResultSchema $schema
   */
  public function setSchema(GoogleCloudDataplexV1DataDocumentationResultSchema $schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return GoogleCloudDataplexV1DataDocumentationResultSchema
   */
  public function getSchema()
  {
    return $this->schema;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataDocumentationResultTableResult::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataDocumentationResultTableResult');

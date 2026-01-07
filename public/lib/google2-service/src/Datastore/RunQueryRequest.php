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

namespace Google\Service\Datastore;

class RunQueryRequest extends \Google\Model
{
  /**
   * The ID of the database against which to make the request. '(default)' is
   * not allowed; please use empty string '' to refer the default database.
   *
   * @var string
   */
  public $databaseId;
  protected $explainOptionsType = ExplainOptions::class;
  protected $explainOptionsDataType = '';
  protected $gqlQueryType = GqlQuery::class;
  protected $gqlQueryDataType = '';
  protected $partitionIdType = PartitionId::class;
  protected $partitionIdDataType = '';
  protected $propertyMaskType = PropertyMask::class;
  protected $propertyMaskDataType = '';
  protected $queryType = Query::class;
  protected $queryDataType = '';
  protected $readOptionsType = ReadOptions::class;
  protected $readOptionsDataType = '';

  /**
   * The ID of the database against which to make the request. '(default)' is
   * not allowed; please use empty string '' to refer the default database.
   *
   * @param string $databaseId
   */
  public function setDatabaseId($databaseId)
  {
    $this->databaseId = $databaseId;
  }
  /**
   * @return string
   */
  public function getDatabaseId()
  {
    return $this->databaseId;
  }
  /**
   * Optional. Explain options for the query. If set, additional query
   * statistics will be returned. If not, only query results will be returned.
   *
   * @param ExplainOptions $explainOptions
   */
  public function setExplainOptions(ExplainOptions $explainOptions)
  {
    $this->explainOptions = $explainOptions;
  }
  /**
   * @return ExplainOptions
   */
  public function getExplainOptions()
  {
    return $this->explainOptions;
  }
  /**
   * The GQL query to run. This query must be a non-aggregation query.
   *
   * @param GqlQuery $gqlQuery
   */
  public function setGqlQuery(GqlQuery $gqlQuery)
  {
    $this->gqlQuery = $gqlQuery;
  }
  /**
   * @return GqlQuery
   */
  public function getGqlQuery()
  {
    return $this->gqlQuery;
  }
  /**
   * Entities are partitioned into subsets, identified by a partition ID.
   * Queries are scoped to a single partition. This partition ID is normalized
   * with the standard default context partition ID.
   *
   * @param PartitionId $partitionId
   */
  public function setPartitionId(PartitionId $partitionId)
  {
    $this->partitionId = $partitionId;
  }
  /**
   * @return PartitionId
   */
  public function getPartitionId()
  {
    return $this->partitionId;
  }
  /**
   * The properties to return. This field must not be set for a projection
   * query. See LookupRequest.property_mask.
   *
   * @param PropertyMask $propertyMask
   */
  public function setPropertyMask(PropertyMask $propertyMask)
  {
    $this->propertyMask = $propertyMask;
  }
  /**
   * @return PropertyMask
   */
  public function getPropertyMask()
  {
    return $this->propertyMask;
  }
  /**
   * The query to run.
   *
   * @param Query $query
   */
  public function setQuery(Query $query)
  {
    $this->query = $query;
  }
  /**
   * @return Query
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * The options for this query.
   *
   * @param ReadOptions $readOptions
   */
  public function setReadOptions(ReadOptions $readOptions)
  {
    $this->readOptions = $readOptions;
  }
  /**
   * @return ReadOptions
   */
  public function getReadOptions()
  {
    return $this->readOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RunQueryRequest::class, 'Google_Service_Datastore_RunQueryRequest');

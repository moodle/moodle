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

namespace Google\Service\Connectors;

class Query extends \Google\Collection
{
  protected $collection_key = 'queryParameters';
  /**
   * Sets the limit for the maximum number of rows returned after the query
   * execution.
   *
   * @var string
   */
  public $maxRows;
  /**
   * Required. Sql query to execute.
   *
   * @var string
   */
  public $query;
  protected $queryParametersType = QueryParameter::class;
  protected $queryParametersDataType = 'array';
  /**
   * Sets the number of seconds the driver will wait for a query to execute.
   *
   * @var string
   */
  public $timeout;

  /**
   * Sets the limit for the maximum number of rows returned after the query
   * execution.
   *
   * @param string $maxRows
   */
  public function setMaxRows($maxRows)
  {
    $this->maxRows = $maxRows;
  }
  /**
   * @return string
   */
  public function getMaxRows()
  {
    return $this->maxRows;
  }
  /**
   * Required. Sql query to execute.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * In the struct, the value corresponds to the value of query parameter and
   * date type corresponds to the date type of the query parameter.
   *
   * @param QueryParameter[] $queryParameters
   */
  public function setQueryParameters($queryParameters)
  {
    $this->queryParameters = $queryParameters;
  }
  /**
   * @return QueryParameter[]
   */
  public function getQueryParameters()
  {
    return $this->queryParameters;
  }
  /**
   * Sets the number of seconds the driver will wait for a query to execute.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Query::class, 'Google_Service_Connectors_Query');

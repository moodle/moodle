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

namespace Google\Service\Dataproc;

class QueryList extends \Google\Collection
{
  protected $collection_key = 'queries';
  /**
   * Required. The queries to execute. You do not need to end a query expression
   * with a semicolon. Multiple queries can be specified in one string by
   * separating each with a semicolon. Here is an example of a Dataproc API
   * snippet that uses a QueryList to specify a HiveJob: "hiveJob": {
   * "queryList": { "queries": [ "query1", "query2", "query3;query4", ] } }
   *
   * @var string[]
   */
  public $queries;

  /**
   * Required. The queries to execute. You do not need to end a query expression
   * with a semicolon. Multiple queries can be specified in one string by
   * separating each with a semicolon. Here is an example of a Dataproc API
   * snippet that uses a QueryList to specify a HiveJob: "hiveJob": {
   * "queryList": { "queries": [ "query1", "query2", "query3;query4", ] } }
   *
   * @param string[] $queries
   */
  public function setQueries($queries)
  {
    $this->queries = $queries;
  }
  /**
   * @return string[]
   */
  public function getQueries()
  {
    return $this->queries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryList::class, 'Google_Service_Dataproc_QueryList');

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

class ExecutionStats extends \Google\Model
{
  /**
   * Debugging statistics from the execution of the query. Note that the
   * debugging stats are subject to change as Firestore evolves. It could
   * include: { "indexes_entries_scanned": "1000", "documents_scanned": "20",
   * "billing_details" : { "documents_billable": "20", "index_entries_billable":
   * "1000", "min_query_cost": "0" } }
   *
   * @var array[]
   */
  public $debugStats;
  /**
   * Total time to execute the query in the backend.
   *
   * @var string
   */
  public $executionDuration;
  /**
   * Total billable read operations.
   *
   * @var string
   */
  public $readOperations;
  /**
   * Total number of results returned, including documents, projections,
   * aggregation results, keys.
   *
   * @var string
   */
  public $resultsReturned;

  /**
   * Debugging statistics from the execution of the query. Note that the
   * debugging stats are subject to change as Firestore evolves. It could
   * include: { "indexes_entries_scanned": "1000", "documents_scanned": "20",
   * "billing_details" : { "documents_billable": "20", "index_entries_billable":
   * "1000", "min_query_cost": "0" } }
   *
   * @param array[] $debugStats
   */
  public function setDebugStats($debugStats)
  {
    $this->debugStats = $debugStats;
  }
  /**
   * @return array[]
   */
  public function getDebugStats()
  {
    return $this->debugStats;
  }
  /**
   * Total time to execute the query in the backend.
   *
   * @param string $executionDuration
   */
  public function setExecutionDuration($executionDuration)
  {
    $this->executionDuration = $executionDuration;
  }
  /**
   * @return string
   */
  public function getExecutionDuration()
  {
    return $this->executionDuration;
  }
  /**
   * Total billable read operations.
   *
   * @param string $readOperations
   */
  public function setReadOperations($readOperations)
  {
    $this->readOperations = $readOperations;
  }
  /**
   * @return string
   */
  public function getReadOperations()
  {
    return $this->readOperations;
  }
  /**
   * Total number of results returned, including documents, projections,
   * aggregation results, keys.
   *
   * @param string $resultsReturned
   */
  public function setResultsReturned($resultsReturned)
  {
    $this->resultsReturned = $resultsReturned;
  }
  /**
   * @return string
   */
  public function getResultsReturned()
  {
    return $this->resultsReturned;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecutionStats::class, 'Google_Service_Datastore_ExecutionStats');

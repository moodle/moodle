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

namespace Google\Service\Dataform;

class InvocationConfig extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const QUERY_PRIORITY_QUERY_PRIORITY_UNSPECIFIED = 'QUERY_PRIORITY_UNSPECIFIED';
  /**
   * Query will be executed in BigQuery with interactive priority. More
   * information can be found at https://cloud.google.com/bigquery/docs/running-
   * queries#queries.
   */
  public const QUERY_PRIORITY_INTERACTIVE = 'INTERACTIVE';
  /**
   * Query will be executed in BigQuery with batch priority. More information
   * can be found at https://cloud.google.com/bigquery/docs/running-
   * queries#batchqueries.
   */
  public const QUERY_PRIORITY_BATCH = 'BATCH';
  protected $collection_key = 'includedTargets';
  /**
   * Optional. When set to true, any incremental tables will be fully refreshed.
   *
   * @var bool
   */
  public $fullyRefreshIncrementalTablesEnabled;
  /**
   * Optional. The set of tags to include.
   *
   * @var string[]
   */
  public $includedTags;
  protected $includedTargetsType = Target::class;
  protected $includedTargetsDataType = 'array';
  /**
   * Optional. Specifies the priority for query execution in BigQuery. More
   * information can be found at https://cloud.google.com/bigquery/docs/running-
   * queries#queries.
   *
   * @var string
   */
  public $queryPriority;
  /**
   * Optional. The service account to run workflow invocations under.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Optional. When set to true, transitive dependencies of included actions
   * will be executed.
   *
   * @var bool
   */
  public $transitiveDependenciesIncluded;
  /**
   * Optional. When set to true, transitive dependents of included actions will
   * be executed.
   *
   * @var bool
   */
  public $transitiveDependentsIncluded;

  /**
   * Optional. When set to true, any incremental tables will be fully refreshed.
   *
   * @param bool $fullyRefreshIncrementalTablesEnabled
   */
  public function setFullyRefreshIncrementalTablesEnabled($fullyRefreshIncrementalTablesEnabled)
  {
    $this->fullyRefreshIncrementalTablesEnabled = $fullyRefreshIncrementalTablesEnabled;
  }
  /**
   * @return bool
   */
  public function getFullyRefreshIncrementalTablesEnabled()
  {
    return $this->fullyRefreshIncrementalTablesEnabled;
  }
  /**
   * Optional. The set of tags to include.
   *
   * @param string[] $includedTags
   */
  public function setIncludedTags($includedTags)
  {
    $this->includedTags = $includedTags;
  }
  /**
   * @return string[]
   */
  public function getIncludedTags()
  {
    return $this->includedTags;
  }
  /**
   * Optional. The set of action identifiers to include.
   *
   * @param Target[] $includedTargets
   */
  public function setIncludedTargets($includedTargets)
  {
    $this->includedTargets = $includedTargets;
  }
  /**
   * @return Target[]
   */
  public function getIncludedTargets()
  {
    return $this->includedTargets;
  }
  /**
   * Optional. Specifies the priority for query execution in BigQuery. More
   * information can be found at https://cloud.google.com/bigquery/docs/running-
   * queries#queries.
   *
   * Accepted values: QUERY_PRIORITY_UNSPECIFIED, INTERACTIVE, BATCH
   *
   * @param self::QUERY_PRIORITY_* $queryPriority
   */
  public function setQueryPriority($queryPriority)
  {
    $this->queryPriority = $queryPriority;
  }
  /**
   * @return self::QUERY_PRIORITY_*
   */
  public function getQueryPriority()
  {
    return $this->queryPriority;
  }
  /**
   * Optional. The service account to run workflow invocations under.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Optional. When set to true, transitive dependencies of included actions
   * will be executed.
   *
   * @param bool $transitiveDependenciesIncluded
   */
  public function setTransitiveDependenciesIncluded($transitiveDependenciesIncluded)
  {
    $this->transitiveDependenciesIncluded = $transitiveDependenciesIncluded;
  }
  /**
   * @return bool
   */
  public function getTransitiveDependenciesIncluded()
  {
    return $this->transitiveDependenciesIncluded;
  }
  /**
   * Optional. When set to true, transitive dependents of included actions will
   * be executed.
   *
   * @param bool $transitiveDependentsIncluded
   */
  public function setTransitiveDependentsIncluded($transitiveDependentsIncluded)
  {
    $this->transitiveDependentsIncluded = $transitiveDependentsIncluded;
  }
  /**
   * @return bool
   */
  public function getTransitiveDependentsIncluded()
  {
    return $this->transitiveDependentsIncluded;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InvocationConfig::class, 'Google_Service_Dataform_InvocationConfig');

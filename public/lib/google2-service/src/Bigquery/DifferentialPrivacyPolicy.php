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

namespace Google\Service\Bigquery;

class DifferentialPrivacyPolicy extends \Google\Model
{
  /**
   * Optional. The total delta budget for all queries against the privacy-
   * protected view. Each subscriber query against this view charges the amount
   * of delta that is pre-defined by the contributor through the privacy policy
   * delta_per_query field. If there is sufficient budget, then the subscriber
   * query attempts to complete. It might still fail due to other reasons, in
   * which case the charge is refunded. If there is insufficient budget the
   * query is rejected. There might be multiple charge attempts if a single
   * query references multiple views. In this case there must be sufficient
   * budget for all charges or the query is rejected and charges are refunded in
   * best effort. The budget does not have a refresh policy and can only be
   * updated via ALTER VIEW or circumvented by creating a new view that can be
   * queried with a fresh budget.
   *
   * @var 
   */
  public $deltaBudget;
  /**
   * Output only. The delta budget remaining. If budget is exhausted, no more
   * queries are allowed. Note that the budget for queries that are in progress
   * is deducted before the query executes. If the query fails or is cancelled
   * then the budget is refunded. In this case the amount of budget remaining
   * can increase.
   *
   * @var 
   */
  public $deltaBudgetRemaining;
  /**
   * Optional. The delta value that is used per query. Delta represents the
   * probability that any row will fail to be epsilon differentially private.
   * Indicates the risk associated with exposing aggregate rows in the result of
   * a query.
   *
   * @var 
   */
  public $deltaPerQuery;
  /**
   * Optional. The total epsilon budget for all queries against the privacy-
   * protected view. Each subscriber query against this view charges the amount
   * of epsilon they request in their query. If there is sufficient budget, then
   * the subscriber query attempts to complete. It might still fail due to other
   * reasons, in which case the charge is refunded. If there is insufficient
   * budget the query is rejected. There might be multiple charge attempts if a
   * single query references multiple views. In this case there must be
   * sufficient budget for all charges or the query is rejected and charges are
   * refunded in best effort. The budget does not have a refresh policy and can
   * only be updated via ALTER VIEW or circumvented by creating a new view that
   * can be queried with a fresh budget.
   *
   * @var 
   */
  public $epsilonBudget;
  /**
   * Output only. The epsilon budget remaining. If budget is exhausted, no more
   * queries are allowed. Note that the budget for queries that are in progress
   * is deducted before the query executes. If the query fails or is cancelled
   * then the budget is refunded. In this case the amount of budget remaining
   * can increase.
   *
   * @var 
   */
  public $epsilonBudgetRemaining;
  /**
   * Optional. The maximum epsilon value that a query can consume. If the
   * subscriber specifies epsilon as a parameter in a SELECT query, it must be
   * less than or equal to this value. The epsilon parameter controls the amount
   * of noise that is added to the groups — a higher epsilon means less noise.
   *
   * @var 
   */
  public $maxEpsilonPerQuery;
  /**
   * Optional. The maximum groups contributed value that is used per query.
   * Represents the maximum number of groups to which each protected entity can
   * contribute. Changing this value does not improve or worsen privacy. The
   * best value for accuracy and utility depends on the query and data.
   *
   * @var string
   */
  public $maxGroupsContributed;
  /**
   * Optional. The privacy unit column associated with this policy. Differential
   * privacy policies can only have one privacy unit column per data source
   * object (table, view).
   *
   * @var string
   */
  public $privacyUnitColumn;

  public function setDeltaBudget($deltaBudget)
  {
    $this->deltaBudget = $deltaBudget;
  }
  public function getDeltaBudget()
  {
    return $this->deltaBudget;
  }
  public function setDeltaBudgetRemaining($deltaBudgetRemaining)
  {
    $this->deltaBudgetRemaining = $deltaBudgetRemaining;
  }
  public function getDeltaBudgetRemaining()
  {
    return $this->deltaBudgetRemaining;
  }
  public function setDeltaPerQuery($deltaPerQuery)
  {
    $this->deltaPerQuery = $deltaPerQuery;
  }
  public function getDeltaPerQuery()
  {
    return $this->deltaPerQuery;
  }
  public function setEpsilonBudget($epsilonBudget)
  {
    $this->epsilonBudget = $epsilonBudget;
  }
  public function getEpsilonBudget()
  {
    return $this->epsilonBudget;
  }
  public function setEpsilonBudgetRemaining($epsilonBudgetRemaining)
  {
    $this->epsilonBudgetRemaining = $epsilonBudgetRemaining;
  }
  public function getEpsilonBudgetRemaining()
  {
    return $this->epsilonBudgetRemaining;
  }
  public function setMaxEpsilonPerQuery($maxEpsilonPerQuery)
  {
    $this->maxEpsilonPerQuery = $maxEpsilonPerQuery;
  }
  public function getMaxEpsilonPerQuery()
  {
    return $this->maxEpsilonPerQuery;
  }
  /**
   * Optional. The maximum groups contributed value that is used per query.
   * Represents the maximum number of groups to which each protected entity can
   * contribute. Changing this value does not improve or worsen privacy. The
   * best value for accuracy and utility depends on the query and data.
   *
   * @param string $maxGroupsContributed
   */
  public function setMaxGroupsContributed($maxGroupsContributed)
  {
    $this->maxGroupsContributed = $maxGroupsContributed;
  }
  /**
   * @return string
   */
  public function getMaxGroupsContributed()
  {
    return $this->maxGroupsContributed;
  }
  /**
   * Optional. The privacy unit column associated with this policy. Differential
   * privacy policies can only have one privacy unit column per data source
   * object (table, view).
   *
   * @param string $privacyUnitColumn
   */
  public function setPrivacyUnitColumn($privacyUnitColumn)
  {
    $this->privacyUnitColumn = $privacyUnitColumn;
  }
  /**
   * @return string
   */
  public function getPrivacyUnitColumn()
  {
    return $this->privacyUnitColumn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DifferentialPrivacyPolicy::class, 'Google_Service_Bigquery_DifferentialPrivacyPolicy');

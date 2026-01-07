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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaControlBoostAction extends \Google\Model
{
  /**
   * Strength of the boost, which should be in [-1, 1]. Negative boost means
   * demotion. Default is 0.0 (No-op).
   *
   * @deprecated
   * @var float
   */
  public $boost;
  /**
   * Required. Specifies which data store's documents can be boosted by this
   * control. Full data store name e.g. projects/123/locations/global/collection
   * s/default_collection/dataStores/default_data_store
   *
   * @var string
   */
  public $dataStore;
  /**
   * Required. Specifies which products to apply the boost to. If no filter is
   * provided all products will be boosted (No-op). Syntax documentation:
   * https://cloud.google.com/retail/docs/filter-and-order Maximum length is
   * 5000 characters. Otherwise an INVALID ARGUMENT error is thrown.
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. Strength of the boost, which should be in [-1, 1]. Negative boost
   * means demotion. Default is 0.0 (No-op).
   *
   * @var float
   */
  public $fixedBoost;
  protected $interpolationBoostSpecType = GoogleCloudDiscoveryengineV1betaControlBoostActionInterpolationBoostSpec::class;
  protected $interpolationBoostSpecDataType = '';

  /**
   * Strength of the boost, which should be in [-1, 1]. Negative boost means
   * demotion. Default is 0.0 (No-op).
   *
   * @deprecated
   * @param float $boost
   */
  public function setBoost($boost)
  {
    $this->boost = $boost;
  }
  /**
   * @deprecated
   * @return float
   */
  public function getBoost()
  {
    return $this->boost;
  }
  /**
   * Required. Specifies which data store's documents can be boosted by this
   * control. Full data store name e.g. projects/123/locations/global/collection
   * s/default_collection/dataStores/default_data_store
   *
   * @param string $dataStore
   */
  public function setDataStore($dataStore)
  {
    $this->dataStore = $dataStore;
  }
  /**
   * @return string
   */
  public function getDataStore()
  {
    return $this->dataStore;
  }
  /**
   * Required. Specifies which products to apply the boost to. If no filter is
   * provided all products will be boosted (No-op). Syntax documentation:
   * https://cloud.google.com/retail/docs/filter-and-order Maximum length is
   * 5000 characters. Otherwise an INVALID ARGUMENT error is thrown.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. Strength of the boost, which should be in [-1, 1]. Negative boost
   * means demotion. Default is 0.0 (No-op).
   *
   * @param float $fixedBoost
   */
  public function setFixedBoost($fixedBoost)
  {
    $this->fixedBoost = $fixedBoost;
  }
  /**
   * @return float
   */
  public function getFixedBoost()
  {
    return $this->fixedBoost;
  }
  /**
   * Optional. Complex specification for custom ranking based on customer
   * defined attribute value.
   *
   * @param GoogleCloudDiscoveryengineV1betaControlBoostActionInterpolationBoostSpec $interpolationBoostSpec
   */
  public function setInterpolationBoostSpec(GoogleCloudDiscoveryengineV1betaControlBoostActionInterpolationBoostSpec $interpolationBoostSpec)
  {
    $this->interpolationBoostSpec = $interpolationBoostSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaControlBoostActionInterpolationBoostSpec
   */
  public function getInterpolationBoostSpec()
  {
    return $this->interpolationBoostSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaControlBoostAction::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaControlBoostAction');

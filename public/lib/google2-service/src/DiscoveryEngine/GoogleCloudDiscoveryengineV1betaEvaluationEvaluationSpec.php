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

class GoogleCloudDiscoveryengineV1betaEvaluationEvaluationSpec extends \Google\Model
{
  protected $querySetSpecType = GoogleCloudDiscoveryengineV1betaEvaluationEvaluationSpecQuerySetSpec::class;
  protected $querySetSpecDataType = '';
  protected $searchRequestType = GoogleCloudDiscoveryengineV1betaSearchRequest::class;
  protected $searchRequestDataType = '';

  /**
   * Optional. The specification of the query set.
   *
   * @param GoogleCloudDiscoveryengineV1betaEvaluationEvaluationSpecQuerySetSpec $querySetSpec
   */
  public function setQuerySetSpec(GoogleCloudDiscoveryengineV1betaEvaluationEvaluationSpecQuerySetSpec $querySetSpec)
  {
    $this->querySetSpec = $querySetSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaEvaluationEvaluationSpecQuerySetSpec
   */
  public function getQuerySetSpec()
  {
    return $this->querySetSpec;
  }
  /**
   * Required. The search request that is used to perform the evaluation. Only
   * the following fields within SearchRequest are supported; if any other
   * fields are provided, an UNSUPPORTED error will be returned: *
   * SearchRequest.serving_config * SearchRequest.branch *
   * SearchRequest.canonical_filter * SearchRequest.query_expansion_spec *
   * SearchRequest.spell_correction_spec * SearchRequest.content_search_spec *
   * SearchRequest.user_pseudo_id
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequest $searchRequest
   */
  public function setSearchRequest(GoogleCloudDiscoveryengineV1betaSearchRequest $searchRequest)
  {
    $this->searchRequest = $searchRequest;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequest
   */
  public function getSearchRequest()
  {
    return $this->searchRequest;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaEvaluationEvaluationSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaEvaluationEvaluationSpec');

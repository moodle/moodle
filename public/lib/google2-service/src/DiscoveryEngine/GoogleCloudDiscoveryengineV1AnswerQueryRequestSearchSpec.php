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

class GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpec extends \Google\Model
{
  protected $searchParamsType = GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchParams::class;
  protected $searchParamsDataType = '';
  protected $searchResultListType = GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultList::class;
  protected $searchResultListDataType = '';

  /**
   * Search parameters.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchParams $searchParams
   */
  public function setSearchParams(GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchParams $searchParams)
  {
    $this->searchParams = $searchParams;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchParams
   */
  public function getSearchParams()
  {
    return $this->searchParams;
  }
  /**
   * Search result list.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultList $searchResultList
   */
  public function setSearchResultList(GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultList $searchResultList)
  {
    $this->searchResultList = $searchResultList;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultList
   */
  public function getSearchResultList()
  {
    return $this->searchResultList;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpec');

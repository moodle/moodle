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

class GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSource extends \Google\Model
{
  protected $enterpriseWebRetrievalSourceType = GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceEnterpriseWebRetrievalSource::class;
  protected $enterpriseWebRetrievalSourceDataType = '';
  protected $googleSearchSourceType = GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceGoogleSearchSource::class;
  protected $googleSearchSourceDataType = '';
  protected $inlineSourceType = GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceInlineSource::class;
  protected $inlineSourceDataType = '';
  protected $searchSourceType = GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceSearchSource::class;
  protected $searchSourceDataType = '';

  /**
   * @param GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceEnterpriseWebRetrievalSource
   */
  public function setEnterpriseWebRetrievalSource(GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceEnterpriseWebRetrievalSource $enterpriseWebRetrievalSource)
  {
    $this->enterpriseWebRetrievalSource = $enterpriseWebRetrievalSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceEnterpriseWebRetrievalSource
   */
  public function getEnterpriseWebRetrievalSource()
  {
    return $this->enterpriseWebRetrievalSource;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceGoogleSearchSource
   */
  public function setGoogleSearchSource(GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceGoogleSearchSource $googleSearchSource)
  {
    $this->googleSearchSource = $googleSearchSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceGoogleSearchSource
   */
  public function getGoogleSearchSource()
  {
    return $this->googleSearchSource;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceInlineSource
   */
  public function setInlineSource(GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceInlineSource $inlineSource)
  {
    $this->inlineSource = $inlineSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceInlineSource
   */
  public function getInlineSource()
  {
    return $this->inlineSource;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceSearchSource
   */
  public function setSearchSource(GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceSearchSource $searchSource)
  {
    $this->searchSource = $searchSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceSearchSource
   */
  public function getSearchSource()
  {
    return $this->searchSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSource::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSource');

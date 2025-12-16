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

class GoogleCloudDiscoveryengineV1betaOrganicCrawlRateTimeSeries extends \Google\Model
{
  protected $googleOrganicCrawlRateType = GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries::class;
  protected $googleOrganicCrawlRateDataType = '';
  protected $vertexAiOrganicCrawlRateType = GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries::class;
  protected $vertexAiOrganicCrawlRateDataType = '';

  /**
   * Google's organic crawl rate time series, which is the sum of all
   * googlebots' crawl rate. Please refer to
   * https://developers.google.com/search/docs/crawling-indexing/overview-
   * google-crawlers for more details about googlebots.
   *
   * @param GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries $googleOrganicCrawlRate
   */
  public function setGoogleOrganicCrawlRate(GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries $googleOrganicCrawlRate)
  {
    $this->googleOrganicCrawlRate = $googleOrganicCrawlRate;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries
   */
  public function getGoogleOrganicCrawlRate()
  {
    return $this->googleOrganicCrawlRate;
  }
  /**
   * Vertex AI's organic crawl rate time series, which is the crawl rate of
   * Google-CloudVertexBot when dedicate crawl is not set. Please refer to
   * https://developers.google.com/search/docs/crawling-indexing/google-common-
   * crawlers#google-cloudvertexbot for more details about Google-
   * CloudVertexBot.
   *
   * @param GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries $vertexAiOrganicCrawlRate
   */
  public function setVertexAiOrganicCrawlRate(GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries $vertexAiOrganicCrawlRate)
  {
    $this->vertexAiOrganicCrawlRate = $vertexAiOrganicCrawlRate;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries
   */
  public function getVertexAiOrganicCrawlRate()
  {
    return $this->vertexAiOrganicCrawlRate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaOrganicCrawlRateTimeSeries::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaOrganicCrawlRateTimeSeries');

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

class GoogleCloudDiscoveryengineV1betaDedicatedCrawlRateTimeSeries extends \Google\Model
{
  protected $autoRefreshCrawlErrorRateType = GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries::class;
  protected $autoRefreshCrawlErrorRateDataType = '';
  protected $autoRefreshCrawlRateType = GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries::class;
  protected $autoRefreshCrawlRateDataType = '';
  protected $userTriggeredCrawlErrorRateType = GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries::class;
  protected $userTriggeredCrawlErrorRateDataType = '';
  protected $userTriggeredCrawlRateType = GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries::class;
  protected $userTriggeredCrawlRateDataType = '';

  /**
   * Vertex AI's error rate time series of auto-refresh dedicated crawl.
   *
   * @param GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries $autoRefreshCrawlErrorRate
   */
  public function setAutoRefreshCrawlErrorRate(GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries $autoRefreshCrawlErrorRate)
  {
    $this->autoRefreshCrawlErrorRate = $autoRefreshCrawlErrorRate;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries
   */
  public function getAutoRefreshCrawlErrorRate()
  {
    return $this->autoRefreshCrawlErrorRate;
  }
  /**
   * Vertex AI's dedicated crawl rate time series of auto-refresh, which is the
   * crawl rate of Google-CloudVertexBot when dedicate crawl is set, and the
   * crawl rate is for best effort use cases like refreshing urls periodically.
   *
   * @param GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries $autoRefreshCrawlRate
   */
  public function setAutoRefreshCrawlRate(GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries $autoRefreshCrawlRate)
  {
    $this->autoRefreshCrawlRate = $autoRefreshCrawlRate;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries
   */
  public function getAutoRefreshCrawlRate()
  {
    return $this->autoRefreshCrawlRate;
  }
  /**
   * Vertex AI's error rate time series of user triggered dedicated crawl.
   *
   * @param GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries $userTriggeredCrawlErrorRate
   */
  public function setUserTriggeredCrawlErrorRate(GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries $userTriggeredCrawlErrorRate)
  {
    $this->userTriggeredCrawlErrorRate = $userTriggeredCrawlErrorRate;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries
   */
  public function getUserTriggeredCrawlErrorRate()
  {
    return $this->userTriggeredCrawlErrorRate;
  }
  /**
   * Vertex AI's dedicated crawl rate time series of user triggered crawl, which
   * is the crawl rate of Google-CloudVertexBot when dedicate crawl is set, and
   * user triggered crawl rate is for deterministic use cases like crawling urls
   * or sitemaps specified by users.
   *
   * @param GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries $userTriggeredCrawlRate
   */
  public function setUserTriggeredCrawlRate(GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries $userTriggeredCrawlRate)
  {
    $this->userTriggeredCrawlRate = $userTriggeredCrawlRate;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaCrawlRateTimeSeries
   */
  public function getUserTriggeredCrawlRate()
  {
    return $this->userTriggeredCrawlRate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaDedicatedCrawlRateTimeSeries::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaDedicatedCrawlRateTimeSeries');

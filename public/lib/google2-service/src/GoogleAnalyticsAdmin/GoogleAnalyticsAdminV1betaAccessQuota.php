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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaAccessQuota extends \Google\Model
{
  protected $concurrentRequestsType = GoogleAnalyticsAdminV1betaAccessQuotaStatus::class;
  protected $concurrentRequestsDataType = '';
  protected $serverErrorsPerProjectPerHourType = GoogleAnalyticsAdminV1betaAccessQuotaStatus::class;
  protected $serverErrorsPerProjectPerHourDataType = '';
  protected $tokensPerDayType = GoogleAnalyticsAdminV1betaAccessQuotaStatus::class;
  protected $tokensPerDayDataType = '';
  protected $tokensPerHourType = GoogleAnalyticsAdminV1betaAccessQuotaStatus::class;
  protected $tokensPerHourDataType = '';
  protected $tokensPerProjectPerHourType = GoogleAnalyticsAdminV1betaAccessQuotaStatus::class;
  protected $tokensPerProjectPerHourDataType = '';

  /**
   * Properties can use up to 50 concurrent requests.
   *
   * @param GoogleAnalyticsAdminV1betaAccessQuotaStatus $concurrentRequests
   */
  public function setConcurrentRequests(GoogleAnalyticsAdminV1betaAccessQuotaStatus $concurrentRequests)
  {
    $this->concurrentRequests = $concurrentRequests;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessQuotaStatus
   */
  public function getConcurrentRequests()
  {
    return $this->concurrentRequests;
  }
  /**
   * Properties and cloud project pairs can have up to 50 server errors per
   * hour.
   *
   * @param GoogleAnalyticsAdminV1betaAccessQuotaStatus $serverErrorsPerProjectPerHour
   */
  public function setServerErrorsPerProjectPerHour(GoogleAnalyticsAdminV1betaAccessQuotaStatus $serverErrorsPerProjectPerHour)
  {
    $this->serverErrorsPerProjectPerHour = $serverErrorsPerProjectPerHour;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessQuotaStatus
   */
  public function getServerErrorsPerProjectPerHour()
  {
    return $this->serverErrorsPerProjectPerHour;
  }
  /**
   * Properties can use 250,000 tokens per day. Most requests consume fewer than
   * 10 tokens.
   *
   * @param GoogleAnalyticsAdminV1betaAccessQuotaStatus $tokensPerDay
   */
  public function setTokensPerDay(GoogleAnalyticsAdminV1betaAccessQuotaStatus $tokensPerDay)
  {
    $this->tokensPerDay = $tokensPerDay;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessQuotaStatus
   */
  public function getTokensPerDay()
  {
    return $this->tokensPerDay;
  }
  /**
   * Properties can use 50,000 tokens per hour. An API request consumes a single
   * number of tokens, and that number is deducted from all of the hourly,
   * daily, and per project hourly quotas.
   *
   * @param GoogleAnalyticsAdminV1betaAccessQuotaStatus $tokensPerHour
   */
  public function setTokensPerHour(GoogleAnalyticsAdminV1betaAccessQuotaStatus $tokensPerHour)
  {
    $this->tokensPerHour = $tokensPerHour;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessQuotaStatus
   */
  public function getTokensPerHour()
  {
    return $this->tokensPerHour;
  }
  /**
   * Properties can use up to 25% of their tokens per project per hour. This
   * amounts to Analytics 360 Properties can use 12,500 tokens per project per
   * hour. An API request consumes a single number of tokens, and that number is
   * deducted from all of the hourly, daily, and per project hourly quotas.
   *
   * @param GoogleAnalyticsAdminV1betaAccessQuotaStatus $tokensPerProjectPerHour
   */
  public function setTokensPerProjectPerHour(GoogleAnalyticsAdminV1betaAccessQuotaStatus $tokensPerProjectPerHour)
  {
    $this->tokensPerProjectPerHour = $tokensPerProjectPerHour;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessQuotaStatus
   */
  public function getTokensPerProjectPerHour()
  {
    return $this->tokensPerProjectPerHour;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaAccessQuota::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaAccessQuota');

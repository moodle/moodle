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

namespace Google\Service\AnalyticsData;

class PropertyQuota extends \Google\Model
{
  protected $concurrentRequestsType = QuotaStatus::class;
  protected $concurrentRequestsDataType = '';
  protected $potentiallyThresholdedRequestsPerHourType = QuotaStatus::class;
  protected $potentiallyThresholdedRequestsPerHourDataType = '';
  protected $serverErrorsPerProjectPerHourType = QuotaStatus::class;
  protected $serverErrorsPerProjectPerHourDataType = '';
  protected $tokensPerDayType = QuotaStatus::class;
  protected $tokensPerDayDataType = '';
  protected $tokensPerHourType = QuotaStatus::class;
  protected $tokensPerHourDataType = '';
  protected $tokensPerProjectPerHourType = QuotaStatus::class;
  protected $tokensPerProjectPerHourDataType = '';

  /**
   * Standard Analytics Properties can send up to 10 concurrent requests;
   * Analytics 360 Properties can use up to 50 concurrent requests.
   *
   * @param QuotaStatus $concurrentRequests
   */
  public function setConcurrentRequests(QuotaStatus $concurrentRequests)
  {
    $this->concurrentRequests = $concurrentRequests;
  }
  /**
   * @return QuotaStatus
   */
  public function getConcurrentRequests()
  {
    return $this->concurrentRequests;
  }
  /**
   * Analytics Properties can send up to 120 requests with potentially
   * thresholded dimensions per hour. In a batch request, each report request is
   * individually counted for this quota if the request contains potentially
   * thresholded dimensions.
   *
   * @param QuotaStatus $potentiallyThresholdedRequestsPerHour
   */
  public function setPotentiallyThresholdedRequestsPerHour(QuotaStatus $potentiallyThresholdedRequestsPerHour)
  {
    $this->potentiallyThresholdedRequestsPerHour = $potentiallyThresholdedRequestsPerHour;
  }
  /**
   * @return QuotaStatus
   */
  public function getPotentiallyThresholdedRequestsPerHour()
  {
    return $this->potentiallyThresholdedRequestsPerHour;
  }
  /**
   * Standard Analytics Properties and cloud project pairs can have up to 10
   * server errors per hour; Analytics 360 Properties and cloud project pairs
   * can have up to 50 server errors per hour.
   *
   * @param QuotaStatus $serverErrorsPerProjectPerHour
   */
  public function setServerErrorsPerProjectPerHour(QuotaStatus $serverErrorsPerProjectPerHour)
  {
    $this->serverErrorsPerProjectPerHour = $serverErrorsPerProjectPerHour;
  }
  /**
   * @return QuotaStatus
   */
  public function getServerErrorsPerProjectPerHour()
  {
    return $this->serverErrorsPerProjectPerHour;
  }
  /**
   * Standard Analytics Properties can use up to 200,000 tokens per day;
   * Analytics 360 Properties can use 2,000,000 tokens per day. Most requests
   * consume fewer than 10 tokens.
   *
   * @param QuotaStatus $tokensPerDay
   */
  public function setTokensPerDay(QuotaStatus $tokensPerDay)
  {
    $this->tokensPerDay = $tokensPerDay;
  }
  /**
   * @return QuotaStatus
   */
  public function getTokensPerDay()
  {
    return $this->tokensPerDay;
  }
  /**
   * Standard Analytics Properties can use up to 40,000 tokens per hour;
   * Analytics 360 Properties can use 400,000 tokens per hour. An API request
   * consumes a single number of tokens, and that number is deducted from all of
   * the hourly, daily, and per project hourly quotas.
   *
   * @param QuotaStatus $tokensPerHour
   */
  public function setTokensPerHour(QuotaStatus $tokensPerHour)
  {
    $this->tokensPerHour = $tokensPerHour;
  }
  /**
   * @return QuotaStatus
   */
  public function getTokensPerHour()
  {
    return $this->tokensPerHour;
  }
  /**
   * Analytics Properties can use up to 35% of their tokens per project per
   * hour. This amounts to standard Analytics Properties can use up to 14,000
   * tokens per project per hour, and Analytics 360 Properties can use 140,000
   * tokens per project per hour. An API request consumes a single number of
   * tokens, and that number is deducted from all of the hourly, daily, and per
   * project hourly quotas.
   *
   * @param QuotaStatus $tokensPerProjectPerHour
   */
  public function setTokensPerProjectPerHour(QuotaStatus $tokensPerProjectPerHour)
  {
    $this->tokensPerProjectPerHour = $tokensPerProjectPerHour;
  }
  /**
   * @return QuotaStatus
   */
  public function getTokensPerProjectPerHour()
  {
    return $this->tokensPerProjectPerHour;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PropertyQuota::class, 'Google_Service_AnalyticsData_PropertyQuota');

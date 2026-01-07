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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1Metrics extends \Google\Collection
{
  protected $collection_key = 'scoreMetrics';
  protected $challengeMetricsType = GoogleCloudRecaptchaenterpriseV1ChallengeMetrics::class;
  protected $challengeMetricsDataType = 'array';
  /**
   * Output only. Identifier. The name of the metrics, in the format
   * `projects/{project}/keys/{key}/metrics`.
   *
   * @var string
   */
  public $name;
  protected $scoreMetricsType = GoogleCloudRecaptchaenterpriseV1ScoreMetrics::class;
  protected $scoreMetricsDataType = 'array';
  /**
   * Inclusive start time aligned to a day in the America/Los_Angeles (Pacific)
   * timezone.
   *
   * @var string
   */
  public $startTime;

  /**
   * Metrics are continuous and in order by dates, and in the granularity of
   * day. Only challenge-based keys (CHECKBOX, INVISIBLE) have challenge-based
   * data.
   *
   * @param GoogleCloudRecaptchaenterpriseV1ChallengeMetrics[] $challengeMetrics
   */
  public function setChallengeMetrics($challengeMetrics)
  {
    $this->challengeMetrics = $challengeMetrics;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1ChallengeMetrics[]
   */
  public function getChallengeMetrics()
  {
    return $this->challengeMetrics;
  }
  /**
   * Output only. Identifier. The name of the metrics, in the format
   * `projects/{project}/keys/{key}/metrics`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Metrics are continuous and in order by dates, and in the granularity of
   * day. All Key types should have score-based data.
   *
   * @param GoogleCloudRecaptchaenterpriseV1ScoreMetrics[] $scoreMetrics
   */
  public function setScoreMetrics($scoreMetrics)
  {
    $this->scoreMetrics = $scoreMetrics;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1ScoreMetrics[]
   */
  public function getScoreMetrics()
  {
    return $this->scoreMetrics;
  }
  /**
   * Inclusive start time aligned to a day in the America/Los_Angeles (Pacific)
   * timezone.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1Metrics::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1Metrics');

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

namespace Google\Service\Compute;

class OutlierDetection extends \Google\Model
{
  protected $baseEjectionTimeType = Duration::class;
  protected $baseEjectionTimeDataType = '';
  /**
   * Number of consecutive errors before a backend endpoint is ejected from the
   * load balancing pool. When the backend endpoint is accessed over HTTP, a 5xx
   * return code qualifies as an error. Defaults to 5.
   *
   * @var int
   */
  public $consecutiveErrors;
  /**
   * The number of consecutive gateway failures (502, 503, 504 status or
   * connection errors that are mapped to one of those status codes) before a
   * consecutive gateway failure ejection occurs. Defaults to 3.
   *
   * @var int
   */
  public $consecutiveGatewayFailure;
  /**
   * The percentage chance that a backend endpoint will be ejected when an
   * outlier status is detected through consecutive 5xx. This setting can be
   * used to disable ejection or to ramp it up slowly. Defaults to 0.
   *
   * @var int
   */
  public $enforcingConsecutiveErrors;
  /**
   * The percentage chance that a backend endpoint will be ejected when an
   * outlier status is detected through consecutive gateway failures. This
   * setting can be used to disable ejection or to ramp it up slowly. Defaults
   * to 100.
   *
   * @var int
   */
  public $enforcingConsecutiveGatewayFailure;
  /**
   * The percentage chance that a backend endpoint will be ejected when an
   * outlier status is detected through success rate statistics. This setting
   * can be used to disable ejection or to ramp it up slowly. Defaults to 100.
   *
   * Not supported when the backend service uses Serverless NEG.
   *
   * @var int
   */
  public $enforcingSuccessRate;
  protected $intervalType = Duration::class;
  protected $intervalDataType = '';
  /**
   * Maximum percentage of backend endpoints in the load balancing pool for the
   * backend service that can be ejected if the ejection conditions are met.
   * Defaults to 50%.
   *
   * @var int
   */
  public $maxEjectionPercent;
  /**
   * The number of backend endpoints in the load balancing pool that must have
   * enough request volume to detect success rate outliers. If the number of
   * backend endpoints is fewer than this setting, outlier detection via success
   * rate statistics is not performed for any backend endpoint in the load
   * balancing pool. Defaults to 5.
   *
   * Not supported when the backend service uses Serverless NEG.
   *
   * @var int
   */
  public $successRateMinimumHosts;
  /**
   * The minimum number of total requests that must be collected in one interval
   * (as defined by the interval duration above) to include this backend
   * endpoint in success rate based outlier detection. If the volume is lower
   * than this setting, outlier detection via success rate statistics is not
   * performed for that backend endpoint. Defaults to 100.
   *
   * Not supported when the backend service uses Serverless NEG.
   *
   * @var int
   */
  public $successRateRequestVolume;
  /**
   * This factor is used to determine the ejection threshold for success rate
   * outlier ejection. The ejection threshold is the difference between the mean
   * success rate, and the product of this factor and the standard deviation of
   * the mean success rate: mean - (stdev * successRateStdevFactor). This factor
   * is divided by a thousand to get a double. That is, if the desired factor is
   * 1.9, the runtime value should be 1900. Defaults to 1900.
   *
   * Not supported when the backend service uses Serverless NEG.
   *
   * @var int
   */
  public $successRateStdevFactor;

  /**
   * The base time that a backend endpoint is ejected for. Defaults to 30000ms
   * or 30s.
   *
   * After a backend endpoint is returned back to the load balancing pool, it
   * can be ejected again in another ejection analysis. Thus, the total ejection
   * time is equal to the base ejection time multiplied by the number of times
   * the backend endpoint has been ejected. Defaults to 30000ms or 30s.
   *
   * @param Duration $baseEjectionTime
   */
  public function setBaseEjectionTime(Duration $baseEjectionTime)
  {
    $this->baseEjectionTime = $baseEjectionTime;
  }
  /**
   * @return Duration
   */
  public function getBaseEjectionTime()
  {
    return $this->baseEjectionTime;
  }
  /**
   * Number of consecutive errors before a backend endpoint is ejected from the
   * load balancing pool. When the backend endpoint is accessed over HTTP, a 5xx
   * return code qualifies as an error. Defaults to 5.
   *
   * @param int $consecutiveErrors
   */
  public function setConsecutiveErrors($consecutiveErrors)
  {
    $this->consecutiveErrors = $consecutiveErrors;
  }
  /**
   * @return int
   */
  public function getConsecutiveErrors()
  {
    return $this->consecutiveErrors;
  }
  /**
   * The number of consecutive gateway failures (502, 503, 504 status or
   * connection errors that are mapped to one of those status codes) before a
   * consecutive gateway failure ejection occurs. Defaults to 3.
   *
   * @param int $consecutiveGatewayFailure
   */
  public function setConsecutiveGatewayFailure($consecutiveGatewayFailure)
  {
    $this->consecutiveGatewayFailure = $consecutiveGatewayFailure;
  }
  /**
   * @return int
   */
  public function getConsecutiveGatewayFailure()
  {
    return $this->consecutiveGatewayFailure;
  }
  /**
   * The percentage chance that a backend endpoint will be ejected when an
   * outlier status is detected through consecutive 5xx. This setting can be
   * used to disable ejection or to ramp it up slowly. Defaults to 0.
   *
   * @param int $enforcingConsecutiveErrors
   */
  public function setEnforcingConsecutiveErrors($enforcingConsecutiveErrors)
  {
    $this->enforcingConsecutiveErrors = $enforcingConsecutiveErrors;
  }
  /**
   * @return int
   */
  public function getEnforcingConsecutiveErrors()
  {
    return $this->enforcingConsecutiveErrors;
  }
  /**
   * The percentage chance that a backend endpoint will be ejected when an
   * outlier status is detected through consecutive gateway failures. This
   * setting can be used to disable ejection or to ramp it up slowly. Defaults
   * to 100.
   *
   * @param int $enforcingConsecutiveGatewayFailure
   */
  public function setEnforcingConsecutiveGatewayFailure($enforcingConsecutiveGatewayFailure)
  {
    $this->enforcingConsecutiveGatewayFailure = $enforcingConsecutiveGatewayFailure;
  }
  /**
   * @return int
   */
  public function getEnforcingConsecutiveGatewayFailure()
  {
    return $this->enforcingConsecutiveGatewayFailure;
  }
  /**
   * The percentage chance that a backend endpoint will be ejected when an
   * outlier status is detected through success rate statistics. This setting
   * can be used to disable ejection or to ramp it up slowly. Defaults to 100.
   *
   * Not supported when the backend service uses Serverless NEG.
   *
   * @param int $enforcingSuccessRate
   */
  public function setEnforcingSuccessRate($enforcingSuccessRate)
  {
    $this->enforcingSuccessRate = $enforcingSuccessRate;
  }
  /**
   * @return int
   */
  public function getEnforcingSuccessRate()
  {
    return $this->enforcingSuccessRate;
  }
  /**
   * Time interval between ejection analysis sweeps. This can result in both new
   * ejections and backend endpoints being returned to service. The interval is
   * equal to the number of seconds as defined in
   * outlierDetection.interval.seconds plus the number of nanoseconds as defined
   * in outlierDetection.interval.nanos. Defaults to 1 second.
   *
   * @param Duration $interval
   */
  public function setInterval(Duration $interval)
  {
    $this->interval = $interval;
  }
  /**
   * @return Duration
   */
  public function getInterval()
  {
    return $this->interval;
  }
  /**
   * Maximum percentage of backend endpoints in the load balancing pool for the
   * backend service that can be ejected if the ejection conditions are met.
   * Defaults to 50%.
   *
   * @param int $maxEjectionPercent
   */
  public function setMaxEjectionPercent($maxEjectionPercent)
  {
    $this->maxEjectionPercent = $maxEjectionPercent;
  }
  /**
   * @return int
   */
  public function getMaxEjectionPercent()
  {
    return $this->maxEjectionPercent;
  }
  /**
   * The number of backend endpoints in the load balancing pool that must have
   * enough request volume to detect success rate outliers. If the number of
   * backend endpoints is fewer than this setting, outlier detection via success
   * rate statistics is not performed for any backend endpoint in the load
   * balancing pool. Defaults to 5.
   *
   * Not supported when the backend service uses Serverless NEG.
   *
   * @param int $successRateMinimumHosts
   */
  public function setSuccessRateMinimumHosts($successRateMinimumHosts)
  {
    $this->successRateMinimumHosts = $successRateMinimumHosts;
  }
  /**
   * @return int
   */
  public function getSuccessRateMinimumHosts()
  {
    return $this->successRateMinimumHosts;
  }
  /**
   * The minimum number of total requests that must be collected in one interval
   * (as defined by the interval duration above) to include this backend
   * endpoint in success rate based outlier detection. If the volume is lower
   * than this setting, outlier detection via success rate statistics is not
   * performed for that backend endpoint. Defaults to 100.
   *
   * Not supported when the backend service uses Serverless NEG.
   *
   * @param int $successRateRequestVolume
   */
  public function setSuccessRateRequestVolume($successRateRequestVolume)
  {
    $this->successRateRequestVolume = $successRateRequestVolume;
  }
  /**
   * @return int
   */
  public function getSuccessRateRequestVolume()
  {
    return $this->successRateRequestVolume;
  }
  /**
   * This factor is used to determine the ejection threshold for success rate
   * outlier ejection. The ejection threshold is the difference between the mean
   * success rate, and the product of this factor and the standard deviation of
   * the mean success rate: mean - (stdev * successRateStdevFactor). This factor
   * is divided by a thousand to get a double. That is, if the desired factor is
   * 1.9, the runtime value should be 1900. Defaults to 1900.
   *
   * Not supported when the backend service uses Serverless NEG.
   *
   * @param int $successRateStdevFactor
   */
  public function setSuccessRateStdevFactor($successRateStdevFactor)
  {
    $this->successRateStdevFactor = $successRateStdevFactor;
  }
  /**
   * @return int
   */
  public function getSuccessRateStdevFactor()
  {
    return $this->successRateStdevFactor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OutlierDetection::class, 'Google_Service_Compute_OutlierDetection');

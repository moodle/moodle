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

namespace Google\Service\FirebaseAppHosting;

class RunConfig extends \Google\Model
{
  /**
   * Optional. Maximum number of requests that each Cloud Run instance can
   * receive. By default, each instance can receive Cloud Run's default of up to
   * 80 requests at the same time. Concurrency can be set to any integer value
   * up to 1000.
   *
   * @var int
   */
  public $concurrency;
  /**
   * Optional. Number of CPUs used for each serving instance. By default, cpu
   * defaults to the Cloud Run's default of 1.0. CPU can be set to value 1, 2,
   * 4, 6, or 8 CPUs, and for less than 1 CPU, a value from 0.08 to less than
   * 1.00, in increments of 0.01. If you set a value of less than 1 CPU, you
   * must set concurrency to 1, and CPU will only be allocated during request
   * processing. Increasing CPUs limit may require increase in memory limits: -
   * 4 CPUs: at least 2 GiB - 6 CPUs: at least 4 GiB - 8 CPUs: at least 4 GiB
   *
   * @var float
   */
  public $cpu;
  /**
   * Optional. Number of Cloud Run instances to maintain at maximum for each
   * revision. By default, each Cloud Run [`service`](https://cloud.google.com/r
   * un/docs/reference/rest/v2/projects.locations.services#resource:-service)
   * scales out to Cloud Run's default of a maximum of 100 instances. The
   * maximum max_instances limit is based on your quota. See
   * https://cloud.google.com/run/docs/configuring/max-instances#limits.
   *
   * @var int
   */
  public $maxInstances;
  /**
   * Optional. Amount of memory allocated for each serving instance in MiB. By
   * default, memory defaults to the Cloud Run's default where each instance is
   * allocated 512 MiB of memory. Memory can be set to any integer value between
   * 128 to 32768. Increasing memory limit may require increase in CPUs limits:
   * - Over 4 GiB: at least 2 CPUs - Over 8 GiB: at least 4 CPUs - Over 16 GiB:
   * at least 6 CPUs - Over 24 GiB: at least 8 CPUs
   *
   * @var int
   */
  public $memoryMib;
  /**
   * Optional. Number of Cloud Run instances to maintain at minimum for each
   * Cloud Run Service. By default, there are no minimum. Even if the service
   * splits traffic across multiple revisions, the total number of instances for
   * a service will be capped at this value.
   *
   * @var int
   */
  public $minInstances;

  /**
   * Optional. Maximum number of requests that each Cloud Run instance can
   * receive. By default, each instance can receive Cloud Run's default of up to
   * 80 requests at the same time. Concurrency can be set to any integer value
   * up to 1000.
   *
   * @param int $concurrency
   */
  public function setConcurrency($concurrency)
  {
    $this->concurrency = $concurrency;
  }
  /**
   * @return int
   */
  public function getConcurrency()
  {
    return $this->concurrency;
  }
  /**
   * Optional. Number of CPUs used for each serving instance. By default, cpu
   * defaults to the Cloud Run's default of 1.0. CPU can be set to value 1, 2,
   * 4, 6, or 8 CPUs, and for less than 1 CPU, a value from 0.08 to less than
   * 1.00, in increments of 0.01. If you set a value of less than 1 CPU, you
   * must set concurrency to 1, and CPU will only be allocated during request
   * processing. Increasing CPUs limit may require increase in memory limits: -
   * 4 CPUs: at least 2 GiB - 6 CPUs: at least 4 GiB - 8 CPUs: at least 4 GiB
   *
   * @param float $cpu
   */
  public function setCpu($cpu)
  {
    $this->cpu = $cpu;
  }
  /**
   * @return float
   */
  public function getCpu()
  {
    return $this->cpu;
  }
  /**
   * Optional. Number of Cloud Run instances to maintain at maximum for each
   * revision. By default, each Cloud Run [`service`](https://cloud.google.com/r
   * un/docs/reference/rest/v2/projects.locations.services#resource:-service)
   * scales out to Cloud Run's default of a maximum of 100 instances. The
   * maximum max_instances limit is based on your quota. See
   * https://cloud.google.com/run/docs/configuring/max-instances#limits.
   *
   * @param int $maxInstances
   */
  public function setMaxInstances($maxInstances)
  {
    $this->maxInstances = $maxInstances;
  }
  /**
   * @return int
   */
  public function getMaxInstances()
  {
    return $this->maxInstances;
  }
  /**
   * Optional. Amount of memory allocated for each serving instance in MiB. By
   * default, memory defaults to the Cloud Run's default where each instance is
   * allocated 512 MiB of memory. Memory can be set to any integer value between
   * 128 to 32768. Increasing memory limit may require increase in CPUs limits:
   * - Over 4 GiB: at least 2 CPUs - Over 8 GiB: at least 4 CPUs - Over 16 GiB:
   * at least 6 CPUs - Over 24 GiB: at least 8 CPUs
   *
   * @param int $memoryMib
   */
  public function setMemoryMib($memoryMib)
  {
    $this->memoryMib = $memoryMib;
  }
  /**
   * @return int
   */
  public function getMemoryMib()
  {
    return $this->memoryMib;
  }
  /**
   * Optional. Number of Cloud Run instances to maintain at minimum for each
   * Cloud Run Service. By default, there are no minimum. Even if the service
   * splits traffic across multiple revisions, the total number of instances for
   * a service will be capped at this value.
   *
   * @param int $minInstances
   */
  public function setMinInstances($minInstances)
  {
    $this->minInstances = $minInstances;
  }
  /**
   * @return int
   */
  public function getMinInstances()
  {
    return $this->minInstances;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RunConfig::class, 'Google_Service_FirebaseAppHosting_RunConfig');

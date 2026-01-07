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

namespace Google\Service\Appengine;

class Instance extends \Google\Model
{
  public const AVAILABILITY_UNSPECIFIED = 'UNSPECIFIED';
  public const AVAILABILITY_RESIDENT = 'RESIDENT';
  public const AVAILABILITY_DYNAMIC = 'DYNAMIC';
  /**
   * There is no liveness health check for the instance. Only applicable for
   * instances in App Engine standard environment.
   */
  public const VM_LIVENESS_LIVENESS_STATE_UNSPECIFIED = 'LIVENESS_STATE_UNSPECIFIED';
  /**
   * The health checking system is aware of the instance but its health is not
   * known at the moment.
   */
  public const VM_LIVENESS_UNKNOWN = 'UNKNOWN';
  /**
   * The instance is reachable i.e. a connection to the application health
   * checking endpoint can be established, and conforms to the requirements
   * defined by the health check.
   */
  public const VM_LIVENESS_HEALTHY = 'HEALTHY';
  /**
   * The instance is reachable, but does not conform to the requirements defined
   * by the health check.
   */
  public const VM_LIVENESS_UNHEALTHY = 'UNHEALTHY';
  /**
   * The instance is being drained. The existing connections to the instance
   * have time to complete, but the new ones are being refused.
   */
  public const VM_LIVENESS_DRAINING = 'DRAINING';
  /**
   * The instance is unreachable i.e. a connection to the application health
   * checking endpoint cannot be established, or the server does not respond
   * within the specified timeout.
   */
  public const VM_LIVENESS_TIMEOUT = 'TIMEOUT';
  /**
   * Output only. App Engine release this instance is running on.
   *
   * @var string
   */
  public $appEngineRelease;
  /**
   * Output only. Availability of the instance.
   *
   * @var string
   */
  public $availability;
  /**
   * Output only. Average latency (ms) over the last minute.
   *
   * @var int
   */
  public $averageLatency;
  /**
   * Output only. Number of errors since this instance was started.
   *
   * @var int
   */
  public $errors;
  /**
   * Output only. Relative name of the instance within the version. Example:
   * instance-1.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Total memory in use (bytes).
   *
   * @var string
   */
  public $memoryUsage;
  /**
   * Output only. Full path to the Instance resource in the API. Example:
   * apps/myapp/services/default/versions/v1/instances/instance-1.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Average queries per second (QPS) over the last minute.
   *
   * @var float
   */
  public $qps;
  /**
   * Output only. Number of requests since this instance was started.
   *
   * @var int
   */
  public $requests;
  /**
   * Output only. Time that this instance was started.@OutputOnly
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. Whether this instance is in debug mode. Only applicable for
   * instances in App Engine flexible environment.
   *
   * @var bool
   */
  public $vmDebugEnabled;
  /**
   * Output only. Virtual machine ID of this instance. Only applicable for
   * instances in App Engine flexible environment.
   *
   * @var string
   */
  public $vmId;
  /**
   * Output only. The IP address of this instance. Only applicable for instances
   * in App Engine flexible environment.
   *
   * @var string
   */
  public $vmIp;
  /**
   * Output only. The liveness health check of this instance. Only applicable
   * for instances in App Engine flexible environment.
   *
   * @var string
   */
  public $vmLiveness;
  /**
   * Output only. Name of the virtual machine where this instance lives. Only
   * applicable for instances in App Engine flexible environment.
   *
   * @var string
   */
  public $vmName;
  /**
   * Output only. Status of the virtual machine where this instance lives. Only
   * applicable for instances in App Engine flexible environment.
   *
   * @var string
   */
  public $vmStatus;
  /**
   * Output only. Zone where the virtual machine is located. Only applicable for
   * instances in App Engine flexible environment.
   *
   * @var string
   */
  public $vmZoneName;

  /**
   * Output only. App Engine release this instance is running on.
   *
   * @param string $appEngineRelease
   */
  public function setAppEngineRelease($appEngineRelease)
  {
    $this->appEngineRelease = $appEngineRelease;
  }
  /**
   * @return string
   */
  public function getAppEngineRelease()
  {
    return $this->appEngineRelease;
  }
  /**
   * Output only. Availability of the instance.
   *
   * Accepted values: UNSPECIFIED, RESIDENT, DYNAMIC
   *
   * @param self::AVAILABILITY_* $availability
   */
  public function setAvailability($availability)
  {
    $this->availability = $availability;
  }
  /**
   * @return self::AVAILABILITY_*
   */
  public function getAvailability()
  {
    return $this->availability;
  }
  /**
   * Output only. Average latency (ms) over the last minute.
   *
   * @param int $averageLatency
   */
  public function setAverageLatency($averageLatency)
  {
    $this->averageLatency = $averageLatency;
  }
  /**
   * @return int
   */
  public function getAverageLatency()
  {
    return $this->averageLatency;
  }
  /**
   * Output only. Number of errors since this instance was started.
   *
   * @param int $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return int
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. Relative name of the instance within the version. Example:
   * instance-1.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Total memory in use (bytes).
   *
   * @param string $memoryUsage
   */
  public function setMemoryUsage($memoryUsage)
  {
    $this->memoryUsage = $memoryUsage;
  }
  /**
   * @return string
   */
  public function getMemoryUsage()
  {
    return $this->memoryUsage;
  }
  /**
   * Output only. Full path to the Instance resource in the API. Example:
   * apps/myapp/services/default/versions/v1/instances/instance-1.
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
   * Output only. Average queries per second (QPS) over the last minute.
   *
   * @param float $qps
   */
  public function setQps($qps)
  {
    $this->qps = $qps;
  }
  /**
   * @return float
   */
  public function getQps()
  {
    return $this->qps;
  }
  /**
   * Output only. Number of requests since this instance was started.
   *
   * @param int $requests
   */
  public function setRequests($requests)
  {
    $this->requests = $requests;
  }
  /**
   * @return int
   */
  public function getRequests()
  {
    return $this->requests;
  }
  /**
   * Output only. Time that this instance was started.@OutputOnly
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
  /**
   * Output only. Whether this instance is in debug mode. Only applicable for
   * instances in App Engine flexible environment.
   *
   * @param bool $vmDebugEnabled
   */
  public function setVmDebugEnabled($vmDebugEnabled)
  {
    $this->vmDebugEnabled = $vmDebugEnabled;
  }
  /**
   * @return bool
   */
  public function getVmDebugEnabled()
  {
    return $this->vmDebugEnabled;
  }
  /**
   * Output only. Virtual machine ID of this instance. Only applicable for
   * instances in App Engine flexible environment.
   *
   * @param string $vmId
   */
  public function setVmId($vmId)
  {
    $this->vmId = $vmId;
  }
  /**
   * @return string
   */
  public function getVmId()
  {
    return $this->vmId;
  }
  /**
   * Output only. The IP address of this instance. Only applicable for instances
   * in App Engine flexible environment.
   *
   * @param string $vmIp
   */
  public function setVmIp($vmIp)
  {
    $this->vmIp = $vmIp;
  }
  /**
   * @return string
   */
  public function getVmIp()
  {
    return $this->vmIp;
  }
  /**
   * Output only. The liveness health check of this instance. Only applicable
   * for instances in App Engine flexible environment.
   *
   * Accepted values: LIVENESS_STATE_UNSPECIFIED, UNKNOWN, HEALTHY, UNHEALTHY,
   * DRAINING, TIMEOUT
   *
   * @param self::VM_LIVENESS_* $vmLiveness
   */
  public function setVmLiveness($vmLiveness)
  {
    $this->vmLiveness = $vmLiveness;
  }
  /**
   * @return self::VM_LIVENESS_*
   */
  public function getVmLiveness()
  {
    return $this->vmLiveness;
  }
  /**
   * Output only. Name of the virtual machine where this instance lives. Only
   * applicable for instances in App Engine flexible environment.
   *
   * @param string $vmName
   */
  public function setVmName($vmName)
  {
    $this->vmName = $vmName;
  }
  /**
   * @return string
   */
  public function getVmName()
  {
    return $this->vmName;
  }
  /**
   * Output only. Status of the virtual machine where this instance lives. Only
   * applicable for instances in App Engine flexible environment.
   *
   * @param string $vmStatus
   */
  public function setVmStatus($vmStatus)
  {
    $this->vmStatus = $vmStatus;
  }
  /**
   * @return string
   */
  public function getVmStatus()
  {
    return $this->vmStatus;
  }
  /**
   * Output only. Zone where the virtual machine is located. Only applicable for
   * instances in App Engine flexible environment.
   *
   * @param string $vmZoneName
   */
  public function setVmZoneName($vmZoneName)
  {
    $this->vmZoneName = $vmZoneName;
  }
  /**
   * @return string
   */
  public function getVmZoneName()
  {
    return $this->vmZoneName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instance::class, 'Google_Service_Appengine_Instance');

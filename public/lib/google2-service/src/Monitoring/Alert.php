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

namespace Google\Service\Monitoring;

class Alert extends \Google\Model
{
  /**
   * The alert state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The alert is open.
   */
  public const STATE_OPEN = 'OPEN';
  /**
   * The alert is closed.
   */
  public const STATE_CLOSED = 'CLOSED';
  /**
   * The time when the alert was closed.
   *
   * @var string
   */
  public $closeTime;
  protected $logType = LogMetadata::class;
  protected $logDataType = '';
  protected $metadataType = MonitoredResourceMetadata::class;
  protected $metadataDataType = '';
  protected $metricType = Metric::class;
  protected $metricDataType = '';
  /**
   * Identifier. The name of the alert.The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/alerts/[ALERT_ID] The [ALERT_ID] is a
   * system-assigned unique identifier for the alert.
   *
   * @var string
   */
  public $name;
  /**
   * The time when the alert was opened.
   *
   * @var string
   */
  public $openTime;
  protected $policyType = PolicySnapshot::class;
  protected $policyDataType = '';
  protected $resourceType = MonitoredResource::class;
  protected $resourceDataType = '';
  /**
   * Output only. The current state of the alert.
   *
   * @var string
   */
  public $state;

  /**
   * The time when the alert was closed.
   *
   * @param string $closeTime
   */
  public function setCloseTime($closeTime)
  {
    $this->closeTime = $closeTime;
  }
  /**
   * @return string
   */
  public function getCloseTime()
  {
    return $this->closeTime;
  }
  /**
   * The log information associated with the alert. This field is only populated
   * for log-based alerts.
   *
   * @param LogMetadata $log
   */
  public function setLog(LogMetadata $log)
  {
    $this->log = $log;
  }
  /**
   * @return LogMetadata
   */
  public function getLog()
  {
    return $this->log;
  }
  /**
   * The metadata of the monitored resource.
   *
   * @param MonitoredResourceMetadata $metadata
   */
  public function setMetadata(MonitoredResourceMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return MonitoredResourceMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The metric type and any metric labels preserved from the incident's
   * generating condition.
   *
   * @param Metric $metric
   */
  public function setMetric(Metric $metric)
  {
    $this->metric = $metric;
  }
  /**
   * @return Metric
   */
  public function getMetric()
  {
    return $this->metric;
  }
  /**
   * Identifier. The name of the alert.The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/alerts/[ALERT_ID] The [ALERT_ID] is a
   * system-assigned unique identifier for the alert.
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
   * The time when the alert was opened.
   *
   * @param string $openTime
   */
  public function setOpenTime($openTime)
  {
    $this->openTime = $openTime;
  }
  /**
   * @return string
   */
  public function getOpenTime()
  {
    return $this->openTime;
  }
  /**
   * The snapshot of the alert policy that generated this alert.
   *
   * @param PolicySnapshot $policy
   */
  public function setPolicy(PolicySnapshot $policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return PolicySnapshot
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * The monitored resource type and any monitored resource labels preserved
   * from the incident's generating condition.
   *
   * @param MonitoredResource $resource
   */
  public function setResource(MonitoredResource $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return MonitoredResource
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Output only. The current state of the alert.
   *
   * Accepted values: STATE_UNSPECIFIED, OPEN, CLOSED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Alert::class, 'Google_Service_Monitoring_Alert');

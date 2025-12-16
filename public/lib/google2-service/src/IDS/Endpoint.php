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

namespace Google\Service\IDS;

class Endpoint extends \Google\Collection
{
  /**
   * Not set.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Informational alerts.
   */
  public const SEVERITY_INFORMATIONAL = 'INFORMATIONAL';
  /**
   * Low severity alerts.
   */
  public const SEVERITY_LOW = 'LOW';
  /**
   * Medium severity alerts.
   */
  public const SEVERITY_MEDIUM = 'MEDIUM';
  /**
   * High severity alerts.
   */
  public const SEVERITY_HIGH = 'HIGH';
  /**
   * Critical severity alerts.
   */
  public const SEVERITY_CRITICAL = 'CRITICAL';
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Active and ready for traffic.
   */
  public const STATE_READY = 'READY';
  /**
   * Being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  protected $collection_key = 'threatExceptions';
  /**
   * Output only. The create time timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * User-provided description of the endpoint
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The fully qualified URL of the endpoint's ILB Forwarding Rule.
   *
   * @var string
   */
  public $endpointForwardingRule;
  /**
   * Output only. The IP address of the IDS Endpoint's ILB.
   *
   * @var string
   */
  public $endpointIp;
  /**
   * The labels of the endpoint.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The name of the endpoint.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The fully qualified URL of the network to which the IDS Endpoint
   * is attached.
   *
   * @var string
   */
  public $network;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Required. Lowest threat severity that this endpoint will alert on.
   *
   * @var string
   */
  public $severity;
  /**
   * Output only. Current state of the endpoint.
   *
   * @var string
   */
  public $state;
  /**
   * List of threat IDs to be excepted from generating alerts.
   *
   * @var string[]
   */
  public $threatExceptions;
  /**
   * Whether the endpoint should report traffic logs in addition to threat logs.
   *
   * @var bool
   */
  public $trafficLogs;
  /**
   * Output only. The update time timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The create time timestamp.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * User-provided description of the endpoint
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. The fully qualified URL of the endpoint's ILB Forwarding Rule.
   *
   * @param string $endpointForwardingRule
   */
  public function setEndpointForwardingRule($endpointForwardingRule)
  {
    $this->endpointForwardingRule = $endpointForwardingRule;
  }
  /**
   * @return string
   */
  public function getEndpointForwardingRule()
  {
    return $this->endpointForwardingRule;
  }
  /**
   * Output only. The IP address of the IDS Endpoint's ILB.
   *
   * @param string $endpointIp
   */
  public function setEndpointIp($endpointIp)
  {
    $this->endpointIp = $endpointIp;
  }
  /**
   * @return string
   */
  public function getEndpointIp()
  {
    return $this->endpointIp;
  }
  /**
   * The labels of the endpoint.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The name of the endpoint.
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
   * Required. The fully qualified URL of the network to which the IDS Endpoint
   * is attached.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Required. Lowest threat severity that this endpoint will alert on.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, INFORMATIONAL, LOW, MEDIUM, HIGH,
   * CRITICAL
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * Output only. Current state of the endpoint.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, DELETING, UPDATING
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
  /**
   * List of threat IDs to be excepted from generating alerts.
   *
   * @param string[] $threatExceptions
   */
  public function setThreatExceptions($threatExceptions)
  {
    $this->threatExceptions = $threatExceptions;
  }
  /**
   * @return string[]
   */
  public function getThreatExceptions()
  {
    return $this->threatExceptions;
  }
  /**
   * Whether the endpoint should report traffic logs in addition to threat logs.
   *
   * @param bool $trafficLogs
   */
  public function setTrafficLogs($trafficLogs)
  {
    $this->trafficLogs = $trafficLogs;
  }
  /**
   * @return bool
   */
  public function getTrafficLogs()
  {
    return $this->trafficLogs;
  }
  /**
   * Output only. The update time timestamp.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Endpoint::class, 'Google_Service_IDS_Endpoint');

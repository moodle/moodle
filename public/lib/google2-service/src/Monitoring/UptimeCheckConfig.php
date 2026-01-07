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

class UptimeCheckConfig extends \Google\Collection
{
  /**
   * The default checker type. Currently converted to STATIC_IP_CHECKERS on
   * creation, the default conversion behavior may change in the future.
   */
  public const CHECKER_TYPE_CHECKER_TYPE_UNSPECIFIED = 'CHECKER_TYPE_UNSPECIFIED';
  /**
   * STATIC_IP_CHECKERS are used for uptime checks that perform egress across
   * the public internet. STATIC_IP_CHECKERS use the static IP addresses
   * returned by ListUptimeCheckIps.
   */
  public const CHECKER_TYPE_STATIC_IP_CHECKERS = 'STATIC_IP_CHECKERS';
  /**
   * VPC_CHECKERS are used for uptime checks that perform egress using Service
   * Directory and private network access. When using VPC_CHECKERS, the
   * monitored resource type must be servicedirectory_service.
   */
  public const CHECKER_TYPE_VPC_CHECKERS = 'VPC_CHECKERS';
  protected $collection_key = 'selectedRegions';
  /**
   * The type of checkers to use to execute the Uptime check.
   *
   * @var string
   */
  public $checkerType;
  protected $contentMatchersType = ContentMatcher::class;
  protected $contentMatchersDataType = 'array';
  /**
   * Whether the check is disabled or not.
   *
   * @var bool
   */
  public $disabled;
  /**
   * A human-friendly name for the Uptime check configuration. The display name
   * should be unique within a Cloud Monitoring Workspace in order to make it
   * easier to identify; however, uniqueness is not enforced. Required.
   *
   * @var string
   */
  public $displayName;
  protected $httpCheckType = HttpCheck::class;
  protected $httpCheckDataType = '';
  protected $internalCheckersType = InternalChecker::class;
  protected $internalCheckersDataType = 'array';
  /**
   * If this is true, then checks are made only from the 'internal_checkers'. If
   * it is false, then checks are made only from the 'selected_regions'. It is
   * an error to provide 'selected_regions' when is_internal is true, or to
   * provide 'internal_checkers' when is_internal is false.
   *
   * @deprecated
   * @var bool
   */
  public $isInternal;
  /**
   * To specify whether to log the results of failed probes to Cloud Logging.
   *
   * @var bool
   */
  public $logCheckFailures;
  protected $monitoredResourceType = MonitoredResource::class;
  protected $monitoredResourceDataType = '';
  /**
   * Identifier. A unique resource name for this Uptime check configuration. The
   * format is:
   * projects/[PROJECT_ID_OR_NUMBER]/uptimeCheckConfigs/[UPTIME_CHECK_ID]
   * [PROJECT_ID_OR_NUMBER] is the Workspace host project associated with the
   * Uptime check.This field should be omitted when creating the Uptime check
   * configuration; on create, the resource name is assigned by the server and
   * included in the response.
   *
   * @var string
   */
  public $name;
  /**
   * How often, in seconds, the Uptime check is performed. Currently, the only
   * supported values are 60s (1 minute), 300s (5 minutes), 600s (10 minutes),
   * and 900s (15 minutes). Optional, defaults to 60s.
   *
   * @var string
   */
  public $period;
  protected $resourceGroupType = ResourceGroup::class;
  protected $resourceGroupDataType = '';
  /**
   * The list of regions from which the check will be run. Some regions contain
   * one location, and others contain more than one. If this field is specified,
   * enough regions must be provided to include a minimum of 3 locations. Not
   * specifying this field will result in Uptime checks running from all
   * available regions.
   *
   * @var string[]
   */
  public $selectedRegions;
  protected $syntheticMonitorType = SyntheticMonitorTarget::class;
  protected $syntheticMonitorDataType = '';
  protected $tcpCheckType = TcpCheck::class;
  protected $tcpCheckDataType = '';
  /**
   * The maximum amount of time to wait for the request to complete (must be
   * between 1 and 60 seconds). Required.
   *
   * @var string
   */
  public $timeout;
  /**
   * User-supplied key/value data to be used for organizing and identifying the
   * UptimeCheckConfig objects.The field can contain up to 64 entries. Each key
   * and value is limited to 63 Unicode characters or 128 bytes, whichever is
   * smaller. Labels and values can contain only lowercase letters, numerals,
   * underscores, and dashes. Keys must begin with a letter.
   *
   * @var string[]
   */
  public $userLabels;

  /**
   * The type of checkers to use to execute the Uptime check.
   *
   * Accepted values: CHECKER_TYPE_UNSPECIFIED, STATIC_IP_CHECKERS, VPC_CHECKERS
   *
   * @param self::CHECKER_TYPE_* $checkerType
   */
  public function setCheckerType($checkerType)
  {
    $this->checkerType = $checkerType;
  }
  /**
   * @return self::CHECKER_TYPE_*
   */
  public function getCheckerType()
  {
    return $this->checkerType;
  }
  /**
   * The content that is expected to appear in the data returned by the target
   * server against which the check is run. Currently, only the first entry in
   * the content_matchers list is supported, and additional entries will be
   * ignored. This field is optional and should only be specified if a content
   * match is required as part of the/ Uptime check.
   *
   * @param ContentMatcher[] $contentMatchers
   */
  public function setContentMatchers($contentMatchers)
  {
    $this->contentMatchers = $contentMatchers;
  }
  /**
   * @return ContentMatcher[]
   */
  public function getContentMatchers()
  {
    return $this->contentMatchers;
  }
  /**
   * Whether the check is disabled or not.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * A human-friendly name for the Uptime check configuration. The display name
   * should be unique within a Cloud Monitoring Workspace in order to make it
   * easier to identify; however, uniqueness is not enforced. Required.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Contains information needed to make an HTTP or HTTPS check.
   *
   * @param HttpCheck $httpCheck
   */
  public function setHttpCheck(HttpCheck $httpCheck)
  {
    $this->httpCheck = $httpCheck;
  }
  /**
   * @return HttpCheck
   */
  public function getHttpCheck()
  {
    return $this->httpCheck;
  }
  /**
   * The internal checkers that this check will egress from. If is_internal is
   * true and this list is empty, the check will egress from all the
   * InternalCheckers configured for the project that owns this
   * UptimeCheckConfig.
   *
   * @deprecated
   * @param InternalChecker[] $internalCheckers
   */
  public function setInternalCheckers($internalCheckers)
  {
    $this->internalCheckers = $internalCheckers;
  }
  /**
   * @deprecated
   * @return InternalChecker[]
   */
  public function getInternalCheckers()
  {
    return $this->internalCheckers;
  }
  /**
   * If this is true, then checks are made only from the 'internal_checkers'. If
   * it is false, then checks are made only from the 'selected_regions'. It is
   * an error to provide 'selected_regions' when is_internal is true, or to
   * provide 'internal_checkers' when is_internal is false.
   *
   * @deprecated
   * @param bool $isInternal
   */
  public function setIsInternal($isInternal)
  {
    $this->isInternal = $isInternal;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getIsInternal()
  {
    return $this->isInternal;
  }
  /**
   * To specify whether to log the results of failed probes to Cloud Logging.
   *
   * @param bool $logCheckFailures
   */
  public function setLogCheckFailures($logCheckFailures)
  {
    $this->logCheckFailures = $logCheckFailures;
  }
  /**
   * @return bool
   */
  public function getLogCheckFailures()
  {
    return $this->logCheckFailures;
  }
  /**
   * The monitored resource (https://cloud.google.com/monitoring/api/resources)
   * associated with the configuration. The following monitored resource types
   * are valid for this field: uptime_url, gce_instance, gae_app,
   * aws_ec2_instance, aws_elb_load_balancer k8s_service
   * servicedirectory_service cloud_run_revision
   *
   * @param MonitoredResource $monitoredResource
   */
  public function setMonitoredResource(MonitoredResource $monitoredResource)
  {
    $this->monitoredResource = $monitoredResource;
  }
  /**
   * @return MonitoredResource
   */
  public function getMonitoredResource()
  {
    return $this->monitoredResource;
  }
  /**
   * Identifier. A unique resource name for this Uptime check configuration. The
   * format is:
   * projects/[PROJECT_ID_OR_NUMBER]/uptimeCheckConfigs/[UPTIME_CHECK_ID]
   * [PROJECT_ID_OR_NUMBER] is the Workspace host project associated with the
   * Uptime check.This field should be omitted when creating the Uptime check
   * configuration; on create, the resource name is assigned by the server and
   * included in the response.
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
   * How often, in seconds, the Uptime check is performed. Currently, the only
   * supported values are 60s (1 minute), 300s (5 minutes), 600s (10 minutes),
   * and 900s (15 minutes). Optional, defaults to 60s.
   *
   * @param string $period
   */
  public function setPeriod($period)
  {
    $this->period = $period;
  }
  /**
   * @return string
   */
  public function getPeriod()
  {
    return $this->period;
  }
  /**
   * The group resource associated with the configuration.
   *
   * @param ResourceGroup $resourceGroup
   */
  public function setResourceGroup(ResourceGroup $resourceGroup)
  {
    $this->resourceGroup = $resourceGroup;
  }
  /**
   * @return ResourceGroup
   */
  public function getResourceGroup()
  {
    return $this->resourceGroup;
  }
  /**
   * The list of regions from which the check will be run. Some regions contain
   * one location, and others contain more than one. If this field is specified,
   * enough regions must be provided to include a minimum of 3 locations. Not
   * specifying this field will result in Uptime checks running from all
   * available regions.
   *
   * @param string[] $selectedRegions
   */
  public function setSelectedRegions($selectedRegions)
  {
    $this->selectedRegions = $selectedRegions;
  }
  /**
   * @return string[]
   */
  public function getSelectedRegions()
  {
    return $this->selectedRegions;
  }
  /**
   * Specifies a Synthetic Monitor to invoke.
   *
   * @param SyntheticMonitorTarget $syntheticMonitor
   */
  public function setSyntheticMonitor(SyntheticMonitorTarget $syntheticMonitor)
  {
    $this->syntheticMonitor = $syntheticMonitor;
  }
  /**
   * @return SyntheticMonitorTarget
   */
  public function getSyntheticMonitor()
  {
    return $this->syntheticMonitor;
  }
  /**
   * Contains information needed to make a TCP check.
   *
   * @param TcpCheck $tcpCheck
   */
  public function setTcpCheck(TcpCheck $tcpCheck)
  {
    $this->tcpCheck = $tcpCheck;
  }
  /**
   * @return TcpCheck
   */
  public function getTcpCheck()
  {
    return $this->tcpCheck;
  }
  /**
   * The maximum amount of time to wait for the request to complete (must be
   * between 1 and 60 seconds). Required.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
  /**
   * User-supplied key/value data to be used for organizing and identifying the
   * UptimeCheckConfig objects.The field can contain up to 64 entries. Each key
   * and value is limited to 63 Unicode characters or 128 bytes, whichever is
   * smaller. Labels and values can contain only lowercase letters, numerals,
   * underscores, and dashes. Keys must begin with a letter.
   *
   * @param string[] $userLabels
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UptimeCheckConfig::class, 'Google_Service_Monitoring_UptimeCheckConfig');

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

namespace Google\Service\ServiceUsage;

class GoogleApiServiceusageV1ServiceConfig extends \Google\Collection
{
  protected $collection_key = 'monitoredResources';
  protected $apisType = Api::class;
  protected $apisDataType = 'array';
  protected $authenticationType = Authentication::class;
  protected $authenticationDataType = '';
  protected $documentationType = Documentation::class;
  protected $documentationDataType = '';
  protected $endpointsType = Endpoint::class;
  protected $endpointsDataType = 'array';
  protected $monitoredResourcesType = MonitoredResourceDescriptor::class;
  protected $monitoredResourcesDataType = 'array';
  protected $monitoringType = Monitoring::class;
  protected $monitoringDataType = '';
  /**
   * The DNS address at which this service is available. An example DNS address
   * would be: `calendar.googleapis.com`.
   *
   * @var string
   */
  public $name;
  protected $quotaType = Quota::class;
  protected $quotaDataType = '';
  /**
   * The product title for this service.
   *
   * @var string
   */
  public $title;
  protected $usageType = Usage::class;
  protected $usageDataType = '';

  /**
   * A list of API interfaces exported by this service. Contains only the names,
   * versions, and method names of the interfaces.
   *
   * @param Api[] $apis
   */
  public function setApis($apis)
  {
    $this->apis = $apis;
  }
  /**
   * @return Api[]
   */
  public function getApis()
  {
    return $this->apis;
  }
  /**
   * Auth configuration. Contains only the OAuth rules.
   *
   * @param Authentication $authentication
   */
  public function setAuthentication(Authentication $authentication)
  {
    $this->authentication = $authentication;
  }
  /**
   * @return Authentication
   */
  public function getAuthentication()
  {
    return $this->authentication;
  }
  /**
   * Additional API documentation. Contains only the summary and the
   * documentation URL.
   *
   * @param Documentation $documentation
   */
  public function setDocumentation(Documentation $documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return Documentation
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Configuration for network endpoints. Contains only the names and aliases of
   * the endpoints.
   *
   * @param Endpoint[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return Endpoint[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * Defines the monitored resources used by this service. This is required by
   * the Service.monitoring and Service.logging configurations.
   *
   * @param MonitoredResourceDescriptor[] $monitoredResources
   */
  public function setMonitoredResources($monitoredResources)
  {
    $this->monitoredResources = $monitoredResources;
  }
  /**
   * @return MonitoredResourceDescriptor[]
   */
  public function getMonitoredResources()
  {
    return $this->monitoredResources;
  }
  /**
   * Monitoring configuration. This should not include the
   * 'producer_destinations' field.
   *
   * @param Monitoring $monitoring
   */
  public function setMonitoring(Monitoring $monitoring)
  {
    $this->monitoring = $monitoring;
  }
  /**
   * @return Monitoring
   */
  public function getMonitoring()
  {
    return $this->monitoring;
  }
  /**
   * The DNS address at which this service is available. An example DNS address
   * would be: `calendar.googleapis.com`.
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
   * Quota configuration.
   *
   * @param Quota $quota
   */
  public function setQuota(Quota $quota)
  {
    $this->quota = $quota;
  }
  /**
   * @return Quota
   */
  public function getQuota()
  {
    return $this->quota;
  }
  /**
   * The product title for this service.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Configuration controlling usage of this service.
   *
   * @param Usage $usage
   */
  public function setUsage(Usage $usage)
  {
    $this->usage = $usage;
  }
  /**
   * @return Usage
   */
  public function getUsage()
  {
    return $this->usage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleApiServiceusageV1ServiceConfig::class, 'Google_Service_ServiceUsage_GoogleApiServiceusageV1ServiceConfig');

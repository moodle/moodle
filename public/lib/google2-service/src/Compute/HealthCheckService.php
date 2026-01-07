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

class HealthCheckService extends \Google\Collection
{
  /**
   * If any backend's health check reports UNHEALTHY, then UNHEALTHY is the
   * HealthState of the entire health check service. If all backend's are
   * healthy, the HealthState of the health check service isHEALTHY.
   */
  public const HEALTH_STATUS_AGGREGATION_POLICY_AND = 'AND';
  /**
   * An EndpointHealth message is returned for each backend in the health check
   * service.
   */
  public const HEALTH_STATUS_AGGREGATION_POLICY_NO_AGGREGATION = 'NO_AGGREGATION';
  protected $collection_key = 'notificationEndpoints';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a HealthCheckService. An up-to-date fingerprint must be provided
   * in order to patch/update the HealthCheckService; Otherwise, the request
   * will fail with error 412 conditionNotMet. To see the latest fingerprint,
   * make a get() request to retrieve the HealthCheckService.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * A list of URLs to the HealthCheck resources. Must have at least one
   * HealthCheck, and not more than 10 for regionalHealthCheckService, and not
   * more than 1 for globalHealthCheckService.HealthCheck resources must
   * haveportSpecification=USE_SERVING_PORT orportSpecification=USE_FIXED_PORT.
   * For regional HealthCheckService, theHealthCheck must be regional and in the
   * same region. For global HealthCheckService,HealthCheck must be global. Mix
   * of regional and globalHealthChecks is not supported. Multiple
   * regionalHealthChecks must belong to the same region. RegionalHealthChecks
   * must belong to the same region as zones ofNetworkEndpointGroups. For
   * globalHealthCheckService using globalINTERNET_IP_PORT
   * NetworkEndpointGroups, the global HealthChecks must specify sourceRegions,
   * and HealthChecks that specify sourceRegions can only be used with global
   * INTERNET_IP_PORTNetworkEndpointGroups.
   *
   * @var string[]
   */
  public $healthChecks;
  /**
   * Optional. Policy for how the results from multiple health checks for the
   * same endpoint are aggregated. Defaults to NO_AGGREGATION if unspecified.
   * - NO_AGGREGATION. An EndpointHealth message is    returned for each  pair
   * in the health check    service.    - AND. If any health check of an
   * endpoint reportsUNHEALTHY, then UNHEALTHY is theHealthState of the
   * endpoint. If all health checks reportHEALTHY, the HealthState of the
   * endpoint isHEALTHY.
   *
   * . This is only allowed with regional HealthCheckService.
   *
   * @var string
   */
  public $healthStatusAggregationPolicy;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output only] Type of the resource.
   * Alwayscompute#healthCheckServicefor health check services.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the resource. The name must be 1-63 characters long, and comply
   * with RFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
   *
   * @var string
   */
  public $name;
  /**
   * A list of URLs to the NetworkEndpointGroup resources. Must not have more
   * than 100.  For regionalHealthCheckService, NEGs must be in zones in the
   * region of the HealthCheckService. For globalHealthCheckServices, the
   * NetworkEndpointGroups must be global INTERNET_IP_PORT.
   *
   * @var string[]
   */
  public $networkEndpointGroups;
  /**
   * A list of URLs to the NotificationEndpoint resources. Must not have more
   * than 10.  A list of endpoints for receiving notifications of change in
   * health status. For regionalHealthCheckService,NotificationEndpoint must be
   * regional and in the same region. For global
   * HealthCheckService,NotificationEndpoint must be global.
   *
   * @var string[]
   */
  public $notificationEndpoints;
  /**
   * Output only. [Output Only] URL of the region where the health check service
   * resides. This field is not applicable to global health check services. You
   * must specify this field as part of the HTTP request URL. It is not settable
   * as a field in the request body.
   *
   * @var string
   */
  public $region;
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;

  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
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
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a HealthCheckService. An up-to-date fingerprint must be provided
   * in order to patch/update the HealthCheckService; Otherwise, the request
   * will fail with error 412 conditionNotMet. To see the latest fingerprint,
   * make a get() request to retrieve the HealthCheckService.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * A list of URLs to the HealthCheck resources. Must have at least one
   * HealthCheck, and not more than 10 for regionalHealthCheckService, and not
   * more than 1 for globalHealthCheckService.HealthCheck resources must
   * haveportSpecification=USE_SERVING_PORT orportSpecification=USE_FIXED_PORT.
   * For regional HealthCheckService, theHealthCheck must be regional and in the
   * same region. For global HealthCheckService,HealthCheck must be global. Mix
   * of regional and globalHealthChecks is not supported. Multiple
   * regionalHealthChecks must belong to the same region. RegionalHealthChecks
   * must belong to the same region as zones ofNetworkEndpointGroups. For
   * globalHealthCheckService using globalINTERNET_IP_PORT
   * NetworkEndpointGroups, the global HealthChecks must specify sourceRegions,
   * and HealthChecks that specify sourceRegions can only be used with global
   * INTERNET_IP_PORTNetworkEndpointGroups.
   *
   * @param string[] $healthChecks
   */
  public function setHealthChecks($healthChecks)
  {
    $this->healthChecks = $healthChecks;
  }
  /**
   * @return string[]
   */
  public function getHealthChecks()
  {
    return $this->healthChecks;
  }
  /**
   * Optional. Policy for how the results from multiple health checks for the
   * same endpoint are aggregated. Defaults to NO_AGGREGATION if unspecified.
   * - NO_AGGREGATION. An EndpointHealth message is    returned for each  pair
   * in the health check    service.    - AND. If any health check of an
   * endpoint reportsUNHEALTHY, then UNHEALTHY is theHealthState of the
   * endpoint. If all health checks reportHEALTHY, the HealthState of the
   * endpoint isHEALTHY.
   *
   * . This is only allowed with regional HealthCheckService.
   *
   * Accepted values: AND, NO_AGGREGATION
   *
   * @param self::HEALTH_STATUS_AGGREGATION_POLICY_* $healthStatusAggregationPolicy
   */
  public function setHealthStatusAggregationPolicy($healthStatusAggregationPolicy)
  {
    $this->healthStatusAggregationPolicy = $healthStatusAggregationPolicy;
  }
  /**
   * @return self::HEALTH_STATUS_AGGREGATION_POLICY_*
   */
  public function getHealthStatusAggregationPolicy()
  {
    return $this->healthStatusAggregationPolicy;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
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
   * Output only. [Output only] Type of the resource.
   * Alwayscompute#healthCheckServicefor health check services.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of the resource. The name must be 1-63 characters long, and comply
   * with RFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
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
   * A list of URLs to the NetworkEndpointGroup resources. Must not have more
   * than 100.  For regionalHealthCheckService, NEGs must be in zones in the
   * region of the HealthCheckService. For globalHealthCheckServices, the
   * NetworkEndpointGroups must be global INTERNET_IP_PORT.
   *
   * @param string[] $networkEndpointGroups
   */
  public function setNetworkEndpointGroups($networkEndpointGroups)
  {
    $this->networkEndpointGroups = $networkEndpointGroups;
  }
  /**
   * @return string[]
   */
  public function getNetworkEndpointGroups()
  {
    return $this->networkEndpointGroups;
  }
  /**
   * A list of URLs to the NotificationEndpoint resources. Must not have more
   * than 10.  A list of endpoints for receiving notifications of change in
   * health status. For regionalHealthCheckService,NotificationEndpoint must be
   * regional and in the same region. For global
   * HealthCheckService,NotificationEndpoint must be global.
   *
   * @param string[] $notificationEndpoints
   */
  public function setNotificationEndpoints($notificationEndpoints)
  {
    $this->notificationEndpoints = $notificationEndpoints;
  }
  /**
   * @return string[]
   */
  public function getNotificationEndpoints()
  {
    return $this->notificationEndpoints;
  }
  /**
   * Output only. [Output Only] URL of the region where the health check service
   * resides. This field is not applicable to global health check services. You
   * must specify this field as part of the HTTP request URL. It is not settable
   * as a field in the request body.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HealthCheckService::class, 'Google_Service_Compute_HealthCheckService');

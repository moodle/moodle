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

namespace Google\Service\Reports;

class Activity extends \Google\Collection
{
  protected $collection_key = 'resourceDetails';
  protected $actorType = ActivityActor::class;
  protected $actorDataType = '';
  /**
   * ETag of the entry.
   *
   * @var string
   */
  public $etag;
  protected $eventsType = ActivityEvents::class;
  protected $eventsDataType = 'array';
  protected $idType = ActivityId::class;
  protected $idDataType = '';
  /**
   * IP address of the user doing the action. This is the Internet Protocol (IP)
   * address of the user when logging into Google Workspace, which may or may
   * not reflect the user's physical location. For example, the IP address can
   * be the user's proxy server's address or a virtual private network (VPN)
   * address. The API supports IPv4 and IPv6.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * The type of API resource. For an activity report, the value is
   * `audit#activity`.
   *
   * @var string
   */
  public $kind;
  protected $networkInfoType = ActivityNetworkInfo::class;
  protected $networkInfoDataType = '';
  /**
   * This is the domain that is affected by the report's event. For example
   * domain of Admin console or the Drive application's document owner.
   *
   * @var string
   */
  public $ownerDomain;
  protected $resourceDetailsType = ResourceDetails::class;
  protected $resourceDetailsDataType = 'array';

  /**
   * User doing the action.
   *
   * @param ActivityActor $actor
   */
  public function setActor(ActivityActor $actor)
  {
    $this->actor = $actor;
  }
  /**
   * @return ActivityActor
   */
  public function getActor()
  {
    return $this->actor;
  }
  /**
   * ETag of the entry.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Activity events in the report.
   *
   * @param ActivityEvents[] $events
   */
  public function setEvents($events)
  {
    $this->events = $events;
  }
  /**
   * @return ActivityEvents[]
   */
  public function getEvents()
  {
    return $this->events;
  }
  /**
   * Unique identifier for each activity record.
   *
   * @param ActivityId $id
   */
  public function setId(ActivityId $id)
  {
    $this->id = $id;
  }
  /**
   * @return ActivityId
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * IP address of the user doing the action. This is the Internet Protocol (IP)
   * address of the user when logging into Google Workspace, which may or may
   * not reflect the user's physical location. For example, the IP address can
   * be the user's proxy server's address or a virtual private network (VPN)
   * address. The API supports IPv4 and IPv6.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * The type of API resource. For an activity report, the value is
   * `audit#activity`.
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
   * Network information of the user doing the action.
   *
   * @param ActivityNetworkInfo $networkInfo
   */
  public function setNetworkInfo(ActivityNetworkInfo $networkInfo)
  {
    $this->networkInfo = $networkInfo;
  }
  /**
   * @return ActivityNetworkInfo
   */
  public function getNetworkInfo()
  {
    return $this->networkInfo;
  }
  /**
   * This is the domain that is affected by the report's event. For example
   * domain of Admin console or the Drive application's document owner.
   *
   * @param string $ownerDomain
   */
  public function setOwnerDomain($ownerDomain)
  {
    $this->ownerDomain = $ownerDomain;
  }
  /**
   * @return string
   */
  public function getOwnerDomain()
  {
    return $this->ownerDomain;
  }
  /**
   * Details of the resource on which the action was performed.
   *
   * @param ResourceDetails[] $resourceDetails
   */
  public function setResourceDetails($resourceDetails)
  {
    $this->resourceDetails = $resourceDetails;
  }
  /**
   * @return ResourceDetails[]
   */
  public function getResourceDetails()
  {
    return $this->resourceDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Activity::class, 'Google_Service_Reports_Activity');

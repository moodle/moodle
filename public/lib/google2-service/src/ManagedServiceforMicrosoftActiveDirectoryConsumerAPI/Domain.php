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

namespace Google\Service\ManagedServiceforMicrosoftActiveDirectoryConsumerAPI;

class Domain extends \Google\Collection
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The domain is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The domain has been created and is fully usable.
   */
  public const STATE_READY = 'READY';
  /**
   * The domain's configuration is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The domain is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The domain is being repaired and may be unusable. Details can be found in
   * the `status_message` field.
   */
  public const STATE_REPAIRING = 'REPAIRING';
  /**
   * The domain is undergoing maintenance.
   */
  public const STATE_PERFORMING_MAINTENANCE = 'PERFORMING_MAINTENANCE';
  /**
   * The domain is not serving requests.
   */
  public const STATE_UNAVAILABLE = 'UNAVAILABLE';
  protected $collection_key = 'trusts';
  /**
   * Optional. The name of delegated administrator account used to perform
   * Active Directory operations. If not specified, `setupadmin` will be used.
   *
   * @var string
   */
  public $admin;
  /**
   * Optional. Configuration for audit logs. True if audit logs are enabled,
   * else false. Default is audit logs disabled.
   *
   * @var bool
   */
  public $auditLogsEnabled;
  /**
   * Optional. The full names of the Google Compute Engine
   * [networks](/compute/docs/networks-and-firewalls#networks) the domain
   * instance is connected to. Networks can be added using UpdateDomain. The
   * domain is only available on networks listed in `authorized_networks`. If
   * CIDR subnets overlap between networks, domain creation will fail.
   *
   * @var string[]
   */
  public $authorizedNetworks;
  /**
   * Output only. The time the instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The fully-qualified domain name of the exposed domain used by
   * clients to connect to the service. Similar to what would be chosen for an
   * Active Directory set up on an internal network.
   *
   * @var string
   */
  public $fqdn;
  /**
   * Optional. Resource labels that can contain user-provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. Locations where domain needs to be provisioned. The locations can
   * be specified according to https://cloud.google.com/compute/docs/regions-
   * zones, such as `us-west1` or `us-east4`. Each domain supports up to 4
   * locations, separated by commas. Each location will use a /26 block.
   *
   * @var string[]
   */
  public $locations;
  /**
   * Required. The unique name of the domain using the form:
   * `projects/{project_id}/locations/global/domains/{domain_name}`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The CIDR range of internal addresses that are reserved for this
   * domain. Reserved networks must be /24 or larger. Ranges must be unique and
   * non-overlapping with existing subnets in [Domain].[authorized_networks].
   *
   * @var string
   */
  public $reservedIpRange;
  /**
   * Output only. The current state of this domain.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Additional information about the current status of this
   * domain, if available.
   *
   * @var string
   */
  public $statusMessage;
  protected $trustsType = Trust::class;
  protected $trustsDataType = 'array';
  /**
   * Output only. The last update time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The name of delegated administrator account used to perform
   * Active Directory operations. If not specified, `setupadmin` will be used.
   *
   * @param string $admin
   */
  public function setAdmin($admin)
  {
    $this->admin = $admin;
  }
  /**
   * @return string
   */
  public function getAdmin()
  {
    return $this->admin;
  }
  /**
   * Optional. Configuration for audit logs. True if audit logs are enabled,
   * else false. Default is audit logs disabled.
   *
   * @param bool $auditLogsEnabled
   */
  public function setAuditLogsEnabled($auditLogsEnabled)
  {
    $this->auditLogsEnabled = $auditLogsEnabled;
  }
  /**
   * @return bool
   */
  public function getAuditLogsEnabled()
  {
    return $this->auditLogsEnabled;
  }
  /**
   * Optional. The full names of the Google Compute Engine
   * [networks](/compute/docs/networks-and-firewalls#networks) the domain
   * instance is connected to. Networks can be added using UpdateDomain. The
   * domain is only available on networks listed in `authorized_networks`. If
   * CIDR subnets overlap between networks, domain creation will fail.
   *
   * @param string[] $authorizedNetworks
   */
  public function setAuthorizedNetworks($authorizedNetworks)
  {
    $this->authorizedNetworks = $authorizedNetworks;
  }
  /**
   * @return string[]
   */
  public function getAuthorizedNetworks()
  {
    return $this->authorizedNetworks;
  }
  /**
   * Output only. The time the instance was created.
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
   * Output only. The fully-qualified domain name of the exposed domain used by
   * clients to connect to the service. Similar to what would be chosen for an
   * Active Directory set up on an internal network.
   *
   * @param string $fqdn
   */
  public function setFqdn($fqdn)
  {
    $this->fqdn = $fqdn;
  }
  /**
   * @return string
   */
  public function getFqdn()
  {
    return $this->fqdn;
  }
  /**
   * Optional. Resource labels that can contain user-provided metadata.
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
   * Required. Locations where domain needs to be provisioned. The locations can
   * be specified according to https://cloud.google.com/compute/docs/regions-
   * zones, such as `us-west1` or `us-east4`. Each domain supports up to 4
   * locations, separated by commas. Each location will use a /26 block.
   *
   * @param string[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return string[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * Required. The unique name of the domain using the form:
   * `projects/{project_id}/locations/global/domains/{domain_name}`.
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
   * Required. The CIDR range of internal addresses that are reserved for this
   * domain. Reserved networks must be /24 or larger. Ranges must be unique and
   * non-overlapping with existing subnets in [Domain].[authorized_networks].
   *
   * @param string $reservedIpRange
   */
  public function setReservedIpRange($reservedIpRange)
  {
    $this->reservedIpRange = $reservedIpRange;
  }
  /**
   * @return string
   */
  public function getReservedIpRange()
  {
    return $this->reservedIpRange;
  }
  /**
   * Output only. The current state of this domain.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, UPDATING, DELETING,
   * REPAIRING, PERFORMING_MAINTENANCE, UNAVAILABLE
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
   * Output only. Additional information about the current status of this
   * domain, if available.
   *
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * Output only. The current trusts associated with the domain.
   *
   * @param Trust[] $trusts
   */
  public function setTrusts($trusts)
  {
    $this->trusts = $trusts;
  }
  /**
   * @return Trust[]
   */
  public function getTrusts()
  {
    return $this->trusts;
  }
  /**
   * Output only. The last update time.
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
class_alias(Domain::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_Domain');

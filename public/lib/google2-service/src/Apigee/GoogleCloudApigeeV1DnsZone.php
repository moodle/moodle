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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1DnsZone extends \Google\Model
{
  /**
   * Resource is in an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Resource is provisioned and ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resource is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Output only. The time that this resource was created on the server.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Description of the resource. String of at most 1024 characters
   * associated with this resource for the user's convenience.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The domain name for hosts in this private zone, for instance
   * "example.com.".
   *
   * @var string
   */
  public $domain;
  /**
   * Identifier. Unique name for the resource. Defined by the server Format:
   * "organizations/{organization}/dnsZones/{dns_zone}".
   *
   * @var string
   */
  public $name;
  protected $peeringConfigType = GoogleCloudApigeeV1DnsZonePeeringConfig::class;
  protected $peeringConfigDataType = '';
  /**
   * Output only. State of the DNS Peering. Values other than `ACTIVE` mean the
   * resource is not ready to use.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time that this resource was updated on the server.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time that this resource was created on the server.
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
   * Required. Description of the resource. String of at most 1024 characters
   * associated with this resource for the user's convenience.
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
   * Required. The domain name for hosts in this private zone, for instance
   * "example.com.".
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Identifier. Unique name for the resource. Defined by the server Format:
   * "organizations/{organization}/dnsZones/{dns_zone}".
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
   * DNS PEERING zone configuration.
   *
   * @param GoogleCloudApigeeV1DnsZonePeeringConfig $peeringConfig
   */
  public function setPeeringConfig(GoogleCloudApigeeV1DnsZonePeeringConfig $peeringConfig)
  {
    $this->peeringConfig = $peeringConfig;
  }
  /**
   * @return GoogleCloudApigeeV1DnsZonePeeringConfig
   */
  public function getPeeringConfig()
  {
    return $this->peeringConfig;
  }
  /**
   * Output only. State of the DNS Peering. Values other than `ACTIVE` mean the
   * resource is not ready to use.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, UPDATING
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
   * Output only. The time that this resource was updated on the server.
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
class_alias(GoogleCloudApigeeV1DnsZone::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DnsZone');

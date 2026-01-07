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

namespace Google\Service\BeyondCorp;

class GoogleCloudBeyondcorpSecuritygatewaysV1Application extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const SCHEMA_SCHEMA_UNSPECIFIED = 'SCHEMA_UNSPECIFIED';
  /**
   * Proxy which routes traffic to actual applications, like Netscaler Gateway.
   */
  public const SCHEMA_PROXY_GATEWAY = 'PROXY_GATEWAY';
  /**
   * Service Discovery API endpoint when Service Discovery is enabled in
   * Gateway.
   */
  public const SCHEMA_API_GATEWAY = 'API_GATEWAY';
  protected $collection_key = 'upstreams';
  /**
   * Output only. Timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. An arbitrary user-provided name for the application resource.
   * Cannot exceed 64 characters.
   *
   * @var string
   */
  public $displayName;
  protected $endpointMatchersType = GoogleCloudBeyondcorpSecuritygatewaysV1EndpointMatcher::class;
  protected $endpointMatchersDataType = 'array';
  /**
   * Identifier. Name of the resource.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Type of the external application.
   *
   * @var string
   */
  public $schema;
  /**
   * Output only. Timestamp when the resource was last modified.
   *
   * @var string
   */
  public $updateTime;
  protected $upstreamsType = GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstream::class;
  protected $upstreamsDataType = 'array';

  /**
   * Output only. Timestamp when the resource was created.
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
   * Optional. An arbitrary user-provided name for the application resource.
   * Cannot exceed 64 characters.
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
   * Optional. An array of conditions to match the application's network
   * endpoint. Each element in the array is an EndpointMatcher object, which
   * defines a specific combination of a hostname pattern and one or more ports.
   * The application is considered matched if at least one of the
   * EndpointMatcher conditions in this array is met (the conditions are
   * combined using OR logic). Each EndpointMatcher must contain a hostname
   * pattern, such as "example.com", and one or more port numbers specified as a
   * string, such as "443". Hostname and port number examples: "*.example.com",
   * "443" "example.com" and "22" "example.com" and "22,33"
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1EndpointMatcher[] $endpointMatchers
   */
  public function setEndpointMatchers($endpointMatchers)
  {
    $this->endpointMatchers = $endpointMatchers;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1EndpointMatcher[]
   */
  public function getEndpointMatchers()
  {
    return $this->endpointMatchers;
  }
  /**
   * Identifier. Name of the resource.
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
   * Optional. Type of the external application.
   *
   * Accepted values: SCHEMA_UNSPECIFIED, PROXY_GATEWAY, API_GATEWAY
   *
   * @param self::SCHEMA_* $schema
   */
  public function setSchema($schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return self::SCHEMA_*
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * Output only. Timestamp when the resource was last modified.
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
  /**
   * Optional. Which upstream resources to forward traffic to.
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstream[] $upstreams
   */
  public function setUpstreams($upstreams)
  {
    $this->upstreams = $upstreams;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstream[]
   */
  public function getUpstreams()
  {
    return $this->upstreams;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBeyondcorpSecuritygatewaysV1Application::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpSecuritygatewaysV1Application');

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

namespace Google\Service\CloudDomains;

class ResourceRecordSet extends \Google\Collection
{
  protected $collection_key = 'signatureRrdata';
  /**
   * For example, www.example.com.
   *
   * @var string
   */
  public $name;
  protected $routingPolicyType = RRSetRoutingPolicy::class;
  protected $routingPolicyDataType = '';
  /**
   * As defined in RFC 1035 (section 5) and RFC 1034 (section 3.6.1) -- see
   * examples.
   *
   * @var string[]
   */
  public $rrdata;
  /**
   * As defined in RFC 4034 (section 3.2).
   *
   * @var string[]
   */
  public $signatureRrdata;
  /**
   * Number of seconds that this `ResourceRecordSet` can be cached by resolvers.
   *
   * @var int
   */
  public $ttl;
  /**
   * The identifier of a supported record type. See the list of Supported DNS
   * record types.
   *
   * @var string
   */
  public $type;

  /**
   * For example, www.example.com.
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
   * Configures dynamic query responses based on either the geo location of the
   * querying user or a weighted round robin based routing policy. A valid
   * `ResourceRecordSet` contains only `rrdata` (for static resolution) or a
   * `routing_policy` (for dynamic resolution).
   *
   * @param RRSetRoutingPolicy $routingPolicy
   */
  public function setRoutingPolicy(RRSetRoutingPolicy $routingPolicy)
  {
    $this->routingPolicy = $routingPolicy;
  }
  /**
   * @return RRSetRoutingPolicy
   */
  public function getRoutingPolicy()
  {
    return $this->routingPolicy;
  }
  /**
   * As defined in RFC 1035 (section 5) and RFC 1034 (section 3.6.1) -- see
   * examples.
   *
   * @param string[] $rrdata
   */
  public function setRrdata($rrdata)
  {
    $this->rrdata = $rrdata;
  }
  /**
   * @return string[]
   */
  public function getRrdata()
  {
    return $this->rrdata;
  }
  /**
   * As defined in RFC 4034 (section 3.2).
   *
   * @param string[] $signatureRrdata
   */
  public function setSignatureRrdata($signatureRrdata)
  {
    $this->signatureRrdata = $signatureRrdata;
  }
  /**
   * @return string[]
   */
  public function getSignatureRrdata()
  {
    return $this->signatureRrdata;
  }
  /**
   * Number of seconds that this `ResourceRecordSet` can be cached by resolvers.
   *
   * @param int $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return int
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * The identifier of a supported record type. See the list of Supported DNS
   * record types.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceRecordSet::class, 'Google_Service_CloudDomains_ResourceRecordSet');

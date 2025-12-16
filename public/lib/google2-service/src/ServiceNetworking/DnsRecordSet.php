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

namespace Google\Service\ServiceNetworking;

class DnsRecordSet extends \Google\Collection
{
  protected $collection_key = 'data';
  /**
   * Required. As defined in RFC 1035 (section 5) and RFC 1034 (section 3.6.1)
   * for examples see https://cloud.google.com/dns/records/json-record.
   *
   * @var string[]
   */
  public $data;
  /**
   * Required. The DNS or domain name of the record set, e.g.
   * `test.example.com`. Cloud DNS requires that a DNS suffix ends with a
   * trailing dot.
   *
   * @var string
   */
  public $domain;
  /**
   * Required. The period of time for which this RecordSet can be cached by
   * resolvers.
   *
   * @var string
   */
  public $ttl;
  /**
   * Required. The identifier of a supported record type.
   *
   * @var string
   */
  public $type;

  /**
   * Required. As defined in RFC 1035 (section 5) and RFC 1034 (section 3.6.1)
   * for examples see https://cloud.google.com/dns/records/json-record.
   *
   * @param string[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Required. The DNS or domain name of the record set, e.g.
   * `test.example.com`. Cloud DNS requires that a DNS suffix ends with a
   * trailing dot.
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
   * Required. The period of time for which this RecordSet can be cached by
   * resolvers.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * Required. The identifier of a supported record type.
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
class_alias(DnsRecordSet::class, 'Google_Service_ServiceNetworking_DnsRecordSet');

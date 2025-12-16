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

namespace Google\Service\CertificateManager;

class CNAME extends \Google\Collection
{
  protected $collection_key = 'resolvedData';
  /**
   * Output only. The expected value of the CNAME record for the domain, equals
   * to `dns_resource_record.data` in the corresponding `DnsAuthorization`.
   *
   * @var string
   */
  public $expectedData;
  /**
   * Output only. The name of the CNAME record for the domain, equals to
   * `dns_resource_record.name` in the corresponding `DnsAuthorization`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The resolved CNAME chain. Empty list if the CNAME record for
   * `CNAME.name` is not found. Otherwise the first item is the value of the
   * CNAME record for `CNAME.name`. If the CNAME chain is longer, the second
   * item is the value of the CNAME record for the first item, and so on.
   *
   * @var string[]
   */
  public $resolvedData;

  /**
   * Output only. The expected value of the CNAME record for the domain, equals
   * to `dns_resource_record.data` in the corresponding `DnsAuthorization`.
   *
   * @param string $expectedData
   */
  public function setExpectedData($expectedData)
  {
    $this->expectedData = $expectedData;
  }
  /**
   * @return string
   */
  public function getExpectedData()
  {
    return $this->expectedData;
  }
  /**
   * Output only. The name of the CNAME record for the domain, equals to
   * `dns_resource_record.name` in the corresponding `DnsAuthorization`.
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
   * Output only. The resolved CNAME chain. Empty list if the CNAME record for
   * `CNAME.name` is not found. Otherwise the first item is the value of the
   * CNAME record for `CNAME.name`. If the CNAME chain is longer, the second
   * item is the value of the CNAME record for the first item, and so on.
   *
   * @param string[] $resolvedData
   */
  public function setResolvedData($resolvedData)
  {
    $this->resolvedData = $resolvedData;
  }
  /**
   * @return string[]
   */
  public function getResolvedData()
  {
    return $this->resolvedData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CNAME::class, 'Google_Service_CertificateManager_CNAME');

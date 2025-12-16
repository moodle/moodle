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

class DnsAuthorization extends \Google\Model
{
  /**
   * Type is unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * FIXED_RECORD DNS authorization uses DNS-01 validation method.
   */
  public const TYPE_FIXED_RECORD = 'FIXED_RECORD';
  /**
   * PER_PROJECT_RECORD DNS authorization allows for independent management of
   * Google-managed certificates with DNS authorization across multiple
   * projects.
   */
  public const TYPE_PER_PROJECT_RECORD = 'PER_PROJECT_RECORD';
  /**
   * Output only. The creation timestamp of a DnsAuthorization.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. One or more paragraphs of text description of a DnsAuthorization.
   *
   * @var string
   */
  public $description;
  protected $dnsResourceRecordType = DnsResourceRecord::class;
  protected $dnsResourceRecordDataType = '';
  /**
   * Required. Immutable. A domain that is being authorized. A DnsAuthorization
   * resource covers a single domain and its wildcard, e.g. authorization for
   * `example.com` can be used to issue certificates for `example.com` and
   * `*.example.com`.
   *
   * @var string
   */
  public $domain;
  /**
   * Optional. Set of labels associated with a DnsAuthorization.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. A user-defined name of the dns authorization. DnsAuthorization
   * names must be unique globally and match pattern
   * `projects/locations/dnsAuthorizations`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Immutable. Type of DnsAuthorization. If unset during resource
   * creation the following default will be used: - in location `global`:
   * FIXED_RECORD, - in other locations: PER_PROJECT_RECORD.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The last update timestamp of a DnsAuthorization.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The creation timestamp of a DnsAuthorization.
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
   * Optional. One or more paragraphs of text description of a DnsAuthorization.
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
   * Output only. DNS Resource Record that needs to be added to DNS
   * configuration.
   *
   * @param DnsResourceRecord $dnsResourceRecord
   */
  public function setDnsResourceRecord(DnsResourceRecord $dnsResourceRecord)
  {
    $this->dnsResourceRecord = $dnsResourceRecord;
  }
  /**
   * @return DnsResourceRecord
   */
  public function getDnsResourceRecord()
  {
    return $this->dnsResourceRecord;
  }
  /**
   * Required. Immutable. A domain that is being authorized. A DnsAuthorization
   * resource covers a single domain and its wildcard, e.g. authorization for
   * `example.com` can be used to issue certificates for `example.com` and
   * `*.example.com`.
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
   * Optional. Set of labels associated with a DnsAuthorization.
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
   * Identifier. A user-defined name of the dns authorization. DnsAuthorization
   * names must be unique globally and match pattern
   * `projects/locations/dnsAuthorizations`.
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
   * Optional. Immutable. Type of DnsAuthorization. If unset during resource
   * creation the following default will be used: - in location `global`:
   * FIXED_RECORD, - in other locations: PER_PROJECT_RECORD.
   *
   * Accepted values: TYPE_UNSPECIFIED, FIXED_RECORD, PER_PROJECT_RECORD
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. The last update timestamp of a DnsAuthorization.
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
class_alias(DnsAuthorization::class, 'Google_Service_CertificateManager_DnsAuthorization');

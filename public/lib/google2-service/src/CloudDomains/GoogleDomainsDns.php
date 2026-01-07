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

class GoogleDomainsDns extends \Google\Collection
{
  /**
   * DS state is unspecified.
   */
  public const DS_STATE_DS_STATE_UNSPECIFIED = 'DS_STATE_UNSPECIFIED';
  /**
   * DNSSEC is disabled for this domain. No DS records for this domain are
   * published in the parent DNS zone.
   */
  public const DS_STATE_DS_RECORDS_UNPUBLISHED = 'DS_RECORDS_UNPUBLISHED';
  /**
   * DNSSEC is enabled for this domain. Appropriate DS records for this domain
   * are published in the parent DNS zone. This option is valid only if the DNS
   * zone referenced in the `Registration`'s `dns_provider` field is already
   * DNSSEC-signed.
   */
  public const DS_STATE_DS_RECORDS_PUBLISHED = 'DS_RECORDS_PUBLISHED';
  protected $collection_key = 'nameServers';
  protected $dsRecordsType = DsRecord::class;
  protected $dsRecordsDataType = 'array';
  /**
   * Required. The state of DS records for this domain. Used to enable or
   * disable automatic DNSSEC.
   *
   * @var string
   */
  public $dsState;
  /**
   * Output only. A list of name servers that store the DNS zone for this
   * domain. Each name server is a domain name, with Unicode domain names
   * expressed in Punycode format. This field is automatically populated with
   * the name servers assigned to the Google Domains DNS zone.
   *
   * @var string[]
   */
  public $nameServers;

  /**
   * Output only. The list of DS records published for this domain. The list is
   * automatically populated when `ds_state` is `DS_RECORDS_PUBLISHED`,
   * otherwise it remains empty.
   *
   * @param DsRecord[] $dsRecords
   */
  public function setDsRecords($dsRecords)
  {
    $this->dsRecords = $dsRecords;
  }
  /**
   * @return DsRecord[]
   */
  public function getDsRecords()
  {
    return $this->dsRecords;
  }
  /**
   * Required. The state of DS records for this domain. Used to enable or
   * disable automatic DNSSEC.
   *
   * Accepted values: DS_STATE_UNSPECIFIED, DS_RECORDS_UNPUBLISHED,
   * DS_RECORDS_PUBLISHED
   *
   * @param self::DS_STATE_* $dsState
   */
  public function setDsState($dsState)
  {
    $this->dsState = $dsState;
  }
  /**
   * @return self::DS_STATE_*
   */
  public function getDsState()
  {
    return $this->dsState;
  }
  /**
   * Output only. A list of name servers that store the DNS zone for this
   * domain. Each name server is a domain name, with Unicode domain names
   * expressed in Punycode format. This field is automatically populated with
   * the name servers assigned to the Google Domains DNS zone.
   *
   * @param string[] $nameServers
   */
  public function setNameServers($nameServers)
  {
    $this->nameServers = $nameServers;
  }
  /**
   * @return string[]
   */
  public function getNameServers()
  {
    return $this->nameServers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDomainsDns::class, 'Google_Service_CloudDomains_GoogleDomainsDns');

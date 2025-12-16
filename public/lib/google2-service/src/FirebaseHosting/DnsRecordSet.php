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

namespace Google\Service\FirebaseHosting;

class DnsRecordSet extends \Google\Collection
{
  protected $collection_key = 'records';
  protected $checkErrorType = Status::class;
  protected $checkErrorDataType = '';
  /**
   * Output only. The domain name the record set pertains to.
   *
   * @var string
   */
  public $domainName;
  protected $recordsType = DnsRecord::class;
  protected $recordsDataType = 'array';

  /**
   * Output only. An error Hosting services encountered when querying your
   * domain name's DNS records. Note: Hosting ignores `NXDOMAIN` errors, as
   * those generally just mean that a domain name hasn't been set up yet.
   *
   * @param Status $checkError
   */
  public function setCheckError(Status $checkError)
  {
    $this->checkError = $checkError;
  }
  /**
   * @return Status
   */
  public function getCheckError()
  {
    return $this->checkError;
  }
  /**
   * Output only. The domain name the record set pertains to.
   *
   * @param string $domainName
   */
  public function setDomainName($domainName)
  {
    $this->domainName = $domainName;
  }
  /**
   * @return string
   */
  public function getDomainName()
  {
    return $this->domainName;
  }
  /**
   * Output only. Records on the domain.
   *
   * @param DnsRecord[] $records
   */
  public function setRecords($records)
  {
    $this->records = $records;
  }
  /**
   * @return DnsRecord[]
   */
  public function getRecords()
  {
    return $this->records;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DnsRecordSet::class, 'Google_Service_FirebaseHosting_DnsRecordSet');

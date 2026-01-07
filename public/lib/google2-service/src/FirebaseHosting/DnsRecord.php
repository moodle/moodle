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

class DnsRecord extends \Google\Model
{
  /**
   * No action necessary.
   */
  public const REQUIRED_ACTION_NONE = 'NONE';
  /**
   * Add this record to your DNS records.
   */
  public const REQUIRED_ACTION_ADD = 'ADD';
  /**
   * Remove this record from your DNS records.
   */
  public const REQUIRED_ACTION_REMOVE = 'REMOVE';
  /**
   * The record's type is unspecified. The message is invalid if this is
   * unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * An `A` record, as defined in [RFC
   * 1035](https://tools.ietf.org/html/rfc1035). A records determine which IPv4
   * addresses a domain name directs traffic towards.
   */
  public const TYPE_A = 'A';
  /**
   * A `CNAME` record, as defined in [RFC
   * 1035](https://tools.ietf.org/html/rfc1035). `CNAME` or Canonical Name
   * records map a domain name to a different, canonical domain name. If a
   * `CNAME` record is present, it should be the only record on the domain name.
   */
  public const TYPE_CNAME = 'CNAME';
  /**
   * A `TXT` record, as defined in [RFC
   * 1035](https://tools.ietf.org/html/rfc1035). `TXT` records hold arbitrary
   * text data on a domain name. Hosting uses `TXT` records to establish which
   * Firebase Project has permission to act on a domain name.
   */
  public const TYPE_TXT = 'TXT';
  /**
   * An AAAA record, as defined in [RFC
   * 3596](https://tools.ietf.org/html/rfc3596) AAAA records determine which
   * IPv6 addresses a domain name directs traffic towards.
   */
  public const TYPE_AAAA = 'AAAA';
  /**
   * A CAA record, as defined in [RFC
   * 6844](https://tools.ietf.org/html/rfc6844). CAA, or Certificate Authority
   * Authorization, records determine which Certificate Authorities (SSL
   * certificate minting organizations) are authorized to mint a certificate for
   * the domain name. Firebase Hosting uses `pki.goog` as its primary CA. CAA
   * records cascade. A CAA record on `foo.com` also applies to `bar.foo.com`
   * unless `bar.foo.com` has its own set of CAA records. CAA records are
   * optional. If a domain name and its parents have no CAA records, all CAs are
   * authorized to mint certificates on its behalf. In general, Hosting only
   * asks you to modify CAA records when doing so is required to unblock SSL
   * cert creation.
   */
  public const TYPE_CAA = 'CAA';
  /**
   * Output only. The domain name the record pertains to, e.g. `foo.bar.com.`.
   *
   * @var string
   */
  public $domainName;
  /**
   * Output only. The data of the record. The meaning of the value depends on
   * record type: - A and AAAA: IP addresses for the domain name. - CNAME:
   * Another domain to check for records. - TXT: Arbitrary text strings
   * associated with the domain name. Hosting uses TXT records to determine
   * which Firebase projects have permission to act on the domain name's behalf.
   * - CAA: The record's flags, tag, and value, e.g. `0 issue "pki.goog"`.
   *
   * @var string
   */
  public $rdata;
  /**
   * Output only. An enum that indicates the a required action for this record.
   *
   * @var string
   */
  public $requiredAction;
  /**
   * Output only. The record's type, which determines what data the record
   * contains.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The domain name the record pertains to, e.g. `foo.bar.com.`.
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
   * Output only. The data of the record. The meaning of the value depends on
   * record type: - A and AAAA: IP addresses for the domain name. - CNAME:
   * Another domain to check for records. - TXT: Arbitrary text strings
   * associated with the domain name. Hosting uses TXT records to determine
   * which Firebase projects have permission to act on the domain name's behalf.
   * - CAA: The record's flags, tag, and value, e.g. `0 issue "pki.goog"`.
   *
   * @param string $rdata
   */
  public function setRdata($rdata)
  {
    $this->rdata = $rdata;
  }
  /**
   * @return string
   */
  public function getRdata()
  {
    return $this->rdata;
  }
  /**
   * Output only. An enum that indicates the a required action for this record.
   *
   * Accepted values: NONE, ADD, REMOVE
   *
   * @param self::REQUIRED_ACTION_* $requiredAction
   */
  public function setRequiredAction($requiredAction)
  {
    $this->requiredAction = $requiredAction;
  }
  /**
   * @return self::REQUIRED_ACTION_*
   */
  public function getRequiredAction()
  {
    return $this->requiredAction;
  }
  /**
   * Output only. The record's type, which determines what data the record
   * contains.
   *
   * Accepted values: TYPE_UNSPECIFIED, A, CNAME, TXT, AAAA, CAA
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DnsRecord::class, 'Google_Service_FirebaseHosting_DnsRecord');

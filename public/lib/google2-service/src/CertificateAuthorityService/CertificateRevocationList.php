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

namespace Google\Service\CertificateAuthorityService;

class CertificateRevocationList extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The CertificateRevocationList is up to date.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The CertificateRevocationList is no longer current.
   */
  public const STATE_SUPERSEDED = 'SUPERSEDED';
  protected $collection_key = 'revokedCertificates';
  /**
   * Output only. The location where 'pem_crl' can be accessed.
   *
   * @var string
   */
  public $accessUrl;
  /**
   * Output only. The time at which this CertificateRevocationList was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Labels with user-defined metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name for this CertificateRevocationList in the
   * format `projects/locations/caPoolscertificateAuthorities/
   * certificateRevocationLists`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The PEM-encoded X.509 CRL.
   *
   * @var string
   */
  public $pemCrl;
  /**
   * Output only. The revision ID of this CertificateRevocationList. A new
   * revision is committed whenever a new CRL is published. The format is an
   * 8-character hexadecimal string.
   *
   * @var string
   */
  public $revisionId;
  protected $revokedCertificatesType = RevokedCertificate::class;
  protected $revokedCertificatesDataType = 'array';
  /**
   * Output only. The CRL sequence number that appears in pem_crl.
   *
   * @var string
   */
  public $sequenceNumber;
  /**
   * Output only. The State for this CertificateRevocationList.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time at which this CertificateRevocationList was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The location where 'pem_crl' can be accessed.
   *
   * @param string $accessUrl
   */
  public function setAccessUrl($accessUrl)
  {
    $this->accessUrl = $accessUrl;
  }
  /**
   * @return string
   */
  public function getAccessUrl()
  {
    return $this->accessUrl;
  }
  /**
   * Output only. The time at which this CertificateRevocationList was created.
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
   * Optional. Labels with user-defined metadata.
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
   * Identifier. The resource name for this CertificateRevocationList in the
   * format `projects/locations/caPoolscertificateAuthorities/
   * certificateRevocationLists`.
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
   * Output only. The PEM-encoded X.509 CRL.
   *
   * @param string $pemCrl
   */
  public function setPemCrl($pemCrl)
  {
    $this->pemCrl = $pemCrl;
  }
  /**
   * @return string
   */
  public function getPemCrl()
  {
    return $this->pemCrl;
  }
  /**
   * Output only. The revision ID of this CertificateRevocationList. A new
   * revision is committed whenever a new CRL is published. The format is an
   * 8-character hexadecimal string.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Output only. The revoked serial numbers that appear in pem_crl.
   *
   * @param RevokedCertificate[] $revokedCertificates
   */
  public function setRevokedCertificates($revokedCertificates)
  {
    $this->revokedCertificates = $revokedCertificates;
  }
  /**
   * @return RevokedCertificate[]
   */
  public function getRevokedCertificates()
  {
    return $this->revokedCertificates;
  }
  /**
   * Output only. The CRL sequence number that appears in pem_crl.
   *
   * @param string $sequenceNumber
   */
  public function setSequenceNumber($sequenceNumber)
  {
    $this->sequenceNumber = $sequenceNumber;
  }
  /**
   * @return string
   */
  public function getSequenceNumber()
  {
    return $this->sequenceNumber;
  }
  /**
   * Output only. The State for this CertificateRevocationList.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, SUPERSEDED
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
   * Output only. The time at which this CertificateRevocationList was updated.
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
class_alias(CertificateRevocationList::class, 'Google_Service_CertificateAuthorityService_CertificateRevocationList');

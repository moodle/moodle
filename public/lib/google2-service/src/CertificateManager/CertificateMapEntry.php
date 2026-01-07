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

class CertificateMapEntry extends \Google\Collection
{
  /**
   * A matcher has't been recognized.
   */
  public const MATCHER_MATCHER_UNSPECIFIED = 'MATCHER_UNSPECIFIED';
  /**
   * A primary certificate that is served when SNI wasn't specified in the
   * request or SNI couldn't be found in the map.
   */
  public const MATCHER_PRIMARY = 'PRIMARY';
  /**
   * The status is undefined.
   */
  public const STATE_SERVING_STATE_UNSPECIFIED = 'SERVING_STATE_UNSPECIFIED';
  /**
   * The configuration is serving.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Update is in progress. Some frontends may serve this configuration.
   */
  public const STATE_PENDING = 'PENDING';
  protected $collection_key = 'certificates';
  /**
   * Optional. A set of Certificates defines for the given `hostname`. There can
   * be defined up to four certificates in each Certificate Map Entry. Each
   * certificate must match pattern `projects/locations/certificates`.
   *
   * @var string[]
   */
  public $certificates;
  /**
   * Output only. The creation timestamp of a Certificate Map Entry.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. One or more paragraphs of text description of a certificate map
   * entry.
   *
   * @var string
   */
  public $description;
  /**
   * A Hostname (FQDN, e.g. `example.com`) or a wildcard hostname expression
   * (`*.example.com`) for a set of hostnames with common suffix. Used as Server
   * Name Indication (SNI) for selecting a proper certificate.
   *
   * @var string
   */
  public $hostname;
  /**
   * Optional. Set of labels associated with a Certificate Map Entry.
   *
   * @var string[]
   */
  public $labels;
  /**
   * A predefined matcher for particular cases, other than SNI selection.
   *
   * @var string
   */
  public $matcher;
  /**
   * Identifier. A user-defined name of the Certificate Map Entry. Certificate
   * Map Entry names must be unique globally and match pattern
   * `projects/locations/certificateMaps/certificateMapEntries`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. A serving state of this Certificate Map Entry.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The update timestamp of a Certificate Map Entry.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. A set of Certificates defines for the given `hostname`. There can
   * be defined up to four certificates in each Certificate Map Entry. Each
   * certificate must match pattern `projects/locations/certificates`.
   *
   * @param string[] $certificates
   */
  public function setCertificates($certificates)
  {
    $this->certificates = $certificates;
  }
  /**
   * @return string[]
   */
  public function getCertificates()
  {
    return $this->certificates;
  }
  /**
   * Output only. The creation timestamp of a Certificate Map Entry.
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
   * Optional. One or more paragraphs of text description of a certificate map
   * entry.
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
   * A Hostname (FQDN, e.g. `example.com`) or a wildcard hostname expression
   * (`*.example.com`) for a set of hostnames with common suffix. Used as Server
   * Name Indication (SNI) for selecting a proper certificate.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * Optional. Set of labels associated with a Certificate Map Entry.
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
   * A predefined matcher for particular cases, other than SNI selection.
   *
   * Accepted values: MATCHER_UNSPECIFIED, PRIMARY
   *
   * @param self::MATCHER_* $matcher
   */
  public function setMatcher($matcher)
  {
    $this->matcher = $matcher;
  }
  /**
   * @return self::MATCHER_*
   */
  public function getMatcher()
  {
    return $this->matcher;
  }
  /**
   * Identifier. A user-defined name of the Certificate Map Entry. Certificate
   * Map Entry names must be unique globally and match pattern
   * `projects/locations/certificateMaps/certificateMapEntries`.
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
   * Output only. A serving state of this Certificate Map Entry.
   *
   * Accepted values: SERVING_STATE_UNSPECIFIED, ACTIVE, PENDING
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
   * Output only. The update timestamp of a Certificate Map Entry.
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
class_alias(CertificateMapEntry::class, 'Google_Service_CertificateManager_CertificateMapEntry');

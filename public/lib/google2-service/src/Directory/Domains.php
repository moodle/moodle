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

namespace Google\Service\Directory;

class Domains extends \Google\Collection
{
  protected $collection_key = 'domainAliases';
  /**
   * Creation time of the domain. Expressed in [Unix
   * time](https://en.wikipedia.org/wiki/Epoch_time) format. (Read-only).
   *
   * @var string
   */
  public $creationTime;
  protected $domainAliasesType = DomainAlias::class;
  protected $domainAliasesDataType = 'array';
  /**
   * The domain name of the customer.
   *
   * @var string
   */
  public $domainName;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Indicates if the domain is a primary domain (Read-only).
   *
   * @var bool
   */
  public $isPrimary;
  /**
   * Kind of resource this is.
   *
   * @var string
   */
  public $kind;
  /**
   * Indicates the verification state of a domain. (Read-only).
   *
   * @var bool
   */
  public $verified;

  /**
   * Creation time of the domain. Expressed in [Unix
   * time](https://en.wikipedia.org/wiki/Epoch_time) format. (Read-only).
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * A list of domain alias objects. (Read-only)
   *
   * @param DomainAlias[] $domainAliases
   */
  public function setDomainAliases($domainAliases)
  {
    $this->domainAliases = $domainAliases;
  }
  /**
   * @return DomainAlias[]
   */
  public function getDomainAliases()
  {
    return $this->domainAliases;
  }
  /**
   * The domain name of the customer.
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
   * ETag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Indicates if the domain is a primary domain (Read-only).
   *
   * @param bool $isPrimary
   */
  public function setIsPrimary($isPrimary)
  {
    $this->isPrimary = $isPrimary;
  }
  /**
   * @return bool
   */
  public function getIsPrimary()
  {
    return $this->isPrimary;
  }
  /**
   * Kind of resource this is.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Indicates the verification state of a domain. (Read-only).
   *
   * @param bool $verified
   */
  public function setVerified($verified)
  {
    $this->verified = $verified;
  }
  /**
   * @return bool
   */
  public function getVerified()
  {
    return $this->verified;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Domains::class, 'Google_Service_Directory_Domains');

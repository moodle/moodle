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

class DomainAlias extends \Google\Model
{
  /**
   * The creation time of the domain alias. (Read-only).
   *
   * @var string
   */
  public $creationTime;
  /**
   * The domain alias name.
   *
   * @var string
   */
  public $domainAliasName;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Kind of resource this is.
   *
   * @var string
   */
  public $kind;
  /**
   * The parent domain name that the domain alias is associated with. This can
   * either be a primary or secondary domain name within a customer.
   *
   * @var string
   */
  public $parentDomainName;
  /**
   * Indicates the verification state of a domain alias. (Read-only)
   *
   * @var bool
   */
  public $verified;

  /**
   * The creation time of the domain alias. (Read-only).
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
   * The domain alias name.
   *
   * @param string $domainAliasName
   */
  public function setDomainAliasName($domainAliasName)
  {
    $this->domainAliasName = $domainAliasName;
  }
  /**
   * @return string
   */
  public function getDomainAliasName()
  {
    return $this->domainAliasName;
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
   * The parent domain name that the domain alias is associated with. This can
   * either be a primary or secondary domain name within a customer.
   *
   * @param string $parentDomainName
   */
  public function setParentDomainName($parentDomainName)
  {
    $this->parentDomainName = $parentDomainName;
  }
  /**
   * @return string
   */
  public function getParentDomainName()
  {
    return $this->parentDomainName;
  }
  /**
   * Indicates the verification state of a domain alias. (Read-only)
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
class_alias(DomainAlias::class, 'Google_Service_Directory_DomainAlias');

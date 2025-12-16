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

namespace Google\Service\FirebaseAppHosting;

class Domain extends \Google\Model
{
  /**
   * The type is unspecified (this should not happen).
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Default, App Hosting-provided and managed domains. These domains are
   * created automatically with their parent backend and cannot be deleted
   * except by deleting that parent, and cannot be moved to another backend.
   * Default domains can be disabled via the `disabled` field.
   */
  public const TYPE_DEFAULT = 'DEFAULT';
  /**
   * Custom, developer-owned domains. Custom Domains allow you to associate a
   * domain you own with your App Hosting backend, and configure that domain to
   * serve your backend's content.
   */
  public const TYPE_CUSTOM = 'CUSTOM';
  /**
   * Optional. Annotations as key value pairs.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. Time at which the domain was created.
   *
   * @var string
   */
  public $createTime;
  protected $customDomainStatusType = CustomDomainStatus::class;
  protected $customDomainStatusDataType = '';
  /**
   * Output only. Time at which the domain was deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Optional. Whether the domain is disabled. Defaults to false.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Optional. Mutable human-readable name for the domain. 63 character limit.
   * e.g. `prod domain`.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Server-computed checksum based on other values; may be sent on
   * update or delete to ensure operation is done on expected resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the domain, e.g.
   * `/projects/p/locations/l/backends/b/domains/foo.com`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. A field that, if true, indicates that the build has an ongoing
   * LRO.
   *
   * @var bool
   */
  public $reconciling;
  protected $serveType = ServingBehavior::class;
  protected $serveDataType = '';
  /**
   * Output only. The type of the domain.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. System-assigned, unique identifier.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Time at which the domain was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Annotations as key value pairs.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. Time at which the domain was created.
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
   * Output only. Represents the state and configuration of a `CUSTOM` type
   * domain. It is only present on Domains of that type.
   *
   * @param CustomDomainStatus $customDomainStatus
   */
  public function setCustomDomainStatus(CustomDomainStatus $customDomainStatus)
  {
    $this->customDomainStatus = $customDomainStatus;
  }
  /**
   * @return CustomDomainStatus
   */
  public function getCustomDomainStatus()
  {
    return $this->customDomainStatus;
  }
  /**
   * Output only. Time at which the domain was deleted.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Optional. Whether the domain is disabled. Defaults to false.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Optional. Mutable human-readable name for the domain. 63 character limit.
   * e.g. `prod domain`.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Server-computed checksum based on other values; may be sent on
   * update or delete to ensure operation is done on expected resource.
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
   * Optional. Labels as key value pairs.
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
   * Identifier. The resource name of the domain, e.g.
   * `/projects/p/locations/l/backends/b/domains/foo.com`
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
   * Output only. A field that, if true, indicates that the build has an ongoing
   * LRO.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Optional. The serving behavior of the domain. If specified, the domain will
   * serve content other than its backend's live content.
   *
   * @param ServingBehavior $serve
   */
  public function setServe(ServingBehavior $serve)
  {
    $this->serve = $serve;
  }
  /**
   * @return ServingBehavior
   */
  public function getServe()
  {
    return $this->serve;
  }
  /**
   * Output only. The type of the domain.
   *
   * Accepted values: TYPE_UNSPECIFIED, DEFAULT, CUSTOM
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
   * Output only. System-assigned, unique identifier.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Time at which the domain was last updated.
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
class_alias(Domain::class, 'Google_Service_FirebaseAppHosting_Domain');

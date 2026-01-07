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

namespace Google\Service\Compute;

class DeprecationStatus extends \Google\Model
{
  public const STATE_ACTIVE = 'ACTIVE';
  public const STATE_DELETED = 'DELETED';
  public const STATE_DEPRECATED = 'DEPRECATED';
  public const STATE_OBSOLETE = 'OBSOLETE';
  /**
   * An optional RFC3339 timestamp on or after which the state of this resource
   * is intended to change to DELETED. This is only informational and the status
   * will not change unless the client explicitly changes it.
   *
   * @var string
   */
  public $deleted;
  /**
   * An optional RFC3339 timestamp on or after which the state of this resource
   * is intended to change to DEPRECATED. This is only informational and the
   * status will not change unless the client explicitly changes it.
   *
   * @var string
   */
  public $deprecated;
  /**
   * An optional RFC3339 timestamp on or after which the state of this resource
   * is intended to change to OBSOLETE. This is only informational and the
   * status will not change unless the client explicitly changes it.
   *
   * @var string
   */
  public $obsolete;
  /**
   * The URL of the suggested replacement for a deprecated resource. The
   * suggested replacement resource must be the same kind of resource as the
   * deprecated resource.
   *
   * @var string
   */
  public $replacement;
  /**
   * The deprecation state of this resource. This can be ACTIVE,DEPRECATED,
   * OBSOLETE, or DELETED. Operations which communicate the end of life date for
   * an image, can useACTIVE. Operations which create a new resource using
   * aDEPRECATED resource will return successfully, but with a warning
   * indicating the deprecated resource and recommending its replacement.
   * Operations which use OBSOLETE orDELETED resources will be rejected and
   * result in an error.
   *
   * @var string
   */
  public $state;

  /**
   * An optional RFC3339 timestamp on or after which the state of this resource
   * is intended to change to DELETED. This is only informational and the status
   * will not change unless the client explicitly changes it.
   *
   * @param string $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return string
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * An optional RFC3339 timestamp on or after which the state of this resource
   * is intended to change to DEPRECATED. This is only informational and the
   * status will not change unless the client explicitly changes it.
   *
   * @param string $deprecated
   */
  public function setDeprecated($deprecated)
  {
    $this->deprecated = $deprecated;
  }
  /**
   * @return string
   */
  public function getDeprecated()
  {
    return $this->deprecated;
  }
  /**
   * An optional RFC3339 timestamp on or after which the state of this resource
   * is intended to change to OBSOLETE. This is only informational and the
   * status will not change unless the client explicitly changes it.
   *
   * @param string $obsolete
   */
  public function setObsolete($obsolete)
  {
    $this->obsolete = $obsolete;
  }
  /**
   * @return string
   */
  public function getObsolete()
  {
    return $this->obsolete;
  }
  /**
   * The URL of the suggested replacement for a deprecated resource. The
   * suggested replacement resource must be the same kind of resource as the
   * deprecated resource.
   *
   * @param string $replacement
   */
  public function setReplacement($replacement)
  {
    $this->replacement = $replacement;
  }
  /**
   * @return string
   */
  public function getReplacement()
  {
    return $this->replacement;
  }
  /**
   * The deprecation state of this resource. This can be ACTIVE,DEPRECATED,
   * OBSOLETE, or DELETED. Operations which communicate the end of life date for
   * an image, can useACTIVE. Operations which create a new resource using
   * aDEPRECATED resource will return successfully, but with a warning
   * indicating the deprecated resource and recommending its replacement.
   * Operations which use OBSOLETE orDELETED resources will be rejected and
   * result in an error.
   *
   * Accepted values: ACTIVE, DELETED, DEPRECATED, OBSOLETE
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeprecationStatus::class, 'Google_Service_Compute_DeprecationStatus');

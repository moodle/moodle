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

namespace Google\Service\OnDemandScanning;

class Version extends \Google\Model
{
  /**
   * Unknown.
   */
  public const KIND_VERSION_KIND_UNSPECIFIED = 'VERSION_KIND_UNSPECIFIED';
  /**
   * A standard package version.
   */
  public const KIND_NORMAL = 'NORMAL';
  /**
   * A special version representing negative infinity.
   */
  public const KIND_MINIMUM = 'MINIMUM';
  /**
   * A special version representing positive infinity.
   */
  public const KIND_MAXIMUM = 'MAXIMUM';
  /**
   * Used to correct mistakes in the version numbering scheme.
   *
   * @var int
   */
  public $epoch;
  /**
   * Human readable version string. This string is of the form :- and is only
   * set when kind is NORMAL.
   *
   * @var string
   */
  public $fullName;
  /**
   * Whether this version is specifying part of an inclusive range. Grafeas does
   * not have the capability to specify version ranges; instead we have fields
   * that specify start version and end versions. At times this is insufficient
   * - we also need to specify whether the version is included in the range or
   * is excluded from the range. This boolean is expected to be set to true when
   * the version is included in a range.
   *
   * @var bool
   */
  public $inclusive;
  /**
   * Required. Distinguishes between sentinel MIN/MAX versions and normal
   * versions.
   *
   * @var string
   */
  public $kind;
  /**
   * Required only when version kind is NORMAL. The main part of the version
   * name.
   *
   * @var string
   */
  public $name;
  /**
   * The iteration of the package build from the above version.
   *
   * @var string
   */
  public $revision;

  /**
   * Used to correct mistakes in the version numbering scheme.
   *
   * @param int $epoch
   */
  public function setEpoch($epoch)
  {
    $this->epoch = $epoch;
  }
  /**
   * @return int
   */
  public function getEpoch()
  {
    return $this->epoch;
  }
  /**
   * Human readable version string. This string is of the form :- and is only
   * set when kind is NORMAL.
   *
   * @param string $fullName
   */
  public function setFullName($fullName)
  {
    $this->fullName = $fullName;
  }
  /**
   * @return string
   */
  public function getFullName()
  {
    return $this->fullName;
  }
  /**
   * Whether this version is specifying part of an inclusive range. Grafeas does
   * not have the capability to specify version ranges; instead we have fields
   * that specify start version and end versions. At times this is insufficient
   * - we also need to specify whether the version is included in the range or
   * is excluded from the range. This boolean is expected to be set to true when
   * the version is included in a range.
   *
   * @param bool $inclusive
   */
  public function setInclusive($inclusive)
  {
    $this->inclusive = $inclusive;
  }
  /**
   * @return bool
   */
  public function getInclusive()
  {
    return $this->inclusive;
  }
  /**
   * Required. Distinguishes between sentinel MIN/MAX versions and normal
   * versions.
   *
   * Accepted values: VERSION_KIND_UNSPECIFIED, NORMAL, MINIMUM, MAXIMUM
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Required only when version kind is NORMAL. The main part of the version
   * name.
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
   * The iteration of the package build from the above version.
   *
   * @param string $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return string
   */
  public function getRevision()
  {
    return $this->revision;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Version::class, 'Google_Service_OnDemandScanning_Version');

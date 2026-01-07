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

namespace Google\Service\CloudAsset;

class GoogleIdentityAccesscontextmanagerV1ServicePerimeter extends \Google\Model
{
  /**
   * Regular Perimeter. When no value is specified, the perimeter uses this
   * type.
   */
  public const PERIMETER_TYPE_PERIMETER_TYPE_REGULAR = 'PERIMETER_TYPE_REGULAR';
  /**
   * Perimeter Bridge.
   */
  public const PERIMETER_TYPE_PERIMETER_TYPE_BRIDGE = 'PERIMETER_TYPE_BRIDGE';
  /**
   * Description of the `ServicePerimeter` and its use. Does not affect
   * behavior.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. An opaque identifier for the current version of the
   * `ServicePerimeter`. This identifier does not follow any specific format. If
   * an etag is not provided, the operation will be performed as if a valid etag
   * is provided.
   *
   * @var string
   */
  public $etag;
  /**
   * Identifier. Resource name for the `ServicePerimeter`. Format:
   * `accessPolicies/{access_policy}/servicePerimeters/{service_perimeter}`. The
   * `service_perimeter` component must begin with a letter, followed by
   * alphanumeric characters or `_`. After you create a `ServicePerimeter`, you
   * cannot change its `name`.
   *
   * @var string
   */
  public $name;
  /**
   * Perimeter type indicator. A single project or VPC network is allowed to be
   * a member of single regular perimeter, but multiple service perimeter
   * bridges. A project cannot be a included in a perimeter bridge without being
   * included in regular perimeter. For perimeter bridges, the restricted
   * service list as well as access level lists must be empty.
   *
   * @var string
   */
  public $perimeterType;
  protected $specType = GoogleIdentityAccesscontextmanagerV1ServicePerimeterConfig::class;
  protected $specDataType = '';
  protected $statusType = GoogleIdentityAccesscontextmanagerV1ServicePerimeterConfig::class;
  protected $statusDataType = '';
  /**
   * Human readable title. Must be unique within the Policy.
   *
   * @var string
   */
  public $title;
  /**
   * Use explicit dry run spec flag. Ordinarily, a dry-run spec implicitly
   * exists for all Service Perimeters, and that spec is identical to the status
   * for those Service Perimeters. When this flag is set, it inhibits the
   * generation of the implicit spec, thereby allowing the user to explicitly
   * provide a configuration ("spec") to use in a dry-run version of the Service
   * Perimeter. This allows the user to test changes to the enforced config
   * ("status") without actually enforcing them. This testing is done through
   * analyzing the differences between currently enforced and suggested
   * restrictions. use_explicit_dry_run_spec must bet set to True if any of the
   * fields in the spec are set to non-default values.
   *
   * @var bool
   */
  public $useExplicitDryRunSpec;

  /**
   * Description of the `ServicePerimeter` and its use. Does not affect
   * behavior.
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
   * Optional. An opaque identifier for the current version of the
   * `ServicePerimeter`. This identifier does not follow any specific format. If
   * an etag is not provided, the operation will be performed as if a valid etag
   * is provided.
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
   * Identifier. Resource name for the `ServicePerimeter`. Format:
   * `accessPolicies/{access_policy}/servicePerimeters/{service_perimeter}`. The
   * `service_perimeter` component must begin with a letter, followed by
   * alphanumeric characters or `_`. After you create a `ServicePerimeter`, you
   * cannot change its `name`.
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
   * Perimeter type indicator. A single project or VPC network is allowed to be
   * a member of single regular perimeter, but multiple service perimeter
   * bridges. A project cannot be a included in a perimeter bridge without being
   * included in regular perimeter. For perimeter bridges, the restricted
   * service list as well as access level lists must be empty.
   *
   * Accepted values: PERIMETER_TYPE_REGULAR, PERIMETER_TYPE_BRIDGE
   *
   * @param self::PERIMETER_TYPE_* $perimeterType
   */
  public function setPerimeterType($perimeterType)
  {
    $this->perimeterType = $perimeterType;
  }
  /**
   * @return self::PERIMETER_TYPE_*
   */
  public function getPerimeterType()
  {
    return $this->perimeterType;
  }
  /**
   * Proposed (or dry run) ServicePerimeter configuration. This configuration
   * allows to specify and test ServicePerimeter configuration without enforcing
   * actual access restrictions. Only allowed to be set when the
   * "use_explicit_dry_run_spec" flag is set.
   *
   * @param GoogleIdentityAccesscontextmanagerV1ServicePerimeterConfig $spec
   */
  public function setSpec(GoogleIdentityAccesscontextmanagerV1ServicePerimeterConfig $spec)
  {
    $this->spec = $spec;
  }
  /**
   * @return GoogleIdentityAccesscontextmanagerV1ServicePerimeterConfig
   */
  public function getSpec()
  {
    return $this->spec;
  }
  /**
   * Current ServicePerimeter configuration. Specifies sets of resources,
   * restricted services and access levels that determine perimeter content and
   * boundaries.
   *
   * @param GoogleIdentityAccesscontextmanagerV1ServicePerimeterConfig $status
   */
  public function setStatus(GoogleIdentityAccesscontextmanagerV1ServicePerimeterConfig $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleIdentityAccesscontextmanagerV1ServicePerimeterConfig
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Human readable title. Must be unique within the Policy.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Use explicit dry run spec flag. Ordinarily, a dry-run spec implicitly
   * exists for all Service Perimeters, and that spec is identical to the status
   * for those Service Perimeters. When this flag is set, it inhibits the
   * generation of the implicit spec, thereby allowing the user to explicitly
   * provide a configuration ("spec") to use in a dry-run version of the Service
   * Perimeter. This allows the user to test changes to the enforced config
   * ("status") without actually enforcing them. This testing is done through
   * analyzing the differences between currently enforced and suggested
   * restrictions. use_explicit_dry_run_spec must bet set to True if any of the
   * fields in the spec are set to non-default values.
   *
   * @param bool $useExplicitDryRunSpec
   */
  public function setUseExplicitDryRunSpec($useExplicitDryRunSpec)
  {
    $this->useExplicitDryRunSpec = $useExplicitDryRunSpec;
  }
  /**
   * @return bool
   */
  public function getUseExplicitDryRunSpec()
  {
    return $this->useExplicitDryRunSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIdentityAccesscontextmanagerV1ServicePerimeter::class, 'Google_Service_CloudAsset_GoogleIdentityAccesscontextmanagerV1ServicePerimeter');

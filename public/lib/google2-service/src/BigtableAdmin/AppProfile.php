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

namespace Google\Service\BigtableAdmin;

class AppProfile extends \Google\Model
{
  /**
   * Default value. Mapped to PRIORITY_HIGH (the legacy behavior) on creation.
   */
  public const PRIORITY_PRIORITY_UNSPECIFIED = 'PRIORITY_UNSPECIFIED';
  public const PRIORITY_PRIORITY_LOW = 'PRIORITY_LOW';
  public const PRIORITY_PRIORITY_MEDIUM = 'PRIORITY_MEDIUM';
  public const PRIORITY_PRIORITY_HIGH = 'PRIORITY_HIGH';
  protected $dataBoostIsolationReadOnlyType = DataBoostIsolationReadOnly::class;
  protected $dataBoostIsolationReadOnlyDataType = '';
  /**
   * Long form description of the use case for this AppProfile.
   *
   * @var string
   */
  public $description;
  /**
   * Strongly validated etag for optimistic concurrency control. Preserve the
   * value returned from `GetAppProfile` when calling `UpdateAppProfile` to fail
   * the request if there has been a modification in the mean time. The
   * `update_mask` of the request need not include `etag` for this protection to
   * apply. See [Wikipedia](https://en.wikipedia.org/wiki/HTTP_ETag) and [RFC
   * 7232](https://tools.ietf.org/html/rfc7232#section-2.3) for more details.
   *
   * @var string
   */
  public $etag;
  protected $multiClusterRoutingUseAnyType = MultiClusterRoutingUseAny::class;
  protected $multiClusterRoutingUseAnyDataType = '';
  /**
   * The unique name of the app profile, up to 50 characters long. Values are of
   * the form `projects/{project}/instances/{instance}/appProfiles/_a-zA-Z0-9*`.
   *
   * @var string
   */
  public $name;
  /**
   * This field has been deprecated in favor of `standard_isolation.priority`.
   * If you set this field, `standard_isolation.priority` will be set instead.
   * The priority of requests sent using this app profile.
   *
   * @deprecated
   * @var string
   */
  public $priority;
  protected $singleClusterRoutingType = SingleClusterRouting::class;
  protected $singleClusterRoutingDataType = '';
  protected $standardIsolationType = StandardIsolation::class;
  protected $standardIsolationDataType = '';

  /**
   * Specifies that this app profile is intended for read-only usage via the
   * Data Boost feature.
   *
   * @param DataBoostIsolationReadOnly $dataBoostIsolationReadOnly
   */
  public function setDataBoostIsolationReadOnly(DataBoostIsolationReadOnly $dataBoostIsolationReadOnly)
  {
    $this->dataBoostIsolationReadOnly = $dataBoostIsolationReadOnly;
  }
  /**
   * @return DataBoostIsolationReadOnly
   */
  public function getDataBoostIsolationReadOnly()
  {
    return $this->dataBoostIsolationReadOnly;
  }
  /**
   * Long form description of the use case for this AppProfile.
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
   * Strongly validated etag for optimistic concurrency control. Preserve the
   * value returned from `GetAppProfile` when calling `UpdateAppProfile` to fail
   * the request if there has been a modification in the mean time. The
   * `update_mask` of the request need not include `etag` for this protection to
   * apply. See [Wikipedia](https://en.wikipedia.org/wiki/HTTP_ETag) and [RFC
   * 7232](https://tools.ietf.org/html/rfc7232#section-2.3) for more details.
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
   * Use a multi-cluster routing policy.
   *
   * @param MultiClusterRoutingUseAny $multiClusterRoutingUseAny
   */
  public function setMultiClusterRoutingUseAny(MultiClusterRoutingUseAny $multiClusterRoutingUseAny)
  {
    $this->multiClusterRoutingUseAny = $multiClusterRoutingUseAny;
  }
  /**
   * @return MultiClusterRoutingUseAny
   */
  public function getMultiClusterRoutingUseAny()
  {
    return $this->multiClusterRoutingUseAny;
  }
  /**
   * The unique name of the app profile, up to 50 characters long. Values are of
   * the form `projects/{project}/instances/{instance}/appProfiles/_a-zA-Z0-9*`.
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
   * This field has been deprecated in favor of `standard_isolation.priority`.
   * If you set this field, `standard_isolation.priority` will be set instead.
   * The priority of requests sent using this app profile.
   *
   * Accepted values: PRIORITY_UNSPECIFIED, PRIORITY_LOW, PRIORITY_MEDIUM,
   * PRIORITY_HIGH
   *
   * @deprecated
   * @param self::PRIORITY_* $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @deprecated
   * @return self::PRIORITY_*
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Use a single-cluster routing policy.
   *
   * @param SingleClusterRouting $singleClusterRouting
   */
  public function setSingleClusterRouting(SingleClusterRouting $singleClusterRouting)
  {
    $this->singleClusterRouting = $singleClusterRouting;
  }
  /**
   * @return SingleClusterRouting
   */
  public function getSingleClusterRouting()
  {
    return $this->singleClusterRouting;
  }
  /**
   * The standard options used for isolating this app profile's traffic from
   * other use cases.
   *
   * @param StandardIsolation $standardIsolation
   */
  public function setStandardIsolation(StandardIsolation $standardIsolation)
  {
    $this->standardIsolation = $standardIsolation;
  }
  /**
   * @return StandardIsolation
   */
  public function getStandardIsolation()
  {
    return $this->standardIsolation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppProfile::class, 'Google_Service_BigtableAdmin_AppProfile');

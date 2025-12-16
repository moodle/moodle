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

namespace Google\Service\ServiceManagement;

class ExperimentalFeatures extends \Google\Model
{
  /**
   * Enables generation of protobuf code using new types that are more Pythonic
   * which are included in `protobuf>=5.29.x`. This feature will be enabled by
   * default 1 month after launching the feature in preview packages.
   *
   * @var bool
   */
  public $protobufPythonicTypesEnabled;
  /**
   * Enables generation of asynchronous REST clients if `rest` transport is
   * enabled. By default, asynchronous REST clients will not be generated. This
   * feature will be enabled by default 1 month after launching the feature in
   * preview packages.
   *
   * @var bool
   */
  public $restAsyncIoEnabled;
  /**
   * Disables generation of an unversioned Python package for this client
   * library. This means that the module names will need to be versioned in
   * import statements. For example `import google.cloud.library_v2` instead of
   * `import google.cloud.library`.
   *
   * @var bool
   */
  public $unversionedPackageDisabled;

  /**
   * Enables generation of protobuf code using new types that are more Pythonic
   * which are included in `protobuf>=5.29.x`. This feature will be enabled by
   * default 1 month after launching the feature in preview packages.
   *
   * @param bool $protobufPythonicTypesEnabled
   */
  public function setProtobufPythonicTypesEnabled($protobufPythonicTypesEnabled)
  {
    $this->protobufPythonicTypesEnabled = $protobufPythonicTypesEnabled;
  }
  /**
   * @return bool
   */
  public function getProtobufPythonicTypesEnabled()
  {
    return $this->protobufPythonicTypesEnabled;
  }
  /**
   * Enables generation of asynchronous REST clients if `rest` transport is
   * enabled. By default, asynchronous REST clients will not be generated. This
   * feature will be enabled by default 1 month after launching the feature in
   * preview packages.
   *
   * @param bool $restAsyncIoEnabled
   */
  public function setRestAsyncIoEnabled($restAsyncIoEnabled)
  {
    $this->restAsyncIoEnabled = $restAsyncIoEnabled;
  }
  /**
   * @return bool
   */
  public function getRestAsyncIoEnabled()
  {
    return $this->restAsyncIoEnabled;
  }
  /**
   * Disables generation of an unversioned Python package for this client
   * library. This means that the module names will need to be versioned in
   * import statements. For example `import google.cloud.library_v2` instead of
   * `import google.cloud.library`.
   *
   * @param bool $unversionedPackageDisabled
   */
  public function setUnversionedPackageDisabled($unversionedPackageDisabled)
  {
    $this->unversionedPackageDisabled = $unversionedPackageDisabled;
  }
  /**
   * @return bool
   */
  public function getUnversionedPackageDisabled()
  {
    return $this->unversionedPackageDisabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExperimentalFeatures::class, 'Google_Service_ServiceManagement_ExperimentalFeatures');

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

class GoogleCloudAssetV1ListConstraint extends \Google\Model
{
  /**
   * Indicates whether values grouped into categories can be used in
   * `Policy.allowed_values` and `Policy.denied_values`. For example,
   * `"in:Python"` would match any value in the 'Python' group.
   *
   * @var bool
   */
  public $supportsIn;
  /**
   * Indicates whether subtrees of Cloud Resource Manager resource hierarchy can
   * be used in `Policy.allowed_values` and `Policy.denied_values`. For example,
   * `"under:folders/123"` would match any resource under the 'folders/123'
   * folder.
   *
   * @var bool
   */
  public $supportsUnder;

  /**
   * Indicates whether values grouped into categories can be used in
   * `Policy.allowed_values` and `Policy.denied_values`. For example,
   * `"in:Python"` would match any value in the 'Python' group.
   *
   * @param bool $supportsIn
   */
  public function setSupportsIn($supportsIn)
  {
    $this->supportsIn = $supportsIn;
  }
  /**
   * @return bool
   */
  public function getSupportsIn()
  {
    return $this->supportsIn;
  }
  /**
   * Indicates whether subtrees of Cloud Resource Manager resource hierarchy can
   * be used in `Policy.allowed_values` and `Policy.denied_values`. For example,
   * `"under:folders/123"` would match any resource under the 'folders/123'
   * folder.
   *
   * @param bool $supportsUnder
   */
  public function setSupportsUnder($supportsUnder)
  {
    $this->supportsUnder = $supportsUnder;
  }
  /**
   * @return bool
   */
  public function getSupportsUnder()
  {
    return $this->supportsUnder;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssetV1ListConstraint::class, 'Google_Service_CloudAsset_GoogleCloudAssetV1ListConstraint');

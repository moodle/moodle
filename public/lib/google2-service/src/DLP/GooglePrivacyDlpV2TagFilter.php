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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2TagFilter extends \Google\Model
{
  /**
   * The namespaced name for the tag key. Must be in the format
   * `{parent_id}/{tag_key_short_name}`, for example, "123456/sensitive" for an
   * organization parent, or "my-project/sensitive" for a project parent.
   *
   * @var string
   */
  public $namespacedTagKey;
  /**
   * The namespaced name for the tag value. Must be in the format
   * `{parent_id}/{tag_key_short_name}/{short_name}`, for example,
   * "123456/environment/prod" for an organization parent, or "my-
   * project/environment/prod" for a project parent.
   *
   * @var string
   */
  public $namespacedTagValue;

  /**
   * The namespaced name for the tag key. Must be in the format
   * `{parent_id}/{tag_key_short_name}`, for example, "123456/sensitive" for an
   * organization parent, or "my-project/sensitive" for a project parent.
   *
   * @param string $namespacedTagKey
   */
  public function setNamespacedTagKey($namespacedTagKey)
  {
    $this->namespacedTagKey = $namespacedTagKey;
  }
  /**
   * @return string
   */
  public function getNamespacedTagKey()
  {
    return $this->namespacedTagKey;
  }
  /**
   * The namespaced name for the tag value. Must be in the format
   * `{parent_id}/{tag_key_short_name}/{short_name}`, for example,
   * "123456/environment/prod" for an organization parent, or "my-
   * project/environment/prod" for a project parent.
   *
   * @param string $namespacedTagValue
   */
  public function setNamespacedTagValue($namespacedTagValue)
  {
    $this->namespacedTagValue = $namespacedTagValue;
  }
  /**
   * @return string
   */
  public function getNamespacedTagValue()
  {
    return $this->namespacedTagValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TagFilter::class, 'Google_Service_DLP_GooglePrivacyDlpV2TagFilter');

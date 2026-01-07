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

class GooglePrivacyDlpV2TagValue extends \Google\Model
{
  /**
   * The namespaced name for the tag value to attach to resources. Must be in
   * the format `{parent_id}/{tag_key_short_name}/{short_name}`, for example,
   * "123456/environment/prod" for an organization parent, or "my-
   * project/environment/prod" for a project parent.
   *
   * @var string
   */
  public $namespacedValue;

  /**
   * The namespaced name for the tag value to attach to resources. Must be in
   * the format `{parent_id}/{tag_key_short_name}/{short_name}`, for example,
   * "123456/environment/prod" for an organization parent, or "my-
   * project/environment/prod" for a project parent.
   *
   * @param string $namespacedValue
   */
  public function setNamespacedValue($namespacedValue)
  {
    $this->namespacedValue = $namespacedValue;
  }
  /**
   * @return string
   */
  public function getNamespacedValue()
  {
    return $this->namespacedValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TagValue::class, 'Google_Service_DLP_GooglePrivacyDlpV2TagValue');

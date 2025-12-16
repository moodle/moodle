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

class GooglePrivacyDlpV2Tag extends \Google\Model
{
  /**
   * The key of a tag key-value pair. For Google Cloud resources, this is the
   * resource name of the key, for example, "tagKeys/123456".
   *
   * @var string
   */
  public $key;
  /**
   * The namespaced name for the tag value to attach to Google Cloud resources.
   * Must be in the format `{parent_id}/{tag_key_short_name}/{short_name}`, for
   * example, "123456/environment/prod" for an organization parent, or "my-
   * project/environment/prod" for a project parent. This is only set for Google
   * Cloud resources.
   *
   * @var string
   */
  public $namespacedTagValue;
  /**
   * The value of a tag key-value pair. For Google Cloud resources, this is the
   * resource name of the value, for example, "tagValues/123456".
   *
   * @var string
   */
  public $value;

  /**
   * The key of a tag key-value pair. For Google Cloud resources, this is the
   * resource name of the key, for example, "tagKeys/123456".
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * The namespaced name for the tag value to attach to Google Cloud resources.
   * Must be in the format `{parent_id}/{tag_key_short_name}/{short_name}`, for
   * example, "123456/environment/prod" for an organization parent, or "my-
   * project/environment/prod" for a project parent. This is only set for Google
   * Cloud resources.
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
  /**
   * The value of a tag key-value pair. For Google Cloud resources, this is the
   * resource name of the value, for example, "tagValues/123456".
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Tag::class, 'Google_Service_DLP_GooglePrivacyDlpV2Tag');

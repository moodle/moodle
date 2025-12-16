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

namespace Google\Service\ApigeeRegistry;

class ApiVersion extends \Google\Model
{
  /**
   * Annotations attach non-identifying metadata to resources. Annotation keys
   * and values are less restricted than those of labels, but should be
   * generally used for small values of broad interest. Larger, topic- specific
   * metadata should be stored in Artifacts.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. Creation timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * A detailed description.
   *
   * @var string
   */
  public $description;
  /**
   * Human-meaningful name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Labels attach identifying metadata to resources. Identifying metadata can
   * be used to filter list operations. Label keys and values can be no longer
   * than 64 characters (Unicode codepoints), can only contain lowercase
   * letters, numeric characters, underscores and dashes. International
   * characters are allowed. No more than 64 user labels can be associated with
   * one resource (System labels are excluded). See https://goo.gl/xmQnxf for
   * more information and examples of labels. System reserved label keys are
   * prefixed with `apigeeregistry.googleapis.com/` and cannot be changed.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Resource name.
   *
   * @var string
   */
  public $name;
  /**
   * The primary spec for this version. Format: projects/{project}/locations/{lo
   * cation}/apis/{api}/versions/{version}/specs/{spec}
   *
   * @var string
   */
  public $primarySpec;
  /**
   * A user-definable description of the lifecycle phase of this API version.
   * Format: free-form, but we expect single words that describe API maturity,
   * e.g., "CONCEPT", "DESIGN", "DEVELOPMENT", "STAGING", "PRODUCTION",
   * "DEPRECATED", "RETIRED".
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Last update timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Annotations attach non-identifying metadata to resources. Annotation keys
   * and values are less restricted than those of labels, but should be
   * generally used for small values of broad interest. Larger, topic- specific
   * metadata should be stored in Artifacts.
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
   * Output only. Creation timestamp.
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
   * A detailed description.
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
   * Human-meaningful name.
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
   * Labels attach identifying metadata to resources. Identifying metadata can
   * be used to filter list operations. Label keys and values can be no longer
   * than 64 characters (Unicode codepoints), can only contain lowercase
   * letters, numeric characters, underscores and dashes. International
   * characters are allowed. No more than 64 user labels can be associated with
   * one resource (System labels are excluded). See https://goo.gl/xmQnxf for
   * more information and examples of labels. System reserved label keys are
   * prefixed with `apigeeregistry.googleapis.com/` and cannot be changed.
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
   * Resource name.
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
   * The primary spec for this version. Format: projects/{project}/locations/{lo
   * cation}/apis/{api}/versions/{version}/specs/{spec}
   *
   * @param string $primarySpec
   */
  public function setPrimarySpec($primarySpec)
  {
    $this->primarySpec = $primarySpec;
  }
  /**
   * @return string
   */
  public function getPrimarySpec()
  {
    return $this->primarySpec;
  }
  /**
   * A user-definable description of the lifecycle phase of this API version.
   * Format: free-form, but we expect single words that describe API maturity,
   * e.g., "CONCEPT", "DESIGN", "DEVELOPMENT", "STAGING", "PRODUCTION",
   * "DEPRECATED", "RETIRED".
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Last update timestamp.
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
class_alias(ApiVersion::class, 'Google_Service_ApigeeRegistry_ApiVersion');

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

class Api extends \Google\Model
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
   * A user-definable description of the availability of this service. Format:
   * free-form, but we expect single words that describe availability, e.g.,
   * "NONE", "TESTING", "PREVIEW", "GENERAL", "DEPRECATED", "SHUTDOWN".
   *
   * @var string
   */
  public $availability;
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
   * letters, numeric characters, underscores, and dashes. International
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
   * The recommended deployment of the API. Format: `projects/{project}/location
   * s/{location}/apis/{api}/deployments/{deployment}`
   *
   * @var string
   */
  public $recommendedDeployment;
  /**
   * The recommended version of the API. Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   *
   * @var string
   */
  public $recommendedVersion;
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
   * A user-definable description of the availability of this service. Format:
   * free-form, but we expect single words that describe availability, e.g.,
   * "NONE", "TESTING", "PREVIEW", "GENERAL", "DEPRECATED", "SHUTDOWN".
   *
   * @param string $availability
   */
  public function setAvailability($availability)
  {
    $this->availability = $availability;
  }
  /**
   * @return string
   */
  public function getAvailability()
  {
    return $this->availability;
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
   * letters, numeric characters, underscores, and dashes. International
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
   * The recommended deployment of the API. Format: `projects/{project}/location
   * s/{location}/apis/{api}/deployments/{deployment}`
   *
   * @param string $recommendedDeployment
   */
  public function setRecommendedDeployment($recommendedDeployment)
  {
    $this->recommendedDeployment = $recommendedDeployment;
  }
  /**
   * @return string
   */
  public function getRecommendedDeployment()
  {
    return $this->recommendedDeployment;
  }
  /**
   * The recommended version of the API. Format:
   * `projects/{project}/locations/{location}/apis/{api}/versions/{version}`
   *
   * @param string $recommendedVersion
   */
  public function setRecommendedVersion($recommendedVersion)
  {
    $this->recommendedVersion = $recommendedVersion;
  }
  /**
   * @return string
   */
  public function getRecommendedVersion()
  {
    return $this->recommendedVersion;
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
class_alias(Api::class, 'Google_Service_ApigeeRegistry_Api');

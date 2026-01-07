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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1NextSchema extends \Google\Model
{
  /**
   * Output only. The time when the Schema was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The user-defined name of the Schema.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. The GCP labels for the Schema.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the Schema. Format:
   * `projects/{project}/locations/{location}/schemas/{schema}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time when the Schema was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the Schema was created.
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
   * Required. The user-defined name of the Schema.
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
   * Optional. The GCP labels for the Schema.
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
   * Identifier. The resource name of the Schema. Format:
   * `projects/{project}/locations/{location}/schemas/{schema}`
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
   * Output only. The time when the Schema was last updated.
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
class_alias(GoogleCloudDocumentaiV1NextSchema::class, 'Google_Service_Document_GoogleCloudDocumentaiV1NextSchema');

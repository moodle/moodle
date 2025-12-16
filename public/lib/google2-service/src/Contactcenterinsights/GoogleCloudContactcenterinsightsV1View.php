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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1View extends \Google\Model
{
  /**
   * Output only. The time at which this view was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The human-readable display name of the view.
   *
   * @var string
   */
  public $displayName;
  /**
   * Immutable. The resource name of the view. Format:
   * projects/{project}/locations/{location}/views/{view}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The most recent time at which the view was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * A filter to reduce conversation results to a specific subset. Refer to
   * https://cloud.google.com/contact-center/insights/docs/filtering for
   * details.
   *
   * @var string
   */
  public $value;

  /**
   * Output only. The time at which this view was created.
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
   * The human-readable display name of the view.
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
   * Immutable. The resource name of the view. Format:
   * projects/{project}/locations/{location}/views/{view}
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
   * Output only. The most recent time at which the view was updated.
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
  /**
   * A filter to reduce conversation results to a specific subset. Refer to
   * https://cloud.google.com/contact-center/insights/docs/filtering for
   * details.
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
class_alias(GoogleCloudContactcenterinsightsV1View::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1View');

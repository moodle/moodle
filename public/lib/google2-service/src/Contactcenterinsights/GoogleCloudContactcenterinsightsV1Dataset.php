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

class GoogleCloudContactcenterinsightsV1Dataset extends \Google\Model
{
  /**
   * Default value for unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * For evals only.
   */
  public const TYPE_EVAL = 'EVAL';
  /**
   * Dataset with new conversations coming in regularly (Insights legacy
   * conversations and AI trainer)
   */
  public const TYPE_LIVE = 'LIVE';
  /**
   * Output only. Dataset create time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Dataset description.
   *
   * @var string
   */
  public $description;
  /**
   * Display name for the dataaset
   *
   * @var string
   */
  public $displayName;
  /**
   * Immutable. Identifier. Resource name of the dataset. Format:
   * projects/{project}/locations/{location}/datasets/{dataset}
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Option TTL for the dataset.
   *
   * @var string
   */
  public $ttl;
  /**
   * Dataset usage type.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Dataset update time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Dataset create time.
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
   * Dataset description.
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
   * Display name for the dataaset
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
   * Immutable. Identifier. Resource name of the dataset. Format:
   * projects/{project}/locations/{location}/datasets/{dataset}
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
   * Optional. Option TTL for the dataset.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * Dataset usage type.
   *
   * Accepted values: TYPE_UNSPECIFIED, EVAL, LIVE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. Dataset update time.
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
class_alias(GoogleCloudContactcenterinsightsV1Dataset::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1Dataset');

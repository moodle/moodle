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

namespace Google\Service\RapidMigrationAssessment;

class Annotation extends \Google\Model
{
  /**
   * Unknown type
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Indicates that this project has opted into StratoZone export.
   */
  public const TYPE_TYPE_LEGACY_EXPORT_CONSENT = 'TYPE_LEGACY_EXPORT_CONSENT';
  /**
   * Indicates that this project is created by Qwiklab.
   */
  public const TYPE_TYPE_QWIKLAB = 'TYPE_QWIKLAB';
  /**
   * Output only. Create time stamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * name of resource.
   *
   * @var string
   */
  public $name;
  /**
   * Type of an annotation.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Update time stamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Create time stamp.
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
   * Labels as key value pairs.
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
   * name of resource.
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
   * Type of an annotation.
   *
   * Accepted values: TYPE_UNSPECIFIED, TYPE_LEGACY_EXPORT_CONSENT, TYPE_QWIKLAB
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
   * Output only. Update time stamp.
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
class_alias(Annotation::class, 'Google_Service_RapidMigrationAssessment_Annotation');

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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1DataCollector extends \Google\Model
{
  /**
   * For future compatibility.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * For integer values.
   */
  public const TYPE_INTEGER = 'INTEGER';
  /**
   * For float values.
   */
  public const TYPE_FLOAT = 'FLOAT';
  /**
   * For string values.
   */
  public const TYPE_STRING = 'STRING';
  /**
   * For boolean values.
   */
  public const TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * For datetime values.
   */
  public const TYPE_DATETIME = 'DATETIME';
  /**
   * Output only. The time at which the data collector was created in
   * milliseconds since the epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * A description of the data collector.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The time at which the Data Collector was last updated in
   * milliseconds since the epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * ID of the data collector. Must begin with `dc_`.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The type of data this data collector will collect.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The time at which the data collector was created in
   * milliseconds since the epoch.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * A description of the data collector.
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
   * Output only. The time at which the Data Collector was last updated in
   * milliseconds since the epoch.
   *
   * @param string $lastModifiedAt
   */
  public function setLastModifiedAt($lastModifiedAt)
  {
    $this->lastModifiedAt = $lastModifiedAt;
  }
  /**
   * @return string
   */
  public function getLastModifiedAt()
  {
    return $this->lastModifiedAt;
  }
  /**
   * ID of the data collector. Must begin with `dc_`.
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
   * Immutable. The type of data this data collector will collect.
   *
   * Accepted values: TYPE_UNSPECIFIED, INTEGER, FLOAT, STRING, BOOLEAN,
   * DATETIME
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1DataCollector::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DataCollector');

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

namespace Google\Service\Logging;

class LogView extends \Google\Model
{
  /**
   * Output only. The creation timestamp of the view.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Describes this view.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Filter that restricts which log entries in a bucket are visible
   * in this view.Filters must be logical conjunctions that use the AND
   * operator, and they can use any of the following qualifiers: SOURCE(), which
   * specifies a project, folder, organization, or billing account of origin.
   * resource.type, which specifies the resource type. LOG_ID(), which
   * identifies the log.They can also use the negations of these qualifiers with
   * the NOT operator.For example:SOURCE("projects/myproject") AND resource.type
   * = "gce_instance" AND NOT LOG_ID("stdout")
   *
   * @var string
   */
  public $filter;
  /**
   * Output only. The resource name of the view.For example:projects/my-
   * project/locations/global/buckets/my-bucket/views/my-view
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The last update timestamp of the view.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The creation timestamp of the view.
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
   * Optional. Describes this view.
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
   * Optional. Filter that restricts which log entries in a bucket are visible
   * in this view.Filters must be logical conjunctions that use the AND
   * operator, and they can use any of the following qualifiers: SOURCE(), which
   * specifies a project, folder, organization, or billing account of origin.
   * resource.type, which specifies the resource type. LOG_ID(), which
   * identifies the log.They can also use the negations of these qualifiers with
   * the NOT operator.For example:SOURCE("projects/myproject") AND resource.type
   * = "gce_instance" AND NOT LOG_ID("stdout")
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Output only. The resource name of the view.For example:projects/my-
   * project/locations/global/buckets/my-bucket/views/my-view
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
   * Output only. The last update timestamp of the view.
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
class_alias(LogView::class, 'Google_Service_Logging_LogView');

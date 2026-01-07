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

namespace Google\Service\SecurityCommandCenter;

class Container extends \Google\Collection
{
  protected $collection_key = 'labels';
  /**
   * The time that the container was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional container image ID, if provided by the container runtime. Uniquely
   * identifies the container image launched using a container image digest.
   *
   * @var string
   */
  public $imageId;
  protected $labelsType = Label::class;
  protected $labelsDataType = 'array';
  /**
   * Name of the container.
   *
   * @var string
   */
  public $name;
  /**
   * Container image URI provided when configuring a pod or container. This
   * string can identify a container image version using mutable tags.
   *
   * @var string
   */
  public $uri;

  /**
   * The time that the container was created.
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
   * Optional container image ID, if provided by the container runtime. Uniquely
   * identifies the container image launched using a container image digest.
   *
   * @param string $imageId
   */
  public function setImageId($imageId)
  {
    $this->imageId = $imageId;
  }
  /**
   * @return string
   */
  public function getImageId()
  {
    return $this->imageId;
  }
  /**
   * Container labels, as provided by the container runtime.
   *
   * @param Label[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return Label[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Name of the container.
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
   * Container image URI provided when configuring a pod or container. This
   * string can identify a container image version using mutable tags.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Container::class, 'Google_Service_SecurityCommandCenter_Container');

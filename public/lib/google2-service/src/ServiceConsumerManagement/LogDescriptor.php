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

namespace Google\Service\ServiceConsumerManagement;

class LogDescriptor extends \Google\Collection
{
  protected $collection_key = 'labels';
  /**
   * A human-readable description of this log. This information appears in the
   * documentation and can contain details.
   *
   * @var string
   */
  public $description;
  /**
   * The human-readable name for this log. This information appears on the user
   * interface and should be concise.
   *
   * @var string
   */
  public $displayName;
  protected $labelsType = LabelDescriptor::class;
  protected $labelsDataType = 'array';
  /**
   * The name of the log. It must be less than 512 characters long and can
   * include the following characters: upper- and lower-case alphanumeric
   * characters [A-Za-z0-9], and punctuation characters including slash,
   * underscore, hyphen, period [/_-.].
   *
   * @var string
   */
  public $name;

  /**
   * A human-readable description of this log. This information appears in the
   * documentation and can contain details.
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
   * The human-readable name for this log. This information appears on the user
   * interface and should be concise.
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
   * The set of labels that are available to describe a specific log entry.
   * Runtime requests that contain labels not specified here are considered
   * invalid.
   *
   * @param LabelDescriptor[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return LabelDescriptor[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The name of the log. It must be less than 512 characters long and can
   * include the following characters: upper- and lower-case alphanumeric
   * characters [A-Za-z0-9], and punctuation characters including slash,
   * underscore, hyphen, period [/_-.].
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LogDescriptor::class, 'Google_Service_ServiceConsumerManagement_LogDescriptor');

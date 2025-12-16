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

class Framework extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const TYPE_FRAMEWORK_TYPE_UNSPECIFIED = 'FRAMEWORK_TYPE_UNSPECIFIED';
  /**
   * The framework is a built-in framework if it is created and managed by GCP.
   */
  public const TYPE_FRAMEWORK_TYPE_BUILT_IN = 'FRAMEWORK_TYPE_BUILT_IN';
  /**
   * The framework is a custom framework if it is created and managed by the
   * user.
   */
  public const TYPE_FRAMEWORK_TYPE_CUSTOM = 'FRAMEWORK_TYPE_CUSTOM';
  protected $collection_key = 'controls';
  /**
   * Category of the framework associated with the finding. E.g. Security
   * Benchmark, or Assured Workloads
   *
   * @var string[]
   */
  public $category;
  protected $controlsType = Control::class;
  protected $controlsDataType = 'array';
  /**
   * Display name of the framework. For a standard framework, this will look
   * like e.g. PCI DSS 3.2.1, whereas for a custom framework it can be a user
   * defined string like MyFramework
   *
   * @var string
   */
  public $displayName;
  /**
   * Name of the framework associated with the finding
   *
   * @var string
   */
  public $name;
  /**
   * Type of the framework associated with the finding, to specify whether the
   * framework is built-in (pre-defined and immutable) or a custom framework
   * defined by the customer (equivalent to security posture)
   *
   * @var string
   */
  public $type;

  /**
   * Category of the framework associated with the finding. E.g. Security
   * Benchmark, or Assured Workloads
   *
   * @param string[] $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string[]
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The controls associated with the framework.
   *
   * @param Control[] $controls
   */
  public function setControls($controls)
  {
    $this->controls = $controls;
  }
  /**
   * @return Control[]
   */
  public function getControls()
  {
    return $this->controls;
  }
  /**
   * Display name of the framework. For a standard framework, this will look
   * like e.g. PCI DSS 3.2.1, whereas for a custom framework it can be a user
   * defined string like MyFramework
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
   * Name of the framework associated with the finding
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
   * Type of the framework associated with the finding, to specify whether the
   * framework is built-in (pre-defined and immutable) or a custom framework
   * defined by the customer (equivalent to security posture)
   *
   * Accepted values: FRAMEWORK_TYPE_UNSPECIFIED, FRAMEWORK_TYPE_BUILT_IN,
   * FRAMEWORK_TYPE_CUSTOM
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
class_alias(Framework::class, 'Google_Service_SecurityCommandCenter_Framework');

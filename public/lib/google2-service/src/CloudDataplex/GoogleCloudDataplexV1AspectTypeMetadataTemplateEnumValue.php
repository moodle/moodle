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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1AspectTypeMetadataTemplateEnumValue extends \Google\Model
{
  /**
   * Optional. You can set this message if you need to deprecate an enum value.
   *
   * @var string
   */
  public $deprecated;
  /**
   * Required. Index for the enum value. It can't be modified.
   *
   * @var int
   */
  public $index;
  /**
   * Required. Name of the enumvalue. This is the actual value that the aspect
   * can contain.
   *
   * @var string
   */
  public $name;

  /**
   * Optional. You can set this message if you need to deprecate an enum value.
   *
   * @param string $deprecated
   */
  public function setDeprecated($deprecated)
  {
    $this->deprecated = $deprecated;
  }
  /**
   * @return string
   */
  public function getDeprecated()
  {
    return $this->deprecated;
  }
  /**
   * Required. Index for the enum value. It can't be modified.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Required. Name of the enumvalue. This is the actual value that the aspect
   * can contain.
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
class_alias(GoogleCloudDataplexV1AspectTypeMetadataTemplateEnumValue::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1AspectTypeMetadataTemplateEnumValue');

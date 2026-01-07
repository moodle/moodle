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

namespace Google\Service\Compute;

class MetadataFilterLabelMatch extends \Google\Model
{
  /**
   * Name of metadata label.
   *
   *  The name can have a maximum length of 1024 characters and must be at least
   * 1 character long.
   *
   * @var string
   */
  public $name;
  /**
   * The value of the label must match the specified value.
   *
   * value can have a maximum length of 1024 characters.
   *
   * @var string
   */
  public $value;

  /**
   * Name of metadata label.
   *
   *  The name can have a maximum length of 1024 characters and must be at least
   * 1 character long.
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
   * The value of the label must match the specified value.
   *
   * value can have a maximum length of 1024 characters.
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
class_alias(MetadataFilterLabelMatch::class, 'Google_Service_Compute_MetadataFilterLabelMatch');

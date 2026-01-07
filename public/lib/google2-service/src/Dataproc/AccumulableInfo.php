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

namespace Google\Service\Dataproc;

class AccumulableInfo extends \Google\Model
{
  /**
   * @var string
   */
  public $accumullableInfoId;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $update;
  /**
   * @var string
   */
  public $value;

  /**
   * @param string $accumullableInfoId
   */
  public function setAccumullableInfoId($accumullableInfoId)
  {
    $this->accumullableInfoId = $accumullableInfoId;
  }
  /**
   * @return string
   */
  public function getAccumullableInfoId()
  {
    return $this->accumullableInfoId;
  }
  /**
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
   * @param string $update
   */
  public function setUpdate($update)
  {
    $this->update = $update;
  }
  /**
   * @return string
   */
  public function getUpdate()
  {
    return $this->update;
  }
  /**
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
class_alias(AccumulableInfo::class, 'Google_Service_Dataproc_AccumulableInfo');

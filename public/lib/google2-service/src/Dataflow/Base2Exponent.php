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

namespace Google\Service\Dataflow;

class Base2Exponent extends \Google\Model
{
  /**
   * Must be greater than 0.
   *
   * @var int
   */
  public $numberOfBuckets;
  /**
   * Must be between -3 and 3. This forces the growth factor of the bucket
   * boundaries to be between `2^(1/8)` and `256`.
   *
   * @var int
   */
  public $scale;

  /**
   * Must be greater than 0.
   *
   * @param int $numberOfBuckets
   */
  public function setNumberOfBuckets($numberOfBuckets)
  {
    $this->numberOfBuckets = $numberOfBuckets;
  }
  /**
   * @return int
   */
  public function getNumberOfBuckets()
  {
    return $this->numberOfBuckets;
  }
  /**
   * Must be between -3 and 3. This forces the growth factor of the bucket
   * boundaries to be between `2^(1/8)` and `256`.
   *
   * @param int $scale
   */
  public function setScale($scale)
  {
    $this->scale = $scale;
  }
  /**
   * @return int
   */
  public function getScale()
  {
    return $this->scale;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Base2Exponent::class, 'Google_Service_Dataflow_Base2Exponent');

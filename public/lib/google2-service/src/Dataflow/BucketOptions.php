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

class BucketOptions extends \Google\Model
{
  protected $exponentialType = Base2Exponent::class;
  protected $exponentialDataType = '';
  protected $linearType = Linear::class;
  protected $linearDataType = '';

  /**
   * Bucket boundaries grow exponentially.
   *
   * @param Base2Exponent $exponential
   */
  public function setExponential(Base2Exponent $exponential)
  {
    $this->exponential = $exponential;
  }
  /**
   * @return Base2Exponent
   */
  public function getExponential()
  {
    return $this->exponential;
  }
  /**
   * Bucket boundaries grow linearly.
   *
   * @param Linear $linear
   */
  public function setLinear(Linear $linear)
  {
    $this->linear = $linear;
  }
  /**
   * @return Linear
   */
  public function getLinear()
  {
    return $this->linear;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BucketOptions::class, 'Google_Service_Dataflow_BucketOptions');

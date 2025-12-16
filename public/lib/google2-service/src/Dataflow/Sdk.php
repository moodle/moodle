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

class Sdk extends \Google\Collection
{
  protected $collection_key = 'stacks';
  /**
   * The SDK harness id.
   *
   * @var string
   */
  public $sdkId;
  protected $stacksType = Stack::class;
  protected $stacksDataType = 'array';

  /**
   * The SDK harness id.
   *
   * @param string $sdkId
   */
  public function setSdkId($sdkId)
  {
    $this->sdkId = $sdkId;
  }
  /**
   * @return string
   */
  public function getSdkId()
  {
    return $this->sdkId;
  }
  /**
   * The stacktraces for the processes running on the SDK harness.
   *
   * @param Stack[] $stacks
   */
  public function setStacks($stacks)
  {
    $this->stacks = $stacks;
  }
  /**
   * @return Stack[]
   */
  public function getStacks()
  {
    return $this->stacks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Sdk::class, 'Google_Service_Dataflow_Sdk');

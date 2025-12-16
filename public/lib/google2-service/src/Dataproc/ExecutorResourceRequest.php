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

class ExecutorResourceRequest extends \Google\Model
{
  /**
   * @var string
   */
  public $amount;
  /**
   * @var string
   */
  public $discoveryScript;
  /**
   * @var string
   */
  public $resourceName;
  /**
   * @var string
   */
  public $vendor;

  /**
   * @param string $amount
   */
  public function setAmount($amount)
  {
    $this->amount = $amount;
  }
  /**
   * @return string
   */
  public function getAmount()
  {
    return $this->amount;
  }
  /**
   * @param string $discoveryScript
   */
  public function setDiscoveryScript($discoveryScript)
  {
    $this->discoveryScript = $discoveryScript;
  }
  /**
   * @return string
   */
  public function getDiscoveryScript()
  {
    return $this->discoveryScript;
  }
  /**
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * @param string $vendor
   */
  public function setVendor($vendor)
  {
    $this->vendor = $vendor;
  }
  /**
   * @return string
   */
  public function getVendor()
  {
    return $this->vendor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecutorResourceRequest::class, 'Google_Service_Dataproc_ExecutorResourceRequest');

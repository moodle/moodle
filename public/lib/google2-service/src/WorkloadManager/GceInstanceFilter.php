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

namespace Google\Service\WorkloadManager;

class GceInstanceFilter extends \Google\Collection
{
  protected $collection_key = 'serviceAccounts';
  /**
   * Service account of compute engine
   *
   * @var string[]
   */
  public $serviceAccounts;

  /**
   * Service account of compute engine
   *
   * @param string[] $serviceAccounts
   */
  public function setServiceAccounts($serviceAccounts)
  {
    $this->serviceAccounts = $serviceAccounts;
  }
  /**
   * @return string[]
   */
  public function getServiceAccounts()
  {
    return $this->serviceAccounts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GceInstanceFilter::class, 'Google_Service_WorkloadManager_GceInstanceFilter');

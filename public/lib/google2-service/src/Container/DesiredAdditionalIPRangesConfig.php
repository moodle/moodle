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

namespace Google\Service\Container;

class DesiredAdditionalIPRangesConfig extends \Google\Collection
{
  protected $collection_key = 'additionalIpRangesConfigs';
  protected $additionalIpRangesConfigsType = AdditionalIPRangesConfig::class;
  protected $additionalIpRangesConfigsDataType = 'array';

  /**
   * List of additional IP ranges configs where each AdditionalIPRangesConfig
   * corresponds to one subnetwork's IP ranges
   *
   * @param AdditionalIPRangesConfig[] $additionalIpRangesConfigs
   */
  public function setAdditionalIpRangesConfigs($additionalIpRangesConfigs)
  {
    $this->additionalIpRangesConfigs = $additionalIpRangesConfigs;
  }
  /**
   * @return AdditionalIPRangesConfig[]
   */
  public function getAdditionalIpRangesConfigs()
  {
    return $this->additionalIpRangesConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DesiredAdditionalIPRangesConfig::class, 'Google_Service_Container_DesiredAdditionalIPRangesConfig');

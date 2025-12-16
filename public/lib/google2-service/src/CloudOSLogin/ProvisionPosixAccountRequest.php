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

namespace Google\Service\CloudOSLogin;

class ProvisionPosixAccountRequest extends \Google\Collection
{
  protected $collection_key = 'regions';
  /**
   * Optional. The regions to wait for a POSIX account to be written to before
   * returning a response. If unspecified, defaults to all regions. Regions are
   * listed at https://cloud.google.com/about/locations#region.
   *
   * @var string[]
   */
  public $regions;

  /**
   * Optional. The regions to wait for a POSIX account to be written to before
   * returning a response. If unspecified, defaults to all regions. Regions are
   * listed at https://cloud.google.com/about/locations#region.
   *
   * @param string[] $regions
   */
  public function setRegions($regions)
  {
    $this->regions = $regions;
  }
  /**
   * @return string[]
   */
  public function getRegions()
  {
    return $this->regions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProvisionPosixAccountRequest::class, 'Google_Service_CloudOSLogin_ProvisionPosixAccountRequest');

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

namespace Google\Service\ServiceConsumerManagement;

class CreateTenancyUnitRequest extends \Google\Model
{
  /**
   * Optional. Optional service producer-provided identifier of the tenancy
   * unit. Must be no longer than 40 characters and preferably URI friendly. If
   * it isn't provided, a UID for the tenancy unit is automatically generated.
   * The identifier must be unique across a managed service. If the tenancy unit
   * already exists for the managed service and service consumer pair, calling
   * `CreateTenancyUnit` returns the existing tenancy unit if the provided
   * identifier is identical or empty, otherwise the call fails.
   *
   * @var string
   */
  public $tenancyUnitId;

  /**
   * Optional. Optional service producer-provided identifier of the tenancy
   * unit. Must be no longer than 40 characters and preferably URI friendly. If
   * it isn't provided, a UID for the tenancy unit is automatically generated.
   * The identifier must be unique across a managed service. If the tenancy unit
   * already exists for the managed service and service consumer pair, calling
   * `CreateTenancyUnit` returns the existing tenancy unit if the provided
   * identifier is identical or empty, otherwise the call fails.
   *
   * @param string $tenancyUnitId
   */
  public function setTenancyUnitId($tenancyUnitId)
  {
    $this->tenancyUnitId = $tenancyUnitId;
  }
  /**
   * @return string
   */
  public function getTenancyUnitId()
  {
    return $this->tenancyUnitId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateTenancyUnitRequest::class, 'Google_Service_ServiceConsumerManagement_CreateTenancyUnitRequest');

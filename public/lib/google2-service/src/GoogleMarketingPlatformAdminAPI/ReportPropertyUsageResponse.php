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

namespace Google\Service\GoogleMarketingPlatformAdminAPI;

class ReportPropertyUsageResponse extends \Google\Collection
{
  protected $collection_key = 'propertyUsages';
  protected $billInfoType = BillInfo::class;
  protected $billInfoDataType = '';
  protected $propertyUsagesType = PropertyUsage::class;
  protected $propertyUsagesDataType = 'array';

  /**
   * Bill amount in the specified organization and month. Will be empty if user
   * only has access to usage data.
   *
   * @param BillInfo $billInfo
   */
  public function setBillInfo(BillInfo $billInfo)
  {
    $this->billInfo = $billInfo;
  }
  /**
   * @return BillInfo
   */
  public function getBillInfo()
  {
    return $this->billInfo;
  }
  /**
   * Usage data for all properties in the specified organization and month.
   *
   * @param PropertyUsage[] $propertyUsages
   */
  public function setPropertyUsages($propertyUsages)
  {
    $this->propertyUsages = $propertyUsages;
  }
  /**
   * @return PropertyUsage[]
   */
  public function getPropertyUsages()
  {
    return $this->propertyUsages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportPropertyUsageResponse::class, 'Google_Service_GoogleMarketingPlatformAdminAPI_ReportPropertyUsageResponse');

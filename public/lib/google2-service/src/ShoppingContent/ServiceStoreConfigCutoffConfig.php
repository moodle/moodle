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

namespace Google\Service\ShoppingContent;

class ServiceStoreConfigCutoffConfig extends \Google\Model
{
  protected $localCutoffTimeType = ServiceStoreConfigCutoffConfigLocalCutoffTime::class;
  protected $localCutoffTimeDataType = '';
  /**
   * Merchants can opt-out of showing n+1 day local delivery when they have a
   * shipping service configured to n day local delivery. For example, if the
   * shipping service defines same-day delivery, and it's past the cut-off,
   * setting this field to `true` results in the calculated shipping service
   * rate returning `NO_DELIVERY_POST_CUTOFF`. In the same example, setting this
   * field to `false` results in the calculated shipping time being one day.
   * This is only for local delivery.
   *
   * @var bool
   */
  public $noDeliveryPostCutoff;
  /**
   * Represents cutoff time as the number of hours before store closing.
   * Mutually exclusive with other fields (hour and minute).
   *
   * @var string
   */
  public $storeCloseOffsetHours;

  /**
   * Time in hours and minutes in the local timezone when local delivery ends.
   *
   * @param ServiceStoreConfigCutoffConfigLocalCutoffTime $localCutoffTime
   */
  public function setLocalCutoffTime(ServiceStoreConfigCutoffConfigLocalCutoffTime $localCutoffTime)
  {
    $this->localCutoffTime = $localCutoffTime;
  }
  /**
   * @return ServiceStoreConfigCutoffConfigLocalCutoffTime
   */
  public function getLocalCutoffTime()
  {
    return $this->localCutoffTime;
  }
  /**
   * Merchants can opt-out of showing n+1 day local delivery when they have a
   * shipping service configured to n day local delivery. For example, if the
   * shipping service defines same-day delivery, and it's past the cut-off,
   * setting this field to `true` results in the calculated shipping service
   * rate returning `NO_DELIVERY_POST_CUTOFF`. In the same example, setting this
   * field to `false` results in the calculated shipping time being one day.
   * This is only for local delivery.
   *
   * @param bool $noDeliveryPostCutoff
   */
  public function setNoDeliveryPostCutoff($noDeliveryPostCutoff)
  {
    $this->noDeliveryPostCutoff = $noDeliveryPostCutoff;
  }
  /**
   * @return bool
   */
  public function getNoDeliveryPostCutoff()
  {
    return $this->noDeliveryPostCutoff;
  }
  /**
   * Represents cutoff time as the number of hours before store closing.
   * Mutually exclusive with other fields (hour and minute).
   *
   * @param string $storeCloseOffsetHours
   */
  public function setStoreCloseOffsetHours($storeCloseOffsetHours)
  {
    $this->storeCloseOffsetHours = $storeCloseOffsetHours;
  }
  /**
   * @return string
   */
  public function getStoreCloseOffsetHours()
  {
    return $this->storeCloseOffsetHours;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceStoreConfigCutoffConfig::class, 'Google_Service_ShoppingContent_ServiceStoreConfigCutoffConfig');

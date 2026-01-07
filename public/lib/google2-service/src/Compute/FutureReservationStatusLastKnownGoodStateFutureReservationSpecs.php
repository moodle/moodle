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

namespace Google\Service\Compute;

class FutureReservationStatusLastKnownGoodStateFutureReservationSpecs extends \Google\Model
{
  protected $shareSettingsType = ShareSettings::class;
  protected $shareSettingsDataType = '';
  protected $specificSkuPropertiesType = FutureReservationSpecificSKUProperties::class;
  protected $specificSkuPropertiesDataType = '';
  protected $timeWindowType = FutureReservationTimeWindow::class;
  protected $timeWindowDataType = '';

  /**
   * Output only. [Output Only] The previous share settings of the Future
   * Reservation.
   *
   * @param ShareSettings $shareSettings
   */
  public function setShareSettings(ShareSettings $shareSettings)
  {
    $this->shareSettings = $shareSettings;
  }
  /**
   * @return ShareSettings
   */
  public function getShareSettings()
  {
    return $this->shareSettings;
  }
  /**
   * Output only. [Output Only] The previous instance related properties of the
   * Future Reservation.
   *
   * @param FutureReservationSpecificSKUProperties $specificSkuProperties
   */
  public function setSpecificSkuProperties(FutureReservationSpecificSKUProperties $specificSkuProperties)
  {
    $this->specificSkuProperties = $specificSkuProperties;
  }
  /**
   * @return FutureReservationSpecificSKUProperties
   */
  public function getSpecificSkuProperties()
  {
    return $this->specificSkuProperties;
  }
  /**
   * Output only. [Output Only] The previous time window of the Future
   * Reservation.
   *
   * @param FutureReservationTimeWindow $timeWindow
   */
  public function setTimeWindow(FutureReservationTimeWindow $timeWindow)
  {
    $this->timeWindow = $timeWindow;
  }
  /**
   * @return FutureReservationTimeWindow
   */
  public function getTimeWindow()
  {
    return $this->timeWindow;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureReservationStatusLastKnownGoodStateFutureReservationSpecs::class, 'Google_Service_Compute_FutureReservationStatusLastKnownGoodStateFutureReservationSpecs');

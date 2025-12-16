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

namespace Google\Service\Monitoring;

class ForecastOptions extends \Google\Model
{
  /**
   * Required. The length of time into the future to forecast whether a time
   * series will violate the threshold. If the predicted value is found to
   * violate the threshold, and the violation is observed in all forecasts made
   * for the configured duration, then the time series is considered to be
   * failing. The forecast horizon can range from 1 hour to 60 hours.
   *
   * @var string
   */
  public $forecastHorizon;

  /**
   * Required. The length of time into the future to forecast whether a time
   * series will violate the threshold. If the predicted value is found to
   * violate the threshold, and the violation is observed in all forecasts made
   * for the configured duration, then the time series is considered to be
   * failing. The forecast horizon can range from 1 hour to 60 hours.
   *
   * @param string $forecastHorizon
   */
  public function setForecastHorizon($forecastHorizon)
  {
    $this->forecastHorizon = $forecastHorizon;
  }
  /**
   * @return string
   */
  public function getForecastHorizon()
  {
    return $this->forecastHorizon;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ForecastOptions::class, 'Google_Service_Monitoring_ForecastOptions');

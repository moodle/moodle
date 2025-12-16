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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DeidentifyDataSourceDetails extends \Google\Model
{
  protected $deidentifyStatsType = GooglePrivacyDlpV2DeidentifyDataSourceStats::class;
  protected $deidentifyStatsDataType = '';
  protected $requestedOptionsType = GooglePrivacyDlpV2RequestedDeidentifyOptions::class;
  protected $requestedOptionsDataType = '';

  /**
   * Stats about the de-identification operation.
   *
   * @param GooglePrivacyDlpV2DeidentifyDataSourceStats $deidentifyStats
   */
  public function setDeidentifyStats(GooglePrivacyDlpV2DeidentifyDataSourceStats $deidentifyStats)
  {
    $this->deidentifyStats = $deidentifyStats;
  }
  /**
   * @return GooglePrivacyDlpV2DeidentifyDataSourceStats
   */
  public function getDeidentifyStats()
  {
    return $this->deidentifyStats;
  }
  /**
   * De-identification config used for the request.
   *
   * @param GooglePrivacyDlpV2RequestedDeidentifyOptions $requestedOptions
   */
  public function setRequestedOptions(GooglePrivacyDlpV2RequestedDeidentifyOptions $requestedOptions)
  {
    $this->requestedOptions = $requestedOptions;
  }
  /**
   * @return GooglePrivacyDlpV2RequestedDeidentifyOptions
   */
  public function getRequestedOptions()
  {
    return $this->requestedOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DeidentifyDataSourceDetails::class, 'Google_Service_DLP_GooglePrivacyDlpV2DeidentifyDataSourceDetails');

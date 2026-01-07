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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1TuneQaScorecardRevisionRequest extends \Google\Model
{
  /**
   * Required. Filter for selecting the feedback labels that needs to be used
   * for training. This filter can be used to limit the feedback labels used for
   * tuning to a feedback labels created or updated for a specific time-window
   * etc.
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. Run in validate only mode, no fine tuning will actually run. Data
   * quality validations like training data distributions will run. Even when
   * set to false, the data quality validations will still run but once the
   * validations complete we will proceed with the fine tune, if applicable.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Required. Filter for selecting the feedback labels that needs to be used
   * for training. This filter can be used to limit the feedback labels used for
   * tuning to a feedback labels created or updated for a specific time-window
   * etc.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. Run in validate only mode, no fine tuning will actually run. Data
   * quality validations like training data distributions will run. Even when
   * set to false, the data quality validations will still run but once the
   * validations complete we will proceed with the fine tune, if applicable.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1TuneQaScorecardRevisionRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1TuneQaScorecardRevisionRequest');

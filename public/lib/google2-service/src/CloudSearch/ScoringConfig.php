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

namespace Google\Service\CloudSearch;

class ScoringConfig extends \Google\Model
{
  /**
   * Whether to use freshness as a ranking signal. By default, freshness is used
   * as a ranking signal. Note that this setting is not available in the Admin
   * UI.
   *
   * @var bool
   */
  public $disableFreshness;
  /**
   * Whether to personalize the results. By default, personal signals will be
   * used to boost results.
   *
   * @var bool
   */
  public $disablePersonalization;

  /**
   * Whether to use freshness as a ranking signal. By default, freshness is used
   * as a ranking signal. Note that this setting is not available in the Admin
   * UI.
   *
   * @param bool $disableFreshness
   */
  public function setDisableFreshness($disableFreshness)
  {
    $this->disableFreshness = $disableFreshness;
  }
  /**
   * @return bool
   */
  public function getDisableFreshness()
  {
    return $this->disableFreshness;
  }
  /**
   * Whether to personalize the results. By default, personal signals will be
   * used to boost results.
   *
   * @param bool $disablePersonalization
   */
  public function setDisablePersonalization($disablePersonalization)
  {
    $this->disablePersonalization = $disablePersonalization;
  }
  /**
   * @return bool
   */
  public function getDisablePersonalization()
  {
    return $this->disablePersonalization;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScoringConfig::class, 'Google_Service_CloudSearch_ScoringConfig');

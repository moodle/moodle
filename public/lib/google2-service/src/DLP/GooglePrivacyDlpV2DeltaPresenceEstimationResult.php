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

class GooglePrivacyDlpV2DeltaPresenceEstimationResult extends \Google\Collection
{
  protected $collection_key = 'deltaPresenceEstimationHistogram';
  protected $deltaPresenceEstimationHistogramType = GooglePrivacyDlpV2DeltaPresenceEstimationHistogramBucket::class;
  protected $deltaPresenceEstimationHistogramDataType = 'array';

  /**
   * The intervals [min_probability, max_probability) do not overlap. If a value
   * doesn't correspond to any such interval, the associated frequency is zero.
   * For example, the following records: {min_probability: 0, max_probability:
   * 0.1, frequency: 17} {min_probability: 0.2, max_probability: 0.3, frequency:
   * 42} {min_probability: 0.3, max_probability: 0.4, frequency: 99} mean that
   * there are no record with an estimated probability in [0.1, 0.2) nor larger
   * or equal to 0.4.
   *
   * @param GooglePrivacyDlpV2DeltaPresenceEstimationHistogramBucket[] $deltaPresenceEstimationHistogram
   */
  public function setDeltaPresenceEstimationHistogram($deltaPresenceEstimationHistogram)
  {
    $this->deltaPresenceEstimationHistogram = $deltaPresenceEstimationHistogram;
  }
  /**
   * @return GooglePrivacyDlpV2DeltaPresenceEstimationHistogramBucket[]
   */
  public function getDeltaPresenceEstimationHistogram()
  {
    return $this->deltaPresenceEstimationHistogram;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DeltaPresenceEstimationResult::class, 'Google_Service_DLP_GooglePrivacyDlpV2DeltaPresenceEstimationResult');

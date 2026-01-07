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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaSearchRequestCrowdingSpec extends \Google\Model
{
  /**
   * Unspecified crowding mode. In this case, server behavior defaults to
   * Mode.DROP_CROWDED_RESULTS.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Drop crowded results.
   */
  public const MODE_DROP_CROWDED_RESULTS = 'DROP_CROWDED_RESULTS';
  /**
   * Demote crowded results to the later pages.
   */
  public const MODE_DEMOTE_CROWDED_RESULTS_TO_END = 'DEMOTE_CROWDED_RESULTS_TO_END';
  /**
   * The field to use for crowding. Documents can be crowded by a field in the
   * Document object. Crowding field is case sensitive.
   *
   * @var string
   */
  public $field;
  /**
   * The maximum number of documents to keep per value of the field. Once there
   * are at least max_count previous results which contain the same value for
   * the given field (according to the order specified in `order_by`), later
   * results with the same value are "crowded away". If not specified, the
   * default value is 1.
   *
   * @var int
   */
  public $maxCount;
  /**
   * Mode to use for documents that are crowded away.
   *
   * @var string
   */
  public $mode;

  /**
   * The field to use for crowding. Documents can be crowded by a field in the
   * Document object. Crowding field is case sensitive.
   *
   * @param string $field
   */
  public function setField($field)
  {
    $this->field = $field;
  }
  /**
   * @return string
   */
  public function getField()
  {
    return $this->field;
  }
  /**
   * The maximum number of documents to keep per value of the field. Once there
   * are at least max_count previous results which contain the same value for
   * the given field (according to the order specified in `order_by`), later
   * results with the same value are "crowded away". If not specified, the
   * default value is 1.
   *
   * @param int $maxCount
   */
  public function setMaxCount($maxCount)
  {
    $this->maxCount = $maxCount;
  }
  /**
   * @return int
   */
  public function getMaxCount()
  {
    return $this->maxCount;
  }
  /**
   * Mode to use for documents that are crowded away.
   *
   * Accepted values: MODE_UNSPECIFIED, DROP_CROWDED_RESULTS,
   * DEMOTE_CROWDED_RESULTS_TO_END
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaSearchRequestCrowdingSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchRequestCrowdingSpec');

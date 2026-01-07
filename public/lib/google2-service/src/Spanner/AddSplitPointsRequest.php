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

namespace Google\Service\Spanner;

class AddSplitPointsRequest extends \Google\Collection
{
  protected $collection_key = 'splitPoints';
  /**
   * Optional. A user-supplied tag associated with the split points. For
   * example, "initial_data_load", "special_event_1". Defaults to
   * "CloudAddSplitPointsAPI" if not specified. The length of the tag must not
   * exceed 50 characters, or else it is trimmed. Only valid UTF8 characters are
   * allowed.
   *
   * @var string
   */
  public $initiator;
  protected $splitPointsType = SplitPoints::class;
  protected $splitPointsDataType = 'array';

  /**
   * Optional. A user-supplied tag associated with the split points. For
   * example, "initial_data_load", "special_event_1". Defaults to
   * "CloudAddSplitPointsAPI" if not specified. The length of the tag must not
   * exceed 50 characters, or else it is trimmed. Only valid UTF8 characters are
   * allowed.
   *
   * @param string $initiator
   */
  public function setInitiator($initiator)
  {
    $this->initiator = $initiator;
  }
  /**
   * @return string
   */
  public function getInitiator()
  {
    return $this->initiator;
  }
  /**
   * Required. The split points to add.
   *
   * @param SplitPoints[] $splitPoints
   */
  public function setSplitPoints($splitPoints)
  {
    $this->splitPoints = $splitPoints;
  }
  /**
   * @return SplitPoints[]
   */
  public function getSplitPoints()
  {
    return $this->splitPoints;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddSplitPointsRequest::class, 'Google_Service_Spanner_AddSplitPointsRequest');

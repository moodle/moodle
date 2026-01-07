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

namespace Google\Service\BackupforGKE;

class ListRestorePlanBindingsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * A token which may be sent as page_token in a subsequent
   * `ListRestorePlanBindings` call to retrieve the next page of results. If
   * this field is omitted or empty, then there are no more results to return.
   *
   * @var string
   */
  public $nextPageToken;
  protected $restorePlanBindingsType = RestorePlanBinding::class;
  protected $restorePlanBindingsDataType = 'array';
  /**
   * Unordered list. Locations that could not be reached.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * A token which may be sent as page_token in a subsequent
   * `ListRestorePlanBindings` call to retrieve the next page of results. If
   * this field is omitted or empty, then there are no more results to return.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * The list of RestorePlanBindings matching the given criteria.
   *
   * @param RestorePlanBinding[] $restorePlanBindings
   */
  public function setRestorePlanBindings($restorePlanBindings)
  {
    $this->restorePlanBindings = $restorePlanBindings;
  }
  /**
   * @return RestorePlanBinding[]
   */
  public function getRestorePlanBindings()
  {
    return $this->restorePlanBindings;
  }
  /**
   * Unordered list. Locations that could not be reached.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListRestorePlanBindingsResponse::class, 'Google_Service_BackupforGKE_ListRestorePlanBindingsResponse');

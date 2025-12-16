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

namespace Google\Service\Bigquery;

class ListRoutinesResponse extends \Google\Collection
{
  protected $collection_key = 'routines';
  /**
   * A token to request the next page of results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $routinesType = Routine::class;
  protected $routinesDataType = 'array';

  /**
   * A token to request the next page of results.
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
   * Routines in the requested dataset. Unless read_mask is set in the request,
   * only the following fields are populated: etag, project_id, dataset_id,
   * routine_id, routine_type, creation_time, last_modified_time, language, and
   * remote_function_options.
   *
   * @param Routine[] $routines
   */
  public function setRoutines($routines)
  {
    $this->routines = $routines;
  }
  /**
   * @return Routine[]
   */
  public function getRoutines()
  {
    return $this->routines;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListRoutinesResponse::class, 'Google_Service_Bigquery_ListRoutinesResponse');

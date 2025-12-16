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

namespace Google\Service\CloudTasks;

class QueryOverride extends \Google\Model
{
  /**
   * The query parameters (e.g., qparam1=123&qparam2=456). Default is an empty
   * string.
   *
   * @var string
   */
  public $queryParams;

  /**
   * The query parameters (e.g., qparam1=123&qparam2=456). Default is an empty
   * string.
   *
   * @param string $queryParams
   */
  public function setQueryParams($queryParams)
  {
    $this->queryParams = $queryParams;
  }
  /**
   * @return string
   */
  public function getQueryParams()
  {
    return $this->queryParams;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryOverride::class, 'Google_Service_CloudTasks_QueryOverride');

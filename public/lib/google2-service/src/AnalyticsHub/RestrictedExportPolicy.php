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

namespace Google\Service\AnalyticsHub;

class RestrictedExportPolicy extends \Google\Model
{
  /**
   * Optional. If true, enable restricted export.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Optional. If true, restrict direct table access (read api/tabledata.list)
   * on linked table.
   *
   * @var bool
   */
  public $restrictDirectTableAccess;
  /**
   * Optional. If true, restrict export of query result derived from restricted
   * linked dataset table.
   *
   * @var bool
   */
  public $restrictQueryResult;

  /**
   * Optional. If true, enable restricted export.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Optional. If true, restrict direct table access (read api/tabledata.list)
   * on linked table.
   *
   * @param bool $restrictDirectTableAccess
   */
  public function setRestrictDirectTableAccess($restrictDirectTableAccess)
  {
    $this->restrictDirectTableAccess = $restrictDirectTableAccess;
  }
  /**
   * @return bool
   */
  public function getRestrictDirectTableAccess()
  {
    return $this->restrictDirectTableAccess;
  }
  /**
   * Optional. If true, restrict export of query result derived from restricted
   * linked dataset table.
   *
   * @param bool $restrictQueryResult
   */
  public function setRestrictQueryResult($restrictQueryResult)
  {
    $this->restrictQueryResult = $restrictQueryResult;
  }
  /**
   * @return bool
   */
  public function getRestrictQueryResult()
  {
    return $this->restrictQueryResult;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RestrictedExportPolicy::class, 'Google_Service_AnalyticsHub_RestrictedExportPolicy');

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

namespace Google\Service\DeploymentManager;

class DebugInfo extends \Google\Collection
{
  protected $collection_key = 'stackEntries';
  /**
   * Additional debugging information provided by the server.
   *
   * @var string
   */
  public $detail;
  /**
   * The stack trace entries indicating where the error occurred.
   *
   * @var string[]
   */
  public $stackEntries;

  /**
   * Additional debugging information provided by the server.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * The stack trace entries indicating where the error occurred.
   *
   * @param string[] $stackEntries
   */
  public function setStackEntries($stackEntries)
  {
    $this->stackEntries = $stackEntries;
  }
  /**
   * @return string[]
   */
  public function getStackEntries()
  {
    return $this->stackEntries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DebugInfo::class, 'Google_Service_DeploymentManager_DebugInfo');

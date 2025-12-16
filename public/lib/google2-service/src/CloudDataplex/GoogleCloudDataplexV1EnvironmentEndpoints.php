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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1EnvironmentEndpoints extends \Google\Model
{
  /**
   * Output only. URI to serve notebook APIs
   *
   * @var string
   */
  public $notebooks;
  /**
   * Output only. URI to serve SQL APIs
   *
   * @var string
   */
  public $sql;

  /**
   * Output only. URI to serve notebook APIs
   *
   * @param string $notebooks
   */
  public function setNotebooks($notebooks)
  {
    $this->notebooks = $notebooks;
  }
  /**
   * @return string
   */
  public function getNotebooks()
  {
    return $this->notebooks;
  }
  /**
   * Output only. URI to serve SQL APIs
   *
   * @param string $sql
   */
  public function setSql($sql)
  {
    $this->sql = $sql;
  }
  /**
   * @return string
   */
  public function getSql()
  {
    return $this->sql;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1EnvironmentEndpoints::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1EnvironmentEndpoints');

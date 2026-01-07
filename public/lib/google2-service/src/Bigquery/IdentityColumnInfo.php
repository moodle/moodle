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

class IdentityColumnInfo extends \Google\Model
{
  /**
   * @var string
   */
  public $generatedMode;
  /**
   * @var string
   */
  public $increment;
  /**
   * @var string
   */
  public $start;

  /**
   * @param string
   */
  public function setGeneratedMode($generatedMode)
  {
    $this->generatedMode = $generatedMode;
  }
  /**
   * @return string
   */
  public function getGeneratedMode()
  {
    return $this->generatedMode;
  }
  /**
   * @param string
   */
  public function setIncrement($increment)
  {
    $this->increment = $increment;
  }
  /**
   * @return string
   */
  public function getIncrement()
  {
    return $this->increment;
  }
  /**
   * @param string
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  /**
   * @return string
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentityColumnInfo::class, 'Google_Service_Bigquery_IdentityColumnInfo');

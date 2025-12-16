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

class IndexAdvice extends \Google\Collection
{
  protected $collection_key = 'ddl';
  /**
   * Optional. DDL statements to add new indexes that will improve the query.
   *
   * @var string[]
   */
  public $ddl;
  /**
   * Optional. Estimated latency improvement factor. For example if the query
   * currently takes 500 ms to run and the estimated latency with new indexes is
   * 100 ms this field will be 5.
   *
   * @var 
   */
  public $improvementFactor;

  /**
   * Optional. DDL statements to add new indexes that will improve the query.
   *
   * @param string[] $ddl
   */
  public function setDdl($ddl)
  {
    $this->ddl = $ddl;
  }
  /**
   * @return string[]
   */
  public function getDdl()
  {
    return $this->ddl;
  }
  public function setImprovementFactor($improvementFactor)
  {
    $this->improvementFactor = $improvementFactor;
  }
  public function getImprovementFactor()
  {
    return $this->improvementFactor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IndexAdvice::class, 'Google_Service_Spanner_IndexAdvice');

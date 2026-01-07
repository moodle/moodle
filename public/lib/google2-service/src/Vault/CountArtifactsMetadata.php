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

namespace Google\Service\Vault;

class CountArtifactsMetadata extends \Google\Model
{
  /**
   * End time of count operation. Available when operation is done.
   *
   * @var string
   */
  public $endTime;
  /**
   * The matter ID of the associated matter.
   *
   * @var string
   */
  public $matterId;
  protected $queryType = Query::class;
  protected $queryDataType = '';
  /**
   * Creation time of count operation.
   *
   * @var string
   */
  public $startTime;

  /**
   * End time of count operation. Available when operation is done.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The matter ID of the associated matter.
   *
   * @param string $matterId
   */
  public function setMatterId($matterId)
  {
    $this->matterId = $matterId;
  }
  /**
   * @return string
   */
  public function getMatterId()
  {
    return $this->matterId;
  }
  /**
   * The search query from the request.
   *
   * @param Query $query
   */
  public function setQuery(Query $query)
  {
    $this->query = $query;
  }
  /**
   * @return Query
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Creation time of count operation.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CountArtifactsMetadata::class, 'Google_Service_Vault_CountArtifactsMetadata');

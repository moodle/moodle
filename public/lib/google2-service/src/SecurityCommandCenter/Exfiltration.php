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

namespace Google\Service\SecurityCommandCenter;

class Exfiltration extends \Google\Collection
{
  protected $collection_key = 'targets';
  protected $sourcesType = ExfilResource::class;
  protected $sourcesDataType = 'array';
  protected $targetsType = ExfilResource::class;
  protected $targetsDataType = 'array';
  /**
   * Total exfiltrated bytes processed for the entire job.
   *
   * @var string
   */
  public $totalExfiltratedBytes;

  /**
   * If there are multiple sources, then the data is considered "joined" between
   * them. For instance, BigQuery can join multiple tables, and each table would
   * be considered a source.
   *
   * @param ExfilResource[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return ExfilResource[]
   */
  public function getSources()
  {
    return $this->sources;
  }
  /**
   * If there are multiple targets, each target would get a complete copy of the
   * "joined" source data.
   *
   * @param ExfilResource[] $targets
   */
  public function setTargets($targets)
  {
    $this->targets = $targets;
  }
  /**
   * @return ExfilResource[]
   */
  public function getTargets()
  {
    return $this->targets;
  }
  /**
   * Total exfiltrated bytes processed for the entire job.
   *
   * @param string $totalExfiltratedBytes
   */
  public function setTotalExfiltratedBytes($totalExfiltratedBytes)
  {
    $this->totalExfiltratedBytes = $totalExfiltratedBytes;
  }
  /**
   * @return string
   */
  public function getTotalExfiltratedBytes()
  {
    return $this->totalExfiltratedBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Exfiltration::class, 'Google_Service_SecurityCommandCenter_Exfiltration');

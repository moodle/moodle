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

class GoogleCloudDataplexV1ActionInvalidDataFormat extends \Google\Collection
{
  protected $collection_key = 'sampledDataLocations';
  /**
   * The expected data format of the entity.
   *
   * @var string
   */
  public $expectedFormat;
  /**
   * The new unexpected data format within the entity.
   *
   * @var string
   */
  public $newFormat;
  /**
   * The list of data locations sampled and used for format/schema inference.
   *
   * @var string[]
   */
  public $sampledDataLocations;

  /**
   * The expected data format of the entity.
   *
   * @param string $expectedFormat
   */
  public function setExpectedFormat($expectedFormat)
  {
    $this->expectedFormat = $expectedFormat;
  }
  /**
   * @return string
   */
  public function getExpectedFormat()
  {
    return $this->expectedFormat;
  }
  /**
   * The new unexpected data format within the entity.
   *
   * @param string $newFormat
   */
  public function setNewFormat($newFormat)
  {
    $this->newFormat = $newFormat;
  }
  /**
   * @return string
   */
  public function getNewFormat()
  {
    return $this->newFormat;
  }
  /**
   * The list of data locations sampled and used for format/schema inference.
   *
   * @param string[] $sampledDataLocations
   */
  public function setSampledDataLocations($sampledDataLocations)
  {
    $this->sampledDataLocations = $sampledDataLocations;
  }
  /**
   * @return string[]
   */
  public function getSampledDataLocations()
  {
    return $this->sampledDataLocations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1ActionInvalidDataFormat::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ActionInvalidDataFormat');

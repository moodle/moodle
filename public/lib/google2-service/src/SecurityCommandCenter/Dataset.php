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

class Dataset extends \Google\Model
{
  /**
   * The user defined display name of dataset, e.g. plants-dataset
   *
   * @var string
   */
  public $displayName;
  /**
   * Resource name of the dataset, e.g.
   * projects/{project}/locations/{location}/datasets/2094040236064505856
   *
   * @var string
   */
  public $name;
  /**
   * Data source, such as BigQuery source URI, e.g. bq://scc-nexus-
   * test.AIPPtest.gsod
   *
   * @var string
   */
  public $source;

  /**
   * The user defined display name of dataset, e.g. plants-dataset
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Resource name of the dataset, e.g.
   * projects/{project}/locations/{location}/datasets/2094040236064505856
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Data source, such as BigQuery source URI, e.g. bq://scc-nexus-
   * test.AIPPtest.gsod
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Dataset::class, 'Google_Service_SecurityCommandCenter_Dataset');

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

namespace Google\Service\Analytics;

class ExperimentVariations extends \Google\Model
{
  /**
   * The name of the variation. This field is required when creating an
   * experiment. This field may not be changed for an experiment whose status is
   * ENDED.
   *
   * @var string
   */
  public $name;
  /**
   * Status of the variation. Possible values: "ACTIVE", "INACTIVE". INACTIVE
   * variations are not served. This field may not be changed for an experiment
   * whose status is ENDED.
   *
   * @var string
   */
  public $status;
  /**
   * The URL of the variation. This field may not be changed for an experiment
   * whose status is RUNNING or ENDED.
   *
   * @var string
   */
  public $url;
  /**
   * Weight that this variation should receive. Only present if the experiment
   * is running. This field is read-only.
   *
   * @var 
   */
  public $weight;
  /**
   * True if the experiment has ended and this variation performed
   * (statistically) significantly better than the original. This field is read-
   * only.
   *
   * @var bool
   */
  public $won;

  /**
   * The name of the variation. This field is required when creating an
   * experiment. This field may not be changed for an experiment whose status is
   * ENDED.
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
   * Status of the variation. Possible values: "ACTIVE", "INACTIVE". INACTIVE
   * variations are not served. This field may not be changed for an experiment
   * whose status is ENDED.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The URL of the variation. This field may not be changed for an experiment
   * whose status is RUNNING or ENDED.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }
  public function getWeight()
  {
    return $this->weight;
  }
  /**
   * True if the experiment has ended and this variation performed
   * (statistically) significantly better than the original. This field is read-
   * only.
   *
   * @param bool $won
   */
  public function setWon($won)
  {
    $this->won = $won;
  }
  /**
   * @return bool
   */
  public function getWon()
  {
    return $this->won;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExperimentVariations::class, 'Google_Service_Analytics_ExperimentVariations');

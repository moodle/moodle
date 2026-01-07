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

namespace Google\Service\OnDemandScanning;

class Metadata extends \Google\Model
{
  /**
   * The timestamp of when the build completed.
   *
   * @var string
   */
  public $buildFinishedOn;
  /**
   * Identifies the particular build invocation, which can be useful for finding
   * associated logs or other ad-hoc analysis. The value SHOULD be globally
   * unique, per in-toto Provenance spec.
   *
   * @var string
   */
  public $buildInvocationId;
  /**
   * The timestamp of when the build started.
   *
   * @var string
   */
  public $buildStartedOn;
  protected $completenessType = Completeness::class;
  protected $completenessDataType = '';
  /**
   * If true, the builder claims that running the recipe on materials will
   * produce bit-for-bit identical output.
   *
   * @var bool
   */
  public $reproducible;

  /**
   * The timestamp of when the build completed.
   *
   * @param string $buildFinishedOn
   */
  public function setBuildFinishedOn($buildFinishedOn)
  {
    $this->buildFinishedOn = $buildFinishedOn;
  }
  /**
   * @return string
   */
  public function getBuildFinishedOn()
  {
    return $this->buildFinishedOn;
  }
  /**
   * Identifies the particular build invocation, which can be useful for finding
   * associated logs or other ad-hoc analysis. The value SHOULD be globally
   * unique, per in-toto Provenance spec.
   *
   * @param string $buildInvocationId
   */
  public function setBuildInvocationId($buildInvocationId)
  {
    $this->buildInvocationId = $buildInvocationId;
  }
  /**
   * @return string
   */
  public function getBuildInvocationId()
  {
    return $this->buildInvocationId;
  }
  /**
   * The timestamp of when the build started.
   *
   * @param string $buildStartedOn
   */
  public function setBuildStartedOn($buildStartedOn)
  {
    $this->buildStartedOn = $buildStartedOn;
  }
  /**
   * @return string
   */
  public function getBuildStartedOn()
  {
    return $this->buildStartedOn;
  }
  /**
   * Indicates that the builder claims certain fields in this message to be
   * complete.
   *
   * @param Completeness $completeness
   */
  public function setCompleteness(Completeness $completeness)
  {
    $this->completeness = $completeness;
  }
  /**
   * @return Completeness
   */
  public function getCompleteness()
  {
    return $this->completeness;
  }
  /**
   * If true, the builder claims that running the recipe on materials will
   * produce bit-for-bit identical output.
   *
   * @param bool $reproducible
   */
  public function setReproducible($reproducible)
  {
    $this->reproducible = $reproducible;
  }
  /**
   * @return bool
   */
  public function getReproducible()
  {
    return $this->reproducible;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Metadata::class, 'Google_Service_OnDemandScanning_Metadata');

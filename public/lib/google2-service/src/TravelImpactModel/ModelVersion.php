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

namespace Google\Service\TravelImpactModel;

class ModelVersion extends \Google\Model
{
  /**
   * Dated versions: Model datasets are recreated with refreshed input data but
   * no change to the algorithms regularly.
   *
   * @var string
   */
  public $dated;
  /**
   * Major versions: Major changes to methodology (e.g. adding new data sources
   * to the model that lead to major output changes). Such changes will be
   * infrequent and announced well in advance. Might involve API version
   * changes, which will respect [Google Cloud API
   * guidelines](https://cloud.google.com/endpoints/docs/openapi/versioning-an-
   * api#backwards-incompatible)
   *
   * @var int
   */
  public $major;
  /**
   * Minor versions: Changes to the model that, while being consistent across
   * schema versions, change the model parameters or implementation.
   *
   * @var int
   */
  public $minor;
  /**
   * Patch versions: Implementation changes meant to address bugs or
   * inaccuracies in the model implementation.
   *
   * @var int
   */
  public $patch;

  /**
   * Dated versions: Model datasets are recreated with refreshed input data but
   * no change to the algorithms regularly.
   *
   * @param string $dated
   */
  public function setDated($dated)
  {
    $this->dated = $dated;
  }
  /**
   * @return string
   */
  public function getDated()
  {
    return $this->dated;
  }
  /**
   * Major versions: Major changes to methodology (e.g. adding new data sources
   * to the model that lead to major output changes). Such changes will be
   * infrequent and announced well in advance. Might involve API version
   * changes, which will respect [Google Cloud API
   * guidelines](https://cloud.google.com/endpoints/docs/openapi/versioning-an-
   * api#backwards-incompatible)
   *
   * @param int $major
   */
  public function setMajor($major)
  {
    $this->major = $major;
  }
  /**
   * @return int
   */
  public function getMajor()
  {
    return $this->major;
  }
  /**
   * Minor versions: Changes to the model that, while being consistent across
   * schema versions, change the model parameters or implementation.
   *
   * @param int $minor
   */
  public function setMinor($minor)
  {
    $this->minor = $minor;
  }
  /**
   * @return int
   */
  public function getMinor()
  {
    return $this->minor;
  }
  /**
   * Patch versions: Implementation changes meant to address bugs or
   * inaccuracies in the model implementation.
   *
   * @param int $patch
   */
  public function setPatch($patch)
  {
    $this->patch = $patch;
  }
  /**
   * @return int
   */
  public function getPatch()
  {
    return $this->patch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModelVersion::class, 'Google_Service_TravelImpactModel_ModelVersion');

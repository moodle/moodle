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

namespace Google\Service\Config;

class TerraformBlueprint extends \Google\Model
{
  /**
   * URI of an object in Google Cloud Storage. Format: `gs://{bucket}/{object}`
   * URI may also specify an object version for zipped objects. Format:
   * `gs://{bucket}/{object}#{version}`
   *
   * @var string
   */
  public $gcsSource;
  protected $gitSourceType = GitSource::class;
  protected $gitSourceDataType = '';
  protected $inputValuesType = TerraformVariable::class;
  protected $inputValuesDataType = 'map';

  /**
   * URI of an object in Google Cloud Storage. Format: `gs://{bucket}/{object}`
   * URI may also specify an object version for zipped objects. Format:
   * `gs://{bucket}/{object}#{version}`
   *
   * @param string $gcsSource
   */
  public function setGcsSource($gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return string
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * URI of a public Git repo.
   *
   * @param GitSource $gitSource
   */
  public function setGitSource(GitSource $gitSource)
  {
    $this->gitSource = $gitSource;
  }
  /**
   * @return GitSource
   */
  public function getGitSource()
  {
    return $this->gitSource;
  }
  /**
   * Optional. Input variable values for the Terraform blueprint.
   *
   * @param TerraformVariable[] $inputValues
   */
  public function setInputValues($inputValues)
  {
    $this->inputValues = $inputValues;
  }
  /**
   * @return TerraformVariable[]
   */
  public function getInputValues()
  {
    return $this->inputValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TerraformBlueprint::class, 'Google_Service_Config_TerraformBlueprint');

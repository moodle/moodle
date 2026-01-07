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

class ApplyResults extends \Google\Model
{
  /**
   * Location of artifacts (e.g. logs) in Google Cloud Storage. Format:
   * `gs://{bucket}/{object}`
   *
   * @var string
   */
  public $artifacts;
  /**
   * Location of a blueprint copy and other manifests in Google Cloud Storage.
   * Format: `gs://{bucket}/{object}`
   *
   * @var string
   */
  public $content;
  protected $outputsType = TerraformOutput::class;
  protected $outputsDataType = 'map';

  /**
   * Location of artifacts (e.g. logs) in Google Cloud Storage. Format:
   * `gs://{bucket}/{object}`
   *
   * @param string $artifacts
   */
  public function setArtifacts($artifacts)
  {
    $this->artifacts = $artifacts;
  }
  /**
   * @return string
   */
  public function getArtifacts()
  {
    return $this->artifacts;
  }
  /**
   * Location of a blueprint copy and other manifests in Google Cloud Storage.
   * Format: `gs://{bucket}/{object}`
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Map of output name to output info.
   *
   * @param TerraformOutput[] $outputs
   */
  public function setOutputs($outputs)
  {
    $this->outputs = $outputs;
  }
  /**
   * @return TerraformOutput[]
   */
  public function getOutputs()
  {
    return $this->outputs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplyResults::class, 'Google_Service_Config_ApplyResults');

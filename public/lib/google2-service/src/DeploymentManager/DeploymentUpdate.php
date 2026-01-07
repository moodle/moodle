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

namespace Google\Service\DeploymentManager;

class DeploymentUpdate extends \Google\Collection
{
  protected $collection_key = 'labels';
  /**
   * Output only. An optional user-provided description of the deployment after
   * the current update has been applied.
   *
   * @var string
   */
  public $description;
  protected $labelsType = DeploymentUpdateLabelEntry::class;
  protected $labelsDataType = 'array';
  /**
   * Output only. URL of the manifest representing the update configuration of
   * this deployment.
   *
   * @var string
   */
  public $manifest;

  /**
   * Output only. An optional user-provided description of the deployment after
   * the current update has been applied.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Map of One Platform labels; provided by the client when the resource is
   * created or updated. Specifically: Label keys must be between 1 and 63
   * characters long and must conform to the following regular expression:
   * `[a-z]([-a-z0-9]*[a-z0-9])?` Label values must be between 0 and 63
   * characters long and must conform to the regular expression
   * `([a-z]([-a-z0-9]*[a-z0-9])?)?`.
   *
   * @param DeploymentUpdateLabelEntry[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return DeploymentUpdateLabelEntry[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. URL of the manifest representing the update configuration of
   * this deployment.
   *
   * @param string $manifest
   */
  public function setManifest($manifest)
  {
    $this->manifest = $manifest;
  }
  /**
   * @return string
   */
  public function getManifest()
  {
    return $this->manifest;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeploymentUpdate::class, 'Google_Service_DeploymentManager_DeploymentUpdate');

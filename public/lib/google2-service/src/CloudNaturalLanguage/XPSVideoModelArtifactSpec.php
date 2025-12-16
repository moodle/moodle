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

namespace Google\Service\CloudNaturalLanguage;

class XPSVideoModelArtifactSpec extends \Google\Collection
{
  protected $collection_key = 'exportArtifact';
  protected $exportArtifactType = XPSModelArtifactItem::class;
  protected $exportArtifactDataType = 'array';
  protected $servingArtifactType = XPSModelArtifactItem::class;
  protected $servingArtifactDataType = '';

  /**
   * The model binary files in different formats for model export.
   *
   * @param XPSModelArtifactItem[] $exportArtifact
   */
  public function setExportArtifact($exportArtifact)
  {
    $this->exportArtifact = $exportArtifact;
  }
  /**
   * @return XPSModelArtifactItem[]
   */
  public function getExportArtifact()
  {
    return $this->exportArtifact;
  }
  /**
   * The default model binary file used for serving (e.g. batch predict) via
   * public Cloud AI Platform API.
   *
   * @param XPSModelArtifactItem $servingArtifact
   */
  public function setServingArtifact(XPSModelArtifactItem $servingArtifact)
  {
    $this->servingArtifact = $servingArtifact;
  }
  /**
   * @return XPSModelArtifactItem
   */
  public function getServingArtifact()
  {
    return $this->servingArtifact;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSVideoModelArtifactSpec::class, 'Google_Service_CloudNaturalLanguage_XPSVideoModelArtifactSpec');

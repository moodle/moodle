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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1SchemaMutabilityMetadata extends \Google\Collection
{
  protected $collection_key = 'processorVersions';
  /**
   * @var bool
   */
  public $editable;
  /**
   * @var string[]
   */
  public $processorVersions;

  /**
   * @param bool
   */
  public function setEditable($editable)
  {
    $this->editable = $editable;
  }
  /**
   * @return bool
   */
  public function getEditable()
  {
    return $this->editable;
  }
  /**
   * @param string[]
   */
  public function setProcessorVersions($processorVersions)
  {
    $this->processorVersions = $processorVersions;
  }
  /**
   * @return string[]
   */
  public function getProcessorVersions()
  {
    return $this->processorVersions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1SchemaMutabilityMetadata::class, 'Google_Service_Document_GoogleCloudDocumentaiV1SchemaMutabilityMetadata');

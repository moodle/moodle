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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1ConversationContext extends \Google\Collection
{
  protected $collection_key = 'contextDocuments';
  /**
   * The current active document the user opened. It contains the document
   * resource reference.
   *
   * @var string
   */
  public $activeDocument;
  /**
   * The current list of documents the user is seeing. It contains the document
   * resource references.
   *
   * @var string[]
   */
  public $contextDocuments;

  /**
   * The current active document the user opened. It contains the document
   * resource reference.
   *
   * @param string $activeDocument
   */
  public function setActiveDocument($activeDocument)
  {
    $this->activeDocument = $activeDocument;
  }
  /**
   * @return string
   */
  public function getActiveDocument()
  {
    return $this->activeDocument;
  }
  /**
   * The current list of documents the user is seeing. It contains the document
   * resource references.
   *
   * @param string[] $contextDocuments
   */
  public function setContextDocuments($contextDocuments)
  {
    $this->contextDocuments = $contextDocuments;
  }
  /**
   * @return string[]
   */
  public function getContextDocuments()
  {
    return $this->contextDocuments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ConversationContext::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ConversationContext');

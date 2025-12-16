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

class GoogleCloudDiscoveryengineV1PurgeDocumentsRequestInlineSource extends \Google\Collection
{
  protected $collection_key = 'documents';
  /**
   * Required. A list of full resource name of documents to purge. In the format
   * `projects/locations/collections/dataStores/branches/documents`. Recommended
   * max of 100 items.
   *
   * @var string[]
   */
  public $documents;

  /**
   * Required. A list of full resource name of documents to purge. In the format
   * `projects/locations/collections/dataStores/branches/documents`. Recommended
   * max of 100 items.
   *
   * @param string[] $documents
   */
  public function setDocuments($documents)
  {
    $this->documents = $documents;
  }
  /**
   * @return string[]
   */
  public function getDocuments()
  {
    return $this->documents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1PurgeDocumentsRequestInlineSource::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1PurgeDocumentsRequestInlineSource');

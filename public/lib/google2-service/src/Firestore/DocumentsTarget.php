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

namespace Google\Service\Firestore;

class DocumentsTarget extends \Google\Collection
{
  protected $collection_key = 'documents';
  /**
   * The names of the documents to retrieve. In the format:
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
   * The request will fail if any of the document is not a child resource of the
   * given `database`. Duplicate names will be elided.
   *
   * @var string[]
   */
  public $documents;

  /**
   * The names of the documents to retrieve. In the format:
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
   * The request will fail if any of the document is not a child resource of the
   * given `database`. Duplicate names will be elided.
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
class_alias(DocumentsTarget::class, 'Google_Service_Firestore_DocumentsTarget');

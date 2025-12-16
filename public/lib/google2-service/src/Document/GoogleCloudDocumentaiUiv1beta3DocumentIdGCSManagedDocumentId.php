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

class GoogleCloudDocumentaiUiv1beta3DocumentIdGCSManagedDocumentId extends \Google\Model
{
  /**
   * Id of the document (indexed) managed by Content Warehouse.
   *
   * @deprecated
   * @var string
   */
  public $cwDocId;
  /**
   * Required. The Cloud Storage URI where the actual document is stored.
   *
   * @var string
   */
  public $gcsUri;

  /**
   * Id of the document (indexed) managed by Content Warehouse.
   *
   * @deprecated
   * @param string $cwDocId
   */
  public function setCwDocId($cwDocId)
  {
    $this->cwDocId = $cwDocId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getCwDocId()
  {
    return $this->cwDocId;
  }
  /**
   * Required. The Cloud Storage URI where the actual document is stored.
   *
   * @param string $gcsUri
   */
  public function setGcsUri($gcsUri)
  {
    $this->gcsUri = $gcsUri;
  }
  /**
   * @return string
   */
  public function getGcsUri()
  {
    return $this->gcsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3DocumentIdGCSManagedDocumentId::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3DocumentIdGCSManagedDocumentId');

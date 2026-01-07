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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1DocumentReference extends \Google\Model
{
  /**
   * Output only. The time when the document is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time when the document is deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * display_name of the referenced document; this name does not need to be
   * consistent to the display_name in the Document proto, depending on the ACL
   * constraint.
   *
   * @var string
   */
  public $displayName;
  /**
   * The document type of the document being referenced.
   *
   * @var bool
   */
  public $documentIsFolder;
  /**
   * Document is a folder with legal hold.
   *
   * @var bool
   */
  public $documentIsLegalHoldFolder;
  /**
   * Document is a folder with retention policy.
   *
   * @var bool
   */
  public $documentIsRetentionFolder;
  /**
   * Required. Name of the referenced document.
   *
   * @var string
   */
  public $documentName;
  /**
   * Stores the subset of the referenced document's content. This is useful to
   * allow user peek the information of the referenced document.
   *
   * @var string
   */
  public $snippet;
  /**
   * Output only. The time when the document is last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the document is created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The time when the document is deleted.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * display_name of the referenced document; this name does not need to be
   * consistent to the display_name in the Document proto, depending on the ACL
   * constraint.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The document type of the document being referenced.
   *
   * @param bool $documentIsFolder
   */
  public function setDocumentIsFolder($documentIsFolder)
  {
    $this->documentIsFolder = $documentIsFolder;
  }
  /**
   * @return bool
   */
  public function getDocumentIsFolder()
  {
    return $this->documentIsFolder;
  }
  /**
   * Document is a folder with legal hold.
   *
   * @param bool $documentIsLegalHoldFolder
   */
  public function setDocumentIsLegalHoldFolder($documentIsLegalHoldFolder)
  {
    $this->documentIsLegalHoldFolder = $documentIsLegalHoldFolder;
  }
  /**
   * @return bool
   */
  public function getDocumentIsLegalHoldFolder()
  {
    return $this->documentIsLegalHoldFolder;
  }
  /**
   * Document is a folder with retention policy.
   *
   * @param bool $documentIsRetentionFolder
   */
  public function setDocumentIsRetentionFolder($documentIsRetentionFolder)
  {
    $this->documentIsRetentionFolder = $documentIsRetentionFolder;
  }
  /**
   * @return bool
   */
  public function getDocumentIsRetentionFolder()
  {
    return $this->documentIsRetentionFolder;
  }
  /**
   * Required. Name of the referenced document.
   *
   * @param string $documentName
   */
  public function setDocumentName($documentName)
  {
    $this->documentName = $documentName;
  }
  /**
   * @return string
   */
  public function getDocumentName()
  {
    return $this->documentName;
  }
  /**
   * Stores the subset of the referenced document's content. This is useful to
   * allow user peek the information of the referenced document.
   *
   * @param string $snippet
   */
  public function setSnippet($snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return string
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * Output only. The time when the document is last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1DocumentReference::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1DocumentReference');

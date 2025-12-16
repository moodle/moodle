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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickDocumentPerCategoryList extends \Google\Collection
{
  public const LIST_TYPE_UNKNOWN_LIST_TYPE = 'UNKNOWN_LIST_TYPE';
  /**
   * All documents in the list correspond to one of the mention categories.
   */
  public const LIST_TYPE_MENTIONS = 'MENTIONS';
  /**
   * All documents in the list correspond to one of the share categories.
   */
  public const LIST_TYPE_SHARES = 'SHARES';
  /**
   * A mixture of document categories that correspond to documents that need the
   * users attention (e.g. documents that have been explicitly shared with the
   * user but have not been viewed and documents where the user was mentioned
   * but has not replied).
   */
  public const LIST_TYPE_NEEDS_ATTENTION = 'NEEDS_ATTENTION';
  /**
   * All documents in the list correspond to one of the view categories.
   */
  public const LIST_TYPE_VIEWS = 'VIEWS';
  /**
   * All documents in the list correspond to one of the edit categories.
   */
  public const LIST_TYPE_EDITS = 'EDITS';
  protected $collection_key = 'documents';
  protected $documentsType = EnterpriseTopazSidekickDocumentPerCategoryListDocumentPerCategoryListEntry::class;
  protected $documentsDataType = 'array';
  /**
   * Localized message explaining how the documents were derived (e.g. from the
   * last 30 days activity). This field is optional.
   *
   * @var string
   */
  public $helpMessage;
  /**
   * @var string
   */
  public $listType;
  /**
   * Description of the types of documents present in the list.
   *
   * @var string
   */
  public $listTypeDescription;
  /**
   * Response message in case no documents are present in the card.
   *
   * @var string
   */
  public $responseMessage;

  /**
   * @param EnterpriseTopazSidekickDocumentPerCategoryListDocumentPerCategoryListEntry[] $documents
   */
  public function setDocuments($documents)
  {
    $this->documents = $documents;
  }
  /**
   * @return EnterpriseTopazSidekickDocumentPerCategoryListDocumentPerCategoryListEntry[]
   */
  public function getDocuments()
  {
    return $this->documents;
  }
  /**
   * Localized message explaining how the documents were derived (e.g. from the
   * last 30 days activity). This field is optional.
   *
   * @param string $helpMessage
   */
  public function setHelpMessage($helpMessage)
  {
    $this->helpMessage = $helpMessage;
  }
  /**
   * @return string
   */
  public function getHelpMessage()
  {
    return $this->helpMessage;
  }
  /**
   * @param self::LIST_TYPE_* $listType
   */
  public function setListType($listType)
  {
    $this->listType = $listType;
  }
  /**
   * @return self::LIST_TYPE_*
   */
  public function getListType()
  {
    return $this->listType;
  }
  /**
   * Description of the types of documents present in the list.
   *
   * @param string $listTypeDescription
   */
  public function setListTypeDescription($listTypeDescription)
  {
    $this->listTypeDescription = $listTypeDescription;
  }
  /**
   * @return string
   */
  public function getListTypeDescription()
  {
    return $this->listTypeDescription;
  }
  /**
   * Response message in case no documents are present in the card.
   *
   * @param string $responseMessage
   */
  public function setResponseMessage($responseMessage)
  {
    $this->responseMessage = $responseMessage;
  }
  /**
   * @return string
   */
  public function getResponseMessage()
  {
    return $this->responseMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickDocumentPerCategoryList::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickDocumentPerCategoryList');

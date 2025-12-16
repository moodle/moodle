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

class EnterpriseTopazSidekickGetAndKeepAheadCardProto extends \Google\Model
{
  protected $declinedEventsType = EnterpriseTopazSidekickGetAndKeepAheadCardProtoDeclinedEvents::class;
  protected $declinedEventsDataType = '';
  protected $mentionedDocumentsType = EnterpriseTopazSidekickDocumentPerCategoryList::class;
  protected $mentionedDocumentsDataType = '';
  protected $sharedDocumentsType = EnterpriseTopazSidekickDocumentPerCategoryList::class;
  protected $sharedDocumentsDataType = '';

  /**
   * @param EnterpriseTopazSidekickGetAndKeepAheadCardProtoDeclinedEvents $declinedEvents
   */
  public function setDeclinedEvents(EnterpriseTopazSidekickGetAndKeepAheadCardProtoDeclinedEvents $declinedEvents)
  {
    $this->declinedEvents = $declinedEvents;
  }
  /**
   * @return EnterpriseTopazSidekickGetAndKeepAheadCardProtoDeclinedEvents
   */
  public function getDeclinedEvents()
  {
    return $this->declinedEvents;
  }
  /**
   * @param EnterpriseTopazSidekickDocumentPerCategoryList $mentionedDocuments
   */
  public function setMentionedDocuments(EnterpriseTopazSidekickDocumentPerCategoryList $mentionedDocuments)
  {
    $this->mentionedDocuments = $mentionedDocuments;
  }
  /**
   * @return EnterpriseTopazSidekickDocumentPerCategoryList
   */
  public function getMentionedDocuments()
  {
    return $this->mentionedDocuments;
  }
  /**
   * @param EnterpriseTopazSidekickDocumentPerCategoryList $sharedDocuments
   */
  public function setSharedDocuments(EnterpriseTopazSidekickDocumentPerCategoryList $sharedDocuments)
  {
    $this->sharedDocuments = $sharedDocuments;
  }
  /**
   * @return EnterpriseTopazSidekickDocumentPerCategoryList
   */
  public function getSharedDocuments()
  {
    return $this->sharedDocuments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickGetAndKeepAheadCardProto::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickGetAndKeepAheadCardProto');

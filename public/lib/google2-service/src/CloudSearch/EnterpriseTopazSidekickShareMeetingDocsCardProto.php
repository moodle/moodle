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

class EnterpriseTopazSidekickShareMeetingDocsCardProto extends \Google\Collection
{
  protected $collection_key = 'document';
  protected $documentType = EnterpriseTopazSidekickCommonDocument::class;
  protected $documentDataType = 'array';
  protected $eventType = EnterpriseTopazSidekickAgendaEntry::class;
  protected $eventDataType = '';

  /**
   * Documents to share for the given meeting.
   *
   * @param EnterpriseTopazSidekickCommonDocument[] $document
   */
  public function setDocument($document)
  {
    $this->document = $document;
  }
  /**
   * @return EnterpriseTopazSidekickCommonDocument[]
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * Event.
   *
   * @param EnterpriseTopazSidekickAgendaEntry $event
   */
  public function setEvent(EnterpriseTopazSidekickAgendaEntry $event)
  {
    $this->event = $event;
  }
  /**
   * @return EnterpriseTopazSidekickAgendaEntry
   */
  public function getEvent()
  {
    return $this->event;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickShareMeetingDocsCardProto::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickShareMeetingDocsCardProto');

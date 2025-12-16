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

class EnterpriseTopazSidekickCommonDocumentJustification extends \Google\Model
{
  /**
   * Unknown justification.
   */
  public const REASON_UNKNOWN = 'UNKNOWN';
  /**
   * Popular documents within collaborators.
   */
  public const REASON_TRENDING_IN_COLLABORATORS = 'TRENDING_IN_COLLABORATORS';
  /**
   * Popular documents within the domain.
   */
  public const REASON_TRENDING_IN_DOMAIN = 'TRENDING_IN_DOMAIN';
  /**
   * Documents being reviewed frequently by the current user .
   */
  public const REASON_FREQUENTLY_VIEWED = 'FREQUENTLY_VIEWED';
  /**
   * Documents being edited frequently by the current user .
   */
  public const REASON_FREQUENTLY_EDITED = 'FREQUENTLY_EDITED';
  /**
   * Documents updated since user's last visit.
   */
  public const REASON_NEW_UPDATES = 'NEW_UPDATES';
  /**
   * Documents that receive comments since user's last visit.
   */
  public const REASON_NEW_COMMENTS = 'NEW_COMMENTS';
  /**
   * Documents in the calendar event description.
   */
  public const REASON_EVENT_DESCRIPTION = 'EVENT_DESCRIPTION';
  /**
   * Documents in the calendar event attachments section.
   */
  public const REASON_EVENT_ATTACHMENT = 'EVENT_ATTACHMENT';
  /**
   * Documents attached in calendar event metadata instead of the attachment
   * section. Event metadata is not visible to the final user. Enterprise assist
   * uses this metadata to store auto-generated documents such as meeting notes.
   */
  public const REASON_EVENT_METADATA_ATTACHMENT = 'EVENT_METADATA_ATTACHMENT';
  /**
   * Documents mined, and so, probably related to the request context. For
   * example, this category includes documents related to a meeting.
   */
  public const REASON_MINED_DOCUMENT = 'MINED_DOCUMENT';
  /**
   * Documents that contains mentions of the user.
   */
  public const REASON_NEW_MENTIONS = 'NEW_MENTIONS';
  /**
   * Documents that are shared with the user.
   */
  public const REASON_NEW_SHARES = 'NEW_SHARES';
  /**
   * A locale aware message that explains why this document was selected.
   *
   * @var string
   */
  public $justification;
  /**
   * Reason on why the document is selected. Populate for trending documents.
   *
   * @var string
   */
  public $reason;

  /**
   * A locale aware message that explains why this document was selected.
   *
   * @param string $justification
   */
  public function setJustification($justification)
  {
    $this->justification = $justification;
  }
  /**
   * @return string
   */
  public function getJustification()
  {
    return $this->justification;
  }
  /**
   * Reason on why the document is selected. Populate for trending documents.
   *
   * Accepted values: UNKNOWN, TRENDING_IN_COLLABORATORS, TRENDING_IN_DOMAIN,
   * FREQUENTLY_VIEWED, FREQUENTLY_EDITED, NEW_UPDATES, NEW_COMMENTS,
   * EVENT_DESCRIPTION, EVENT_ATTACHMENT, EVENT_METADATA_ATTACHMENT,
   * MINED_DOCUMENT, NEW_MENTIONS, NEW_SHARES
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickCommonDocumentJustification::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickCommonDocumentJustification');

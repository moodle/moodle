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

namespace Google\Service\AlertCenter;

class GoogleOperations extends \Google\Collection
{
  protected $collection_key = 'affectedUserEmails';
  /**
   * The list of emails which correspond to the users directly affected by the
   * incident.
   *
   * @var string[]
   */
  public $affectedUserEmails;
  protected $attachmentDataType = Attachment::class;
  protected $attachmentDataDataType = '';
  /**
   * A detailed, freeform incident description.
   *
   * @var string
   */
  public $description;
  /**
   * Customer domain for email template personalization.
   *
   * @var string
   */
  public $domain;
  /**
   * A header to display above the incident message. Typically used to attach a
   * localized notice on the timeline for followup comms translations.
   *
   * @var string
   */
  public $header;
  /**
   * A one-line incident description.
   *
   * @var string
   */
  public $title;

  /**
   * The list of emails which correspond to the users directly affected by the
   * incident.
   *
   * @param string[] $affectedUserEmails
   */
  public function setAffectedUserEmails($affectedUserEmails)
  {
    $this->affectedUserEmails = $affectedUserEmails;
  }
  /**
   * @return string[]
   */
  public function getAffectedUserEmails()
  {
    return $this->affectedUserEmails;
  }
  /**
   * Optional. Application-specific data for an incident, provided when the
   * Google Workspace application which reported the incident cannot be
   * completely restored to a valid state.
   *
   * @param Attachment $attachmentData
   */
  public function setAttachmentData(Attachment $attachmentData)
  {
    $this->attachmentData = $attachmentData;
  }
  /**
   * @return Attachment
   */
  public function getAttachmentData()
  {
    return $this->attachmentData;
  }
  /**
   * A detailed, freeform incident description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Customer domain for email template personalization.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * A header to display above the incident message. Typically used to attach a
   * localized notice on the timeline for followup comms translations.
   *
   * @param string $header
   */
  public function setHeader($header)
  {
    $this->header = $header;
  }
  /**
   * @return string
   */
  public function getHeader()
  {
    return $this->header;
  }
  /**
   * A one-line incident description.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleOperations::class, 'Google_Service_AlertCenter_GoogleOperations');

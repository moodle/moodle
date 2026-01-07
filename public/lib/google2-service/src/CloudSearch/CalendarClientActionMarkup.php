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

class CalendarClientActionMarkup extends \Google\Model
{
  protected $addAttachmentsActionMarkupType = AppsExtensionsMarkupCalendarClientActionMarkupAddAttachmentsActionMarkup::class;
  protected $addAttachmentsActionMarkupDataType = '';
  protected $editAttendeesActionMarkupType = AppsExtensionsMarkupCalendarClientActionMarkupEditAttendeesActionMarkup::class;
  protected $editAttendeesActionMarkupDataType = '';
  protected $editConferenceDataActionMarkupType = AppsExtensionsMarkupCalendarClientActionMarkupEditConferenceDataActionMarkup::class;
  protected $editConferenceDataActionMarkupDataType = '';

  /**
   * @param AppsExtensionsMarkupCalendarClientActionMarkupAddAttachmentsActionMarkup
   */
  public function setAddAttachmentsActionMarkup(AppsExtensionsMarkupCalendarClientActionMarkupAddAttachmentsActionMarkup $addAttachmentsActionMarkup)
  {
    $this->addAttachmentsActionMarkup = $addAttachmentsActionMarkup;
  }
  /**
   * @return AppsExtensionsMarkupCalendarClientActionMarkupAddAttachmentsActionMarkup
   */
  public function getAddAttachmentsActionMarkup()
  {
    return $this->addAttachmentsActionMarkup;
  }
  /**
   * @param AppsExtensionsMarkupCalendarClientActionMarkupEditAttendeesActionMarkup
   */
  public function setEditAttendeesActionMarkup(AppsExtensionsMarkupCalendarClientActionMarkupEditAttendeesActionMarkup $editAttendeesActionMarkup)
  {
    $this->editAttendeesActionMarkup = $editAttendeesActionMarkup;
  }
  /**
   * @return AppsExtensionsMarkupCalendarClientActionMarkupEditAttendeesActionMarkup
   */
  public function getEditAttendeesActionMarkup()
  {
    return $this->editAttendeesActionMarkup;
  }
  /**
   * @param AppsExtensionsMarkupCalendarClientActionMarkupEditConferenceDataActionMarkup
   */
  public function setEditConferenceDataActionMarkup(AppsExtensionsMarkupCalendarClientActionMarkupEditConferenceDataActionMarkup $editConferenceDataActionMarkup)
  {
    $this->editConferenceDataActionMarkup = $editConferenceDataActionMarkup;
  }
  /**
   * @return AppsExtensionsMarkupCalendarClientActionMarkupEditConferenceDataActionMarkup
   */
  public function getEditConferenceDataActionMarkup()
  {
    return $this->editConferenceDataActionMarkup;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CalendarClientActionMarkup::class, 'Google_Service_CloudSearch_CalendarClientActionMarkup');

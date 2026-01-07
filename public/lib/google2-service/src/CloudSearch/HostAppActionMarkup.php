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

class HostAppActionMarkup extends \Google\Model
{
  protected $calendarActionType = CalendarClientActionMarkup::class;
  protected $calendarActionDataType = '';
  protected $chatActionType = ChatClientActionMarkup::class;
  protected $chatActionDataType = '';
  protected $driveActionType = DriveClientActionMarkup::class;
  protected $driveActionDataType = '';
  protected $editorActionType = EditorClientActionMarkup::class;
  protected $editorActionDataType = '';
  protected $gmailActionType = GmailClientActionMarkup::class;
  protected $gmailActionDataType = '';
  protected $sheetsActionType = SheetsClientActionMarkup::class;
  protected $sheetsActionDataType = '';

  /**
   * @param CalendarClientActionMarkup
   */
  public function setCalendarAction(CalendarClientActionMarkup $calendarAction)
  {
    $this->calendarAction = $calendarAction;
  }
  /**
   * @return CalendarClientActionMarkup
   */
  public function getCalendarAction()
  {
    return $this->calendarAction;
  }
  /**
   * @param ChatClientActionMarkup
   */
  public function setChatAction(ChatClientActionMarkup $chatAction)
  {
    $this->chatAction = $chatAction;
  }
  /**
   * @return ChatClientActionMarkup
   */
  public function getChatAction()
  {
    return $this->chatAction;
  }
  /**
   * @param DriveClientActionMarkup
   */
  public function setDriveAction(DriveClientActionMarkup $driveAction)
  {
    $this->driveAction = $driveAction;
  }
  /**
   * @return DriveClientActionMarkup
   */
  public function getDriveAction()
  {
    return $this->driveAction;
  }
  /**
   * @param EditorClientActionMarkup
   */
  public function setEditorAction(EditorClientActionMarkup $editorAction)
  {
    $this->editorAction = $editorAction;
  }
  /**
   * @return EditorClientActionMarkup
   */
  public function getEditorAction()
  {
    return $this->editorAction;
  }
  /**
   * @param GmailClientActionMarkup
   */
  public function setGmailAction(GmailClientActionMarkup $gmailAction)
  {
    $this->gmailAction = $gmailAction;
  }
  /**
   * @return GmailClientActionMarkup
   */
  public function getGmailAction()
  {
    return $this->gmailAction;
  }
  /**
   * @param SheetsClientActionMarkup
   */
  public function setSheetsAction(SheetsClientActionMarkup $sheetsAction)
  {
    $this->sheetsAction = $sheetsAction;
  }
  /**
   * @return SheetsClientActionMarkup
   */
  public function getSheetsAction()
  {
    return $this->sheetsAction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HostAppActionMarkup::class, 'Google_Service_CloudSearch_HostAppActionMarkup');

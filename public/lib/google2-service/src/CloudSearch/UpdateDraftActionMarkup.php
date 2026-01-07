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

class UpdateDraftActionMarkup extends \Google\Model
{
  protected $updateBccRecipientsType = UpdateBccRecipients::class;
  protected $updateBccRecipientsDataType = '';
  protected $updateBodyType = UpdateBody::class;
  protected $updateBodyDataType = '';
  protected $updateCcRecipientsType = UpdateCcRecipients::class;
  protected $updateCcRecipientsDataType = '';
  protected $updateSubjectType = UpdateSubject::class;
  protected $updateSubjectDataType = '';
  protected $updateToRecipientsType = UpdateToRecipients::class;
  protected $updateToRecipientsDataType = '';

  /**
   * @param UpdateBccRecipients
   */
  public function setUpdateBccRecipients(UpdateBccRecipients $updateBccRecipients)
  {
    $this->updateBccRecipients = $updateBccRecipients;
  }
  /**
   * @return UpdateBccRecipients
   */
  public function getUpdateBccRecipients()
  {
    return $this->updateBccRecipients;
  }
  /**
   * @param UpdateBody
   */
  public function setUpdateBody(UpdateBody $updateBody)
  {
    $this->updateBody = $updateBody;
  }
  /**
   * @return UpdateBody
   */
  public function getUpdateBody()
  {
    return $this->updateBody;
  }
  /**
   * @param UpdateCcRecipients
   */
  public function setUpdateCcRecipients(UpdateCcRecipients $updateCcRecipients)
  {
    $this->updateCcRecipients = $updateCcRecipients;
  }
  /**
   * @return UpdateCcRecipients
   */
  public function getUpdateCcRecipients()
  {
    return $this->updateCcRecipients;
  }
  /**
   * @param UpdateSubject
   */
  public function setUpdateSubject(UpdateSubject $updateSubject)
  {
    $this->updateSubject = $updateSubject;
  }
  /**
   * @return UpdateSubject
   */
  public function getUpdateSubject()
  {
    return $this->updateSubject;
  }
  /**
   * @param UpdateToRecipients
   */
  public function setUpdateToRecipients(UpdateToRecipients $updateToRecipients)
  {
    $this->updateToRecipients = $updateToRecipients;
  }
  /**
   * @return UpdateToRecipients
   */
  public function getUpdateToRecipients()
  {
    return $this->updateToRecipients;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateDraftActionMarkup::class, 'Google_Service_CloudSearch_UpdateDraftActionMarkup');

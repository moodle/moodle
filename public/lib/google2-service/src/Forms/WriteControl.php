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

namespace Google\Service\Forms;

class WriteControl extends \Google\Model
{
  /**
   * The revision ID of the form that the write request is applied to. If this
   * is not the latest revision of the form, the request is not processed and
   * returns a 400 bad request error.
   *
   * @var string
   */
  public $requiredRevisionId;
  /**
   * The target revision ID of the form that the write request is applied to. If
   * changes have occurred after this revision, the changes in this update
   * request are transformed against those changes. This results in a new
   * revision of the form that incorporates both the changes in the request and
   * the intervening changes, with the server resolving conflicting changes. The
   * target revision ID may only be used to write to recent versions of a form.
   * If the target revision is too far behind the latest revision, the request
   * is not processed and returns a 400 (Bad Request Error). The request may be
   * retried after reading the latest version of the form. In most cases a
   * target revision ID remains valid for several minutes after it is read, but
   * for frequently-edited forms this window may be shorter.
   *
   * @var string
   */
  public $targetRevisionId;

  /**
   * The revision ID of the form that the write request is applied to. If this
   * is not the latest revision of the form, the request is not processed and
   * returns a 400 bad request error.
   *
   * @param string $requiredRevisionId
   */
  public function setRequiredRevisionId($requiredRevisionId)
  {
    $this->requiredRevisionId = $requiredRevisionId;
  }
  /**
   * @return string
   */
  public function getRequiredRevisionId()
  {
    return $this->requiredRevisionId;
  }
  /**
   * The target revision ID of the form that the write request is applied to. If
   * changes have occurred after this revision, the changes in this update
   * request are transformed against those changes. This results in a new
   * revision of the form that incorporates both the changes in the request and
   * the intervening changes, with the server resolving conflicting changes. The
   * target revision ID may only be used to write to recent versions of a form.
   * If the target revision is too far behind the latest revision, the request
   * is not processed and returns a 400 (Bad Request Error). The request may be
   * retried after reading the latest version of the form. In most cases a
   * target revision ID remains valid for several minutes after it is read, but
   * for frequently-edited forms this window may be shorter.
   *
   * @param string $targetRevisionId
   */
  public function setTargetRevisionId($targetRevisionId)
  {
    $this->targetRevisionId = $targetRevisionId;
  }
  /**
   * @return string
   */
  public function getTargetRevisionId()
  {
    return $this->targetRevisionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WriteControl::class, 'Google_Service_Forms_WriteControl');

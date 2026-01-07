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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2WriteControl extends \Google\Model
{
  /**
   * The revision ID of the label that the write request will be applied to. If
   * this isn't the latest revision of the label, the request will not be
   * processed and will return a 400 Bad Request error.
   *
   * @var string
   */
  public $requiredRevisionId;

  /**
   * The revision ID of the label that the write request will be applied to. If
   * this isn't the latest revision of the label, the request will not be
   * processed and will return a 400 Bad Request error.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2WriteControl::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2WriteControl');

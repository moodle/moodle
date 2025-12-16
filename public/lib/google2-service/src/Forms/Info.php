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

class Info extends \Google\Model
{
  /**
   * The description of the form.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The title of the document which is visible in Drive. If
   * Info.title is empty, `document_title` may appear in its place in the Google
   * Forms UI and be visible to responders. `document_title` can be set on
   * create, but cannot be modified by a batchUpdate request. Please use the
   * [Google Drive
   * API](https://developers.google.com/drive/api/v3/reference/files/update) if
   * you need to programmatically update `document_title`.
   *
   * @var string
   */
  public $documentTitle;
  /**
   * Required. The title of the form which is visible to responders.
   *
   * @var string
   */
  public $title;

  /**
   * The description of the form.
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
   * Output only. The title of the document which is visible in Drive. If
   * Info.title is empty, `document_title` may appear in its place in the Google
   * Forms UI and be visible to responders. `document_title` can be set on
   * create, but cannot be modified by a batchUpdate request. Please use the
   * [Google Drive
   * API](https://developers.google.com/drive/api/v3/reference/files/update) if
   * you need to programmatically update `document_title`.
   *
   * @param string $documentTitle
   */
  public function setDocumentTitle($documentTitle)
  {
    $this->documentTitle = $documentTitle;
  }
  /**
   * @return string
   */
  public function getDocumentTitle()
  {
    return $this->documentTitle;
  }
  /**
   * Required. The title of the form which is visible to responders.
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
class_alias(Info::class, 'Google_Service_Forms_Info');

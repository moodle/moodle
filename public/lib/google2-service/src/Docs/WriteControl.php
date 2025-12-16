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

namespace Google\Service\Docs;

class WriteControl extends \Google\Model
{
  /**
   * The optional revision ID of the document the write request is applied to.
   * If this is not the latest revision of the document, the request is not
   * processed and returns a 400 bad request error. When a required revision ID
   * is returned in a response, it indicates the revision ID of the document
   * after the request was applied.
   *
   * @var string
   */
  public $requiredRevisionId;
  /**
   * The optional target revision ID of the document the write request is
   * applied to. If collaborator changes have occurred after the document was
   * read using the API, the changes produced by this write request are applied
   * against the collaborator changes. This results in a new revision of the
   * document that incorporates both the collaborator changes and the changes in
   * the request, with the Docs server resolving conflicting changes. When using
   * target revision ID, the API client can be thought of as another
   * collaborator of the document. The target revision ID can only be used to
   * write to recent versions of a document. If the target revision is too far
   * behind the latest revision, the request is not processed and returns a 400
   * bad request error. The request should be tried again after retrieving the
   * latest version of the document. Usually a revision ID remains valid for use
   * as a target revision for several minutes after it's read, but for
   * frequently edited documents this window might be shorter.
   *
   * @var string
   */
  public $targetRevisionId;

  /**
   * The optional revision ID of the document the write request is applied to.
   * If this is not the latest revision of the document, the request is not
   * processed and returns a 400 bad request error. When a required revision ID
   * is returned in a response, it indicates the revision ID of the document
   * after the request was applied.
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
   * The optional target revision ID of the document the write request is
   * applied to. If collaborator changes have occurred after the document was
   * read using the API, the changes produced by this write request are applied
   * against the collaborator changes. This results in a new revision of the
   * document that incorporates both the collaborator changes and the changes in
   * the request, with the Docs server resolving conflicting changes. When using
   * target revision ID, the API client can be thought of as another
   * collaborator of the document. The target revision ID can only be used to
   * write to recent versions of a document. If the target revision is too far
   * behind the latest revision, the request is not processed and returns a 400
   * bad request error. The request should be tried again after retrieving the
   * latest version of the document. Usually a revision ID remains valid for use
   * as a target revision for several minutes after it's read, but for
   * frequently edited documents this window might be shorter.
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
class_alias(WriteControl::class, 'Google_Service_Docs_WriteControl');

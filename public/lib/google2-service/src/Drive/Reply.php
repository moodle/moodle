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

namespace Google\Service\Drive;

class Reply extends \Google\Collection
{
  protected $collection_key = 'mentionedEmailAddresses';
  /**
   * The action the reply performed to the parent comment. The supported values
   * are: * `resolve` * `reopen`
   *
   * @var string
   */
  public $action;
  /**
   * Output only. The email address of the user assigned to this comment. If no
   * user is assigned, the field is unset.
   *
   * @var string
   */
  public $assigneeEmailAddress;
  protected $authorType = User::class;
  protected $authorDataType = '';
  /**
   * The plain text content of the reply. This field is used for setting the
   * content, while `htmlContent` should be displayed. This field is required by
   * the `create` method if no `action` value is specified.
   *
   * @var string
   */
  public $content;
  /**
   * The time at which the reply was created (RFC 3339 date-time).
   *
   * @var string
   */
  public $createdTime;
  /**
   * Output only. Whether the reply has been deleted. A deleted reply has no
   * content.
   *
   * @var bool
   */
  public $deleted;
  /**
   * Output only. The content of the reply with HTML formatting.
   *
   * @var string
   */
  public $htmlContent;
  /**
   * Output only. The ID of the reply.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `"drive#reply"`.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. A list of email addresses for users mentioned in this comment.
   * If no users are mentioned, the list is empty.
   *
   * @var string[]
   */
  public $mentionedEmailAddresses;
  /**
   * The last time the reply was modified (RFC 3339 date-time).
   *
   * @var string
   */
  public $modifiedTime;

  /**
   * The action the reply performed to the parent comment. The supported values
   * are: * `resolve` * `reopen`
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Output only. The email address of the user assigned to this comment. If no
   * user is assigned, the field is unset.
   *
   * @param string $assigneeEmailAddress
   */
  public function setAssigneeEmailAddress($assigneeEmailAddress)
  {
    $this->assigneeEmailAddress = $assigneeEmailAddress;
  }
  /**
   * @return string
   */
  public function getAssigneeEmailAddress()
  {
    return $this->assigneeEmailAddress;
  }
  /**
   * Output only. The author of the reply. The author's email address and
   * permission ID won't be populated.
   *
   * @param User $author
   */
  public function setAuthor(User $author)
  {
    $this->author = $author;
  }
  /**
   * @return User
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * The plain text content of the reply. This field is used for setting the
   * content, while `htmlContent` should be displayed. This field is required by
   * the `create` method if no `action` value is specified.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * The time at which the reply was created (RFC 3339 date-time).
   *
   * @param string $createdTime
   */
  public function setCreatedTime($createdTime)
  {
    $this->createdTime = $createdTime;
  }
  /**
   * @return string
   */
  public function getCreatedTime()
  {
    return $this->createdTime;
  }
  /**
   * Output only. Whether the reply has been deleted. A deleted reply has no
   * content.
   *
   * @param bool $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return bool
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * Output only. The content of the reply with HTML formatting.
   *
   * @param string $htmlContent
   */
  public function setHtmlContent($htmlContent)
  {
    $this->htmlContent = $htmlContent;
  }
  /**
   * @return string
   */
  public function getHtmlContent()
  {
    return $this->htmlContent;
  }
  /**
   * Output only. The ID of the reply.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `"drive#reply"`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Output only. A list of email addresses for users mentioned in this comment.
   * If no users are mentioned, the list is empty.
   *
   * @param string[] $mentionedEmailAddresses
   */
  public function setMentionedEmailAddresses($mentionedEmailAddresses)
  {
    $this->mentionedEmailAddresses = $mentionedEmailAddresses;
  }
  /**
   * @return string[]
   */
  public function getMentionedEmailAddresses()
  {
    return $this->mentionedEmailAddresses;
  }
  /**
   * The last time the reply was modified (RFC 3339 date-time).
   *
   * @param string $modifiedTime
   */
  public function setModifiedTime($modifiedTime)
  {
    $this->modifiedTime = $modifiedTime;
  }
  /**
   * @return string
   */
  public function getModifiedTime()
  {
    return $this->modifiedTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Reply::class, 'Google_Service_Drive_Reply');

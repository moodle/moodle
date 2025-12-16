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

class Comment extends \Google\Collection
{
  protected $collection_key = 'replies';
  /**
   * A region of the document represented as a JSON string. For details on
   * defining anchor properties, refer to [Manage comments and
   * replies](https://developers.google.com/workspace/drive/api/v3/manage-
   * comments).
   *
   * @var string
   */
  public $anchor;
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
   * The plain text content of the comment. This field is used for setting the
   * content, while `htmlContent` should be displayed.
   *
   * @var string
   */
  public $content;
  /**
   * The time at which the comment was created (RFC 3339 date-time).
   *
   * @var string
   */
  public $createdTime;
  /**
   * Output only. Whether the comment has been deleted. A deleted comment has no
   * content.
   *
   * @var bool
   */
  public $deleted;
  /**
   * Output only. The content of the comment with HTML formatting.
   *
   * @var string
   */
  public $htmlContent;
  /**
   * Output only. The ID of the comment.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `"drive#comment"`.
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
   * The last time the comment or any of its replies was modified (RFC 3339
   * date-time).
   *
   * @var string
   */
  public $modifiedTime;
  protected $quotedFileContentType = CommentQuotedFileContent::class;
  protected $quotedFileContentDataType = '';
  protected $repliesType = Reply::class;
  protected $repliesDataType = 'array';
  /**
   * Output only. Whether the comment has been resolved by one of its replies.
   *
   * @var bool
   */
  public $resolved;

  /**
   * A region of the document represented as a JSON string. For details on
   * defining anchor properties, refer to [Manage comments and
   * replies](https://developers.google.com/workspace/drive/api/v3/manage-
   * comments).
   *
   * @param string $anchor
   */
  public function setAnchor($anchor)
  {
    $this->anchor = $anchor;
  }
  /**
   * @return string
   */
  public function getAnchor()
  {
    return $this->anchor;
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
   * Output only. The author of the comment. The author's email address and
   * permission ID will not be populated.
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
   * The plain text content of the comment. This field is used for setting the
   * content, while `htmlContent` should be displayed.
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
   * The time at which the comment was created (RFC 3339 date-time).
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
   * Output only. Whether the comment has been deleted. A deleted comment has no
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
   * Output only. The content of the comment with HTML formatting.
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
   * Output only. The ID of the comment.
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
   * string `"drive#comment"`.
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
   * The last time the comment or any of its replies was modified (RFC 3339
   * date-time).
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
  /**
   * The file content to which the comment refers, typically within the anchor
   * region. For a text file, for example, this would be the text at the
   * location of the comment.
   *
   * @param CommentQuotedFileContent $quotedFileContent
   */
  public function setQuotedFileContent(CommentQuotedFileContent $quotedFileContent)
  {
    $this->quotedFileContent = $quotedFileContent;
  }
  /**
   * @return CommentQuotedFileContent
   */
  public function getQuotedFileContent()
  {
    return $this->quotedFileContent;
  }
  /**
   * Output only. The full list of replies to the comment in chronological
   * order.
   *
   * @param Reply[] $replies
   */
  public function setReplies($replies)
  {
    $this->replies = $replies;
  }
  /**
   * @return Reply[]
   */
  public function getReplies()
  {
    return $this->replies;
  }
  /**
   * Output only. Whether the comment has been resolved by one of its replies.
   *
   * @param bool $resolved
   */
  public function setResolved($resolved)
  {
    $this->resolved = $resolved;
  }
  /**
   * @return bool
   */
  public function getResolved()
  {
    return $this->resolved;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Comment::class, 'Google_Service_Drive_Comment');

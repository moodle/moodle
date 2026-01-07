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

namespace Google\Service\YouTube;

class CommentThreadSnippet extends \Google\Model
{
  /**
   * Whether the current viewer of the thread can reply to it. This is viewer
   * specific - other viewers may see a different value for this field.
   *
   * @var bool
   */
  public $canReply;
  /**
   * The YouTube channel the comments in the thread refer to or the channel with
   * the video the comments refer to. If neither video_id nor post_id is set the
   * comments refer to the channel itself.
   *
   * @var string
   */
  public $channelId;
  /**
   * Whether the thread (and therefore all its comments) is visible to all
   * YouTube users.
   *
   * @var bool
   */
  public $isPublic;
  /**
   * The ID of the post the comments refer to, if any.
   *
   * @var string
   */
  public $postId;
  protected $topLevelCommentType = Comment::class;
  protected $topLevelCommentDataType = '';
  /**
   * The total number of replies (not including the top level comment).
   *
   * @var string
   */
  public $totalReplyCount;
  /**
   * The ID of the video the comments refer to, if any.
   *
   * @var string
   */
  public $videoId;

  /**
   * Whether the current viewer of the thread can reply to it. This is viewer
   * specific - other viewers may see a different value for this field.
   *
   * @param bool $canReply
   */
  public function setCanReply($canReply)
  {
    $this->canReply = $canReply;
  }
  /**
   * @return bool
   */
  public function getCanReply()
  {
    return $this->canReply;
  }
  /**
   * The YouTube channel the comments in the thread refer to or the channel with
   * the video the comments refer to. If neither video_id nor post_id is set the
   * comments refer to the channel itself.
   *
   * @param string $channelId
   */
  public function setChannelId($channelId)
  {
    $this->channelId = $channelId;
  }
  /**
   * @return string
   */
  public function getChannelId()
  {
    return $this->channelId;
  }
  /**
   * Whether the thread (and therefore all its comments) is visible to all
   * YouTube users.
   *
   * @param bool $isPublic
   */
  public function setIsPublic($isPublic)
  {
    $this->isPublic = $isPublic;
  }
  /**
   * @return bool
   */
  public function getIsPublic()
  {
    return $this->isPublic;
  }
  /**
   * The ID of the post the comments refer to, if any.
   *
   * @param string $postId
   */
  public function setPostId($postId)
  {
    $this->postId = $postId;
  }
  /**
   * @return string
   */
  public function getPostId()
  {
    return $this->postId;
  }
  /**
   * The top level comment of this thread.
   *
   * @param Comment $topLevelComment
   */
  public function setTopLevelComment(Comment $topLevelComment)
  {
    $this->topLevelComment = $topLevelComment;
  }
  /**
   * @return Comment
   */
  public function getTopLevelComment()
  {
    return $this->topLevelComment;
  }
  /**
   * The total number of replies (not including the top level comment).
   *
   * @param string $totalReplyCount
   */
  public function setTotalReplyCount($totalReplyCount)
  {
    $this->totalReplyCount = $totalReplyCount;
  }
  /**
   * @return string
   */
  public function getTotalReplyCount()
  {
    return $this->totalReplyCount;
  }
  /**
   * The ID of the video the comments refer to, if any.
   *
   * @param string $videoId
   */
  public function setVideoId($videoId)
  {
    $this->videoId = $videoId;
  }
  /**
   * @return string
   */
  public function getVideoId()
  {
    return $this->videoId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommentThreadSnippet::class, 'Google_Service_YouTube_CommentThreadSnippet');

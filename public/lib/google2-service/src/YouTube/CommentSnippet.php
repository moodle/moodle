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

class CommentSnippet extends \Google\Model
{
  /**
   * The comment is available for public display.
   */
  public const MODERATION_STATUS_published = 'published';
  /**
   * The comment is awaiting review by a moderator.
   */
  public const MODERATION_STATUS_heldForReview = 'heldForReview';
  public const MODERATION_STATUS_likelySpam = 'likelySpam';
  /**
   * The comment is unfit for display.
   */
  public const MODERATION_STATUS_rejected = 'rejected';
  public const VIEWER_RATING_none = 'none';
  /**
   * The entity is liked.
   */
  public const VIEWER_RATING_like = 'like';
  /**
   * The entity is disliked.
   */
  public const VIEWER_RATING_dislike = 'dislike';
  protected $authorChannelIdType = CommentSnippetAuthorChannelId::class;
  protected $authorChannelIdDataType = '';
  /**
   * Link to the author's YouTube channel, if any.
   *
   * @var string
   */
  public $authorChannelUrl;
  /**
   * The name of the user who posted the comment.
   *
   * @var string
   */
  public $authorDisplayName;
  /**
   * The URL for the avatar of the user who posted the comment.
   *
   * @var string
   */
  public $authorProfileImageUrl;
  /**
   * Whether the current viewer can rate this comment.
   *
   * @var bool
   */
  public $canRate;
  /**
   * The id of the corresponding YouTube channel. In case of a channel comment
   * this is the channel the comment refers to. In case of a video or post
   * comment it's the video/post's channel.
   *
   * @var string
   */
  public $channelId;
  /**
   * The total number of likes this comment has received.
   *
   * @var string
   */
  public $likeCount;
  /**
   * The comment's moderation status. Will not be set if the comments were
   * requested through the id filter.
   *
   * @var string
   */
  public $moderationStatus;
  /**
   * The unique id of the top-level comment, only set for replies.
   *
   * @var string
   */
  public $parentId;
  /**
   * The ID of the post the comment refers to, if any.
   *
   * @var string
   */
  public $postId;
  /**
   * The date and time when the comment was originally published.
   *
   * @var string
   */
  public $publishedAt;
  /**
   * The comment's text. The format is either plain text or HTML dependent on
   * what has been requested. Even the plain text representation may differ from
   * the text originally posted in that it may replace video links with video
   * titles etc.
   *
   * @var string
   */
  public $textDisplay;
  /**
   * The comment's original raw text as initially posted or last updated. The
   * original text will only be returned if it is accessible to the viewer,
   * which is only guaranteed if the viewer is the comment's author.
   *
   * @var string
   */
  public $textOriginal;
  /**
   * The date and time when the comment was last updated.
   *
   * @var string
   */
  public $updatedAt;
  /**
   * The ID of the video the comment refers to, if any.
   *
   * @var string
   */
  public $videoId;
  /**
   * The rating the viewer has given to this comment. For the time being this
   * will never return RATE_TYPE_DISLIKE and instead return RATE_TYPE_NONE. This
   * may change in the future.
   *
   * @var string
   */
  public $viewerRating;

  /**
   * @param CommentSnippetAuthorChannelId $authorChannelId
   */
  public function setAuthorChannelId(CommentSnippetAuthorChannelId $authorChannelId)
  {
    $this->authorChannelId = $authorChannelId;
  }
  /**
   * @return CommentSnippetAuthorChannelId
   */
  public function getAuthorChannelId()
  {
    return $this->authorChannelId;
  }
  /**
   * Link to the author's YouTube channel, if any.
   *
   * @param string $authorChannelUrl
   */
  public function setAuthorChannelUrl($authorChannelUrl)
  {
    $this->authorChannelUrl = $authorChannelUrl;
  }
  /**
   * @return string
   */
  public function getAuthorChannelUrl()
  {
    return $this->authorChannelUrl;
  }
  /**
   * The name of the user who posted the comment.
   *
   * @param string $authorDisplayName
   */
  public function setAuthorDisplayName($authorDisplayName)
  {
    $this->authorDisplayName = $authorDisplayName;
  }
  /**
   * @return string
   */
  public function getAuthorDisplayName()
  {
    return $this->authorDisplayName;
  }
  /**
   * The URL for the avatar of the user who posted the comment.
   *
   * @param string $authorProfileImageUrl
   */
  public function setAuthorProfileImageUrl($authorProfileImageUrl)
  {
    $this->authorProfileImageUrl = $authorProfileImageUrl;
  }
  /**
   * @return string
   */
  public function getAuthorProfileImageUrl()
  {
    return $this->authorProfileImageUrl;
  }
  /**
   * Whether the current viewer can rate this comment.
   *
   * @param bool $canRate
   */
  public function setCanRate($canRate)
  {
    $this->canRate = $canRate;
  }
  /**
   * @return bool
   */
  public function getCanRate()
  {
    return $this->canRate;
  }
  /**
   * The id of the corresponding YouTube channel. In case of a channel comment
   * this is the channel the comment refers to. In case of a video or post
   * comment it's the video/post's channel.
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
   * The total number of likes this comment has received.
   *
   * @param string $likeCount
   */
  public function setLikeCount($likeCount)
  {
    $this->likeCount = $likeCount;
  }
  /**
   * @return string
   */
  public function getLikeCount()
  {
    return $this->likeCount;
  }
  /**
   * The comment's moderation status. Will not be set if the comments were
   * requested through the id filter.
   *
   * Accepted values: published, heldForReview, likelySpam, rejected
   *
   * @param self::MODERATION_STATUS_* $moderationStatus
   */
  public function setModerationStatus($moderationStatus)
  {
    $this->moderationStatus = $moderationStatus;
  }
  /**
   * @return self::MODERATION_STATUS_*
   */
  public function getModerationStatus()
  {
    return $this->moderationStatus;
  }
  /**
   * The unique id of the top-level comment, only set for replies.
   *
   * @param string $parentId
   */
  public function setParentId($parentId)
  {
    $this->parentId = $parentId;
  }
  /**
   * @return string
   */
  public function getParentId()
  {
    return $this->parentId;
  }
  /**
   * The ID of the post the comment refers to, if any.
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
   * The date and time when the comment was originally published.
   *
   * @param string $publishedAt
   */
  public function setPublishedAt($publishedAt)
  {
    $this->publishedAt = $publishedAt;
  }
  /**
   * @return string
   */
  public function getPublishedAt()
  {
    return $this->publishedAt;
  }
  /**
   * The comment's text. The format is either plain text or HTML dependent on
   * what has been requested. Even the plain text representation may differ from
   * the text originally posted in that it may replace video links with video
   * titles etc.
   *
   * @param string $textDisplay
   */
  public function setTextDisplay($textDisplay)
  {
    $this->textDisplay = $textDisplay;
  }
  /**
   * @return string
   */
  public function getTextDisplay()
  {
    return $this->textDisplay;
  }
  /**
   * The comment's original raw text as initially posted or last updated. The
   * original text will only be returned if it is accessible to the viewer,
   * which is only guaranteed if the viewer is the comment's author.
   *
   * @param string $textOriginal
   */
  public function setTextOriginal($textOriginal)
  {
    $this->textOriginal = $textOriginal;
  }
  /**
   * @return string
   */
  public function getTextOriginal()
  {
    return $this->textOriginal;
  }
  /**
   * The date and time when the comment was last updated.
   *
   * @param string $updatedAt
   */
  public function setUpdatedAt($updatedAt)
  {
    $this->updatedAt = $updatedAt;
  }
  /**
   * @return string
   */
  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }
  /**
   * The ID of the video the comment refers to, if any.
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
  /**
   * The rating the viewer has given to this comment. For the time being this
   * will never return RATE_TYPE_DISLIKE and instead return RATE_TYPE_NONE. This
   * may change in the future.
   *
   * Accepted values: none, like, dislike
   *
   * @param self::VIEWER_RATING_* $viewerRating
   */
  public function setViewerRating($viewerRating)
  {
    $this->viewerRating = $viewerRating;
  }
  /**
   * @return self::VIEWER_RATING_*
   */
  public function getViewerRating()
  {
    return $this->viewerRating;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommentSnippet::class, 'Google_Service_YouTube_CommentSnippet');

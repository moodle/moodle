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

class ActivityContentDetails extends \Google\Model
{
  protected $bulletinType = ActivityContentDetailsBulletin::class;
  protected $bulletinDataType = '';
  protected $channelItemType = ActivityContentDetailsChannelItem::class;
  protected $channelItemDataType = '';
  protected $commentType = ActivityContentDetailsComment::class;
  protected $commentDataType = '';
  protected $favoriteType = ActivityContentDetailsFavorite::class;
  protected $favoriteDataType = '';
  protected $likeType = ActivityContentDetailsLike::class;
  protected $likeDataType = '';
  protected $playlistItemType = ActivityContentDetailsPlaylistItem::class;
  protected $playlistItemDataType = '';
  protected $promotedItemType = ActivityContentDetailsPromotedItem::class;
  protected $promotedItemDataType = '';
  protected $recommendationType = ActivityContentDetailsRecommendation::class;
  protected $recommendationDataType = '';
  protected $socialType = ActivityContentDetailsSocial::class;
  protected $socialDataType = '';
  protected $subscriptionType = ActivityContentDetailsSubscription::class;
  protected $subscriptionDataType = '';
  protected $uploadType = ActivityContentDetailsUpload::class;
  protected $uploadDataType = '';

  /**
   * The bulletin object contains details about a channel bulletin post. This
   * object is only present if the snippet.type is bulletin.
   *
   * @param ActivityContentDetailsBulletin $bulletin
   */
  public function setBulletin(ActivityContentDetailsBulletin $bulletin)
  {
    $this->bulletin = $bulletin;
  }
  /**
   * @return ActivityContentDetailsBulletin
   */
  public function getBulletin()
  {
    return $this->bulletin;
  }
  /**
   * The channelItem object contains details about a resource which was added to
   * a channel. This property is only present if the snippet.type is
   * channelItem.
   *
   * @param ActivityContentDetailsChannelItem $channelItem
   */
  public function setChannelItem(ActivityContentDetailsChannelItem $channelItem)
  {
    $this->channelItem = $channelItem;
  }
  /**
   * @return ActivityContentDetailsChannelItem
   */
  public function getChannelItem()
  {
    return $this->channelItem;
  }
  /**
   * The comment object contains information about a resource that received a
   * comment. This property is only present if the snippet.type is comment.
   *
   * @param ActivityContentDetailsComment $comment
   */
  public function setComment(ActivityContentDetailsComment $comment)
  {
    $this->comment = $comment;
  }
  /**
   * @return ActivityContentDetailsComment
   */
  public function getComment()
  {
    return $this->comment;
  }
  /**
   * The favorite object contains information about a video that was marked as a
   * favorite video. This property is only present if the snippet.type is
   * favorite.
   *
   * @param ActivityContentDetailsFavorite $favorite
   */
  public function setFavorite(ActivityContentDetailsFavorite $favorite)
  {
    $this->favorite = $favorite;
  }
  /**
   * @return ActivityContentDetailsFavorite
   */
  public function getFavorite()
  {
    return $this->favorite;
  }
  /**
   * The like object contains information about a resource that received a
   * positive (like) rating. This property is only present if the snippet.type
   * is like.
   *
   * @param ActivityContentDetailsLike $like
   */
  public function setLike(ActivityContentDetailsLike $like)
  {
    $this->like = $like;
  }
  /**
   * @return ActivityContentDetailsLike
   */
  public function getLike()
  {
    return $this->like;
  }
  /**
   * The playlistItem object contains information about a new playlist item.
   * This property is only present if the snippet.type is playlistItem.
   *
   * @param ActivityContentDetailsPlaylistItem $playlistItem
   */
  public function setPlaylistItem(ActivityContentDetailsPlaylistItem $playlistItem)
  {
    $this->playlistItem = $playlistItem;
  }
  /**
   * @return ActivityContentDetailsPlaylistItem
   */
  public function getPlaylistItem()
  {
    return $this->playlistItem;
  }
  /**
   * The promotedItem object contains details about a resource which is being
   * promoted. This property is only present if the snippet.type is
   * promotedItem.
   *
   * @param ActivityContentDetailsPromotedItem $promotedItem
   */
  public function setPromotedItem(ActivityContentDetailsPromotedItem $promotedItem)
  {
    $this->promotedItem = $promotedItem;
  }
  /**
   * @return ActivityContentDetailsPromotedItem
   */
  public function getPromotedItem()
  {
    return $this->promotedItem;
  }
  /**
   * The recommendation object contains information about a recommended
   * resource. This property is only present if the snippet.type is
   * recommendation.
   *
   * @param ActivityContentDetailsRecommendation $recommendation
   */
  public function setRecommendation(ActivityContentDetailsRecommendation $recommendation)
  {
    $this->recommendation = $recommendation;
  }
  /**
   * @return ActivityContentDetailsRecommendation
   */
  public function getRecommendation()
  {
    return $this->recommendation;
  }
  /**
   * The social object contains details about a social network post. This
   * property is only present if the snippet.type is social.
   *
   * @param ActivityContentDetailsSocial $social
   */
  public function setSocial(ActivityContentDetailsSocial $social)
  {
    $this->social = $social;
  }
  /**
   * @return ActivityContentDetailsSocial
   */
  public function getSocial()
  {
    return $this->social;
  }
  /**
   * The subscription object contains information about a channel that a user
   * subscribed to. This property is only present if the snippet.type is
   * subscription.
   *
   * @param ActivityContentDetailsSubscription $subscription
   */
  public function setSubscription(ActivityContentDetailsSubscription $subscription)
  {
    $this->subscription = $subscription;
  }
  /**
   * @return ActivityContentDetailsSubscription
   */
  public function getSubscription()
  {
    return $this->subscription;
  }
  /**
   * The upload object contains information about the uploaded video. This
   * property is only present if the snippet.type is upload.
   *
   * @param ActivityContentDetailsUpload $upload
   */
  public function setUpload(ActivityContentDetailsUpload $upload)
  {
    $this->upload = $upload;
  }
  /**
   * @return ActivityContentDetailsUpload
   */
  public function getUpload()
  {
    return $this->upload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActivityContentDetails::class, 'Google_Service_YouTube_ActivityContentDetails');

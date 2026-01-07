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

namespace Google\Service\Blogger;

class PostUserInfo extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "postUserInfo" => "post_user_info",
  ];
  /**
   * The kind of this entity. Always blogger#postUserInfo.
   *
   * @var string
   */
  public $kind;
  protected $postType = Post::class;
  protected $postDataType = '';
  protected $postUserInfoType = PostPerUserInfo::class;
  protected $postUserInfoDataType = '';

  /**
   * The kind of this entity. Always blogger#postUserInfo.
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
   * The Post resource.
   *
   * @param Post $post
   */
  public function setPost(Post $post)
  {
    $this->post = $post;
  }
  /**
   * @return Post
   */
  public function getPost()
  {
    return $this->post;
  }
  /**
   * Information about a User for the Post.
   *
   * @param PostPerUserInfo $postUserInfo
   */
  public function setPostUserInfo(PostPerUserInfo $postUserInfo)
  {
    $this->postUserInfo = $postUserInfo;
  }
  /**
   * @return PostPerUserInfo
   */
  public function getPostUserInfo()
  {
    return $this->postUserInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostUserInfo::class, 'Google_Service_Blogger_PostUserInfo');

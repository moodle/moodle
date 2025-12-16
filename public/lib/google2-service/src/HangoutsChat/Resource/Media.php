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

namespace Google\Service\HangoutsChat\Resource;

use Google\Service\HangoutsChat\Media as MediaModel;
use Google\Service\HangoutsChat\UploadAttachmentRequest;
use Google\Service\HangoutsChat\UploadAttachmentResponse;

/**
 * The "media" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chatService = new Google\Service\HangoutsChat(...);
 *   $media = $chatService->media;
 *  </code>
 */
class Media extends \Google\Service\Resource
{
  /**
   * Downloads media. Download is supported on the URI
   * `/v1/media/{+name}?alt=media`. (media.download)
   *
   * @param string $resourceName Name of the media that is being downloaded. See
   * ReadRequest.resource_name.
   * @param array $optParams Optional parameters.
   * @return MediaModel
   * @throws \Google\Service\Exception
   */
  public function download($resourceName, $optParams = [])
  {
    $params = ['resourceName' => $resourceName];
    $params = array_merge($params, $optParams);
    return $this->call('download', [$params], MediaModel::class);
  }
  /**
   * Uploads an attachment. For an example, see [Upload media as a file
   * attachment](https://developers.google.com/workspace/chat/upload-media-
   * attachments). Requires user
   * [authentication](https://developers.google.com/workspace/chat/authenticate-
   * authorize-chat-user) with one of the following [authorization
   * scopes](https://developers.google.com/workspace/chat/authenticate-
   * authorize#chat-api-scopes): -
   * `https://www.googleapis.com/auth/chat.messages.create` -
   * `https://www.googleapis.com/auth/chat.messages` -
   * `https://www.googleapis.com/auth/chat.import` (import mode spaces only) You
   * can upload attachments up to 200 MB. Certain file types aren't supported. For
   * details, see [File types blocked by Google Chat](https://support.google.com/c
   * hat/answer/7651457?&co=GENIE.Platform%3DDesktop#File%20types%20blocked%20in%2
   * 0Google%20Chat). (media.upload)
   *
   * @param string $parent Required. Resource name of the Chat space in which the
   * attachment is uploaded. Format "spaces/{space}".
   * @param UploadAttachmentRequest $postBody
   * @param array $optParams Optional parameters.
   * @return UploadAttachmentResponse
   * @throws \Google\Service\Exception
   */
  public function upload($parent, UploadAttachmentRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upload', [$params], UploadAttachmentResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Media::class, 'Google_Service_HangoutsChat_Resource_Media');

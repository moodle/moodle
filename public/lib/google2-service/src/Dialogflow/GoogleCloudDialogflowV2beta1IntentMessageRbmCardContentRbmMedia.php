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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2beta1IntentMessageRbmCardContentRbmMedia extends \Google\Model
{
  /**
   * Not specified.
   */
  public const HEIGHT_HEIGHT_UNSPECIFIED = 'HEIGHT_UNSPECIFIED';
  /**
   * 112 DP.
   */
  public const HEIGHT_SHORT = 'SHORT';
  /**
   * 168 DP.
   */
  public const HEIGHT_MEDIUM = 'MEDIUM';
  /**
   * 264 DP. Not available for rich card carousels when the card width is set to
   * small.
   */
  public const HEIGHT_TALL = 'TALL';
  /**
   * Required. Publicly reachable URI of the file. The RBM platform determines
   * the MIME type of the file from the content-type field in the HTTP headers
   * when the platform fetches the file. The content-type field must be present
   * and accurate in the HTTP response from the URL.
   *
   * @var string
   */
  public $fileUri;
  /**
   * Required for cards with vertical orientation. The height of the media
   * within a rich card with a vertical layout. For a standalone card with
   * horizontal layout, height is not customizable, and this field is ignored.
   *
   * @var string
   */
  public $height;
  /**
   * Optional. Publicly reachable URI of the thumbnail.If you don't provide a
   * thumbnail URI, the RBM platform displays a blank placeholder thumbnail
   * until the user's device downloads the file. Depending on the user's
   * setting, the file may not download automatically and may require the user
   * to tap a download button.
   *
   * @var string
   */
  public $thumbnailUri;

  /**
   * Required. Publicly reachable URI of the file. The RBM platform determines
   * the MIME type of the file from the content-type field in the HTTP headers
   * when the platform fetches the file. The content-type field must be present
   * and accurate in the HTTP response from the URL.
   *
   * @param string $fileUri
   */
  public function setFileUri($fileUri)
  {
    $this->fileUri = $fileUri;
  }
  /**
   * @return string
   */
  public function getFileUri()
  {
    return $this->fileUri;
  }
  /**
   * Required for cards with vertical orientation. The height of the media
   * within a rich card with a vertical layout. For a standalone card with
   * horizontal layout, height is not customizable, and this field is ignored.
   *
   * Accepted values: HEIGHT_UNSPECIFIED, SHORT, MEDIUM, TALL
   *
   * @param self::HEIGHT_* $height
   */
  public function setHeight($height)
  {
    $this->height = $height;
  }
  /**
   * @return self::HEIGHT_*
   */
  public function getHeight()
  {
    return $this->height;
  }
  /**
   * Optional. Publicly reachable URI of the thumbnail.If you don't provide a
   * thumbnail URI, the RBM platform displays a blank placeholder thumbnail
   * until the user's device downloads the file. Depending on the user's
   * setting, the file may not download automatically and may require the user
   * to tap a download button.
   *
   * @param string $thumbnailUri
   */
  public function setThumbnailUri($thumbnailUri)
  {
    $this->thumbnailUri = $thumbnailUri;
  }
  /**
   * @return string
   */
  public function getThumbnailUri()
  {
    return $this->thumbnailUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1IntentMessageRbmCardContentRbmMedia::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentMessageRbmCardContentRbmMedia');

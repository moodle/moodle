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

namespace Google\Service\HangoutsChat;

class Attachment extends \Google\Model
{
  /**
   * Reserved.
   */
  public const SOURCE_SOURCE_UNSPECIFIED = 'SOURCE_UNSPECIFIED';
  /**
   * The file is a Google Drive file.
   */
  public const SOURCE_DRIVE_FILE = 'DRIVE_FILE';
  /**
   * The file is uploaded to Chat.
   */
  public const SOURCE_UPLOADED_CONTENT = 'UPLOADED_CONTENT';
  protected $attachmentDataRefType = AttachmentDataRef::class;
  protected $attachmentDataRefDataType = '';
  /**
   * Output only. The original file name for the content, not the full path.
   *
   * @var string
   */
  public $contentName;
  /**
   * Output only. The content type (MIME type) of the file.
   *
   * @var string
   */
  public $contentType;
  /**
   * Output only. The download URL which should be used to allow a human user to
   * download the attachment. Chat apps shouldn't use this URL to download
   * attachment content.
   *
   * @var string
   */
  public $downloadUri;
  protected $driveDataRefType = DriveDataRef::class;
  protected $driveDataRefDataType = '';
  /**
   * Identifier. Resource name of the attachment. Format:
   * `spaces/{space}/messages/{message}/attachments/{attachment}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The source of the attachment.
   *
   * @var string
   */
  public $source;
  /**
   * Output only. The thumbnail URL which should be used to preview the
   * attachment to a human user. Chat apps shouldn't use this URL to download
   * attachment content.
   *
   * @var string
   */
  public $thumbnailUri;

  /**
   * Optional. A reference to the attachment data. This field is used to create
   * or update messages with attachments, or with the media API to download the
   * attachment data.
   *
   * @param AttachmentDataRef $attachmentDataRef
   */
  public function setAttachmentDataRef(AttachmentDataRef $attachmentDataRef)
  {
    $this->attachmentDataRef = $attachmentDataRef;
  }
  /**
   * @return AttachmentDataRef
   */
  public function getAttachmentDataRef()
  {
    return $this->attachmentDataRef;
  }
  /**
   * Output only. The original file name for the content, not the full path.
   *
   * @param string $contentName
   */
  public function setContentName($contentName)
  {
    $this->contentName = $contentName;
  }
  /**
   * @return string
   */
  public function getContentName()
  {
    return $this->contentName;
  }
  /**
   * Output only. The content type (MIME type) of the file.
   *
   * @param string $contentType
   */
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }
  /**
   * @return string
   */
  public function getContentType()
  {
    return $this->contentType;
  }
  /**
   * Output only. The download URL which should be used to allow a human user to
   * download the attachment. Chat apps shouldn't use this URL to download
   * attachment content.
   *
   * @param string $downloadUri
   */
  public function setDownloadUri($downloadUri)
  {
    $this->downloadUri = $downloadUri;
  }
  /**
   * @return string
   */
  public function getDownloadUri()
  {
    return $this->downloadUri;
  }
  /**
   * Output only. A reference to the Google Drive attachment. This field is used
   * with the Google Drive API.
   *
   * @param DriveDataRef $driveDataRef
   */
  public function setDriveDataRef(DriveDataRef $driveDataRef)
  {
    $this->driveDataRef = $driveDataRef;
  }
  /**
   * @return DriveDataRef
   */
  public function getDriveDataRef()
  {
    return $this->driveDataRef;
  }
  /**
   * Identifier. Resource name of the attachment. Format:
   * `spaces/{space}/messages/{message}/attachments/{attachment}`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The source of the attachment.
   *
   * Accepted values: SOURCE_UNSPECIFIED, DRIVE_FILE, UPLOADED_CONTENT
   *
   * @param self::SOURCE_* $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return self::SOURCE_*
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Output only. The thumbnail URL which should be used to preview the
   * attachment to a human user. Chat apps shouldn't use this URL to download
   * attachment content.
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
class_alias(Attachment::class, 'Google_Service_HangoutsChat_Attachment');

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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickCommonDocument extends \Google\Model
{
  /**
   * Unknown access type.
   */
  public const ACCESS_TYPE_UNKNOWN_ACCESS = 'UNKNOWN_ACCESS';
  /**
   * Access allowed.
   */
  public const ACCESS_TYPE_ALLOWED = 'ALLOWED';
  /**
   * Access not allowed.
   */
  public const ACCESS_TYPE_NOT_ALLOWED = 'NOT_ALLOWED';
  /**
   * Unknown provenance.
   */
  public const PROVENANCE_UNKNOWN_PROVENANCE = 'UNKNOWN_PROVENANCE';
  /**
   * Calendar event description.
   */
  public const PROVENANCE_CALENDAR_DESCRIPTION = 'CALENDAR_DESCRIPTION';
  /**
   * Calendar event attachment.
   */
  public const PROVENANCE_CALENDAR_ATTACHMENT = 'CALENDAR_ATTACHMENT';
  /**
   * Mined (extracted by some offline/online analysis).
   */
  public const PROVENANCE_MINED = 'MINED';
  /**
   * Attachment created by enterprise assist.
   */
  public const PROVENANCE_CALENDAR_ASSIST_ATTACHMENT = 'CALENDAR_ASSIST_ATTACHMENT';
  /**
   * Unknown justification.
   */
  public const REASON_UNKNOWN = 'UNKNOWN';
  /**
   * Popular documents within collaborators.
   */
  public const REASON_TRENDING_IN_COLLABORATORS = 'TRENDING_IN_COLLABORATORS';
  /**
   * Popular documents within the domain.
   */
  public const REASON_TRENDING_IN_DOMAIN = 'TRENDING_IN_DOMAIN';
  /**
   * Documents being reviewed frequently by the current user .
   */
  public const REASON_FREQUENTLY_VIEWED = 'FREQUENTLY_VIEWED';
  /**
   * Documents being edited frequently by the current user .
   */
  public const REASON_FREQUENTLY_EDITED = 'FREQUENTLY_EDITED';
  /**
   * Documents updated since user's last visit.
   */
  public const REASON_NEW_UPDATES = 'NEW_UPDATES';
  /**
   * Documents that receive comments since user's last visit.
   */
  public const REASON_NEW_COMMENTS = 'NEW_COMMENTS';
  /**
   * Documents in the calendar event description.
   */
  public const REASON_EVENT_DESCRIPTION = 'EVENT_DESCRIPTION';
  /**
   * Documents in the calendar event attachments section.
   */
  public const REASON_EVENT_ATTACHMENT = 'EVENT_ATTACHMENT';
  /**
   * Documents attached in calendar event metadata instead of the attachment
   * section. Event metadata is not visible to the final user. Enterprise assist
   * uses this metadata to store auto-generated documents such as meeting notes.
   */
  public const REASON_EVENT_METADATA_ATTACHMENT = 'EVENT_METADATA_ATTACHMENT';
  /**
   * Documents mined, and so, probably related to the request context. For
   * example, this category includes documents related to a meeting.
   */
  public const REASON_MINED_DOCUMENT = 'MINED_DOCUMENT';
  /**
   * Documents that contains mentions of the user.
   */
  public const REASON_NEW_MENTIONS = 'NEW_MENTIONS';
  /**
   * Documents that are shared with the user.
   */
  public const REASON_NEW_SHARES = 'NEW_SHARES';
  /**
   * If the type is unknown or not represented in this enum.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Drive document types Writely, Word, etc.
   */
  public const TYPE_DOCUMENT = 'DOCUMENT';
  /**
   * Presently, PowerPoint, etc.
   */
  public const TYPE_PRESENTATION = 'PRESENTATION';
  /**
   * Trix, Excel, etc.
   */
  public const TYPE_SPREADSHEET = 'SPREADSHEET';
  /**
   * File types for Gdrive objects are below. PDF.
   */
  public const TYPE_PDF = 'PDF';
  /**
   * Image.
   */
  public const TYPE_IMAGE = 'IMAGE';
  /**
   * Fall-back for unknown Gdrive types.
   */
  public const TYPE_BINARY_BLOB = 'BINARY_BLOB';
  /**
   * Fusion table.
   */
  public const TYPE_FUSION_TABLE = 'FUSION_TABLE';
  /**
   * Folder.
   */
  public const TYPE_FOLDER = 'FOLDER';
  /**
   * Drawing.
   */
  public const TYPE_DRAWING = 'DRAWING';
  /**
   * Video.
   */
  public const TYPE_VIDEO = 'VIDEO';
  /**
   * Form.
   */
  public const TYPE_FORM = 'FORM';
  /**
   * Link formats uncategorized URL links
   */
  public const TYPE_LINK_URL = 'LINK_URL';
  /**
   * meaningful links that should be renderred specifically
   */
  public const TYPE_LINK_GO = 'LINK_GO';
  /**
   * Link to goo.gl.
   */
  public const TYPE_LINK_GOO_GL = 'LINK_GOO_GL';
  /**
   * Link to bit_ly.
   */
  public const TYPE_LINK_BIT_LY = 'LINK_BIT_LY';
  /**
   * Link to Gmail.
   */
  public const TYPE_LINK_GMAIL = 'LINK_GMAIL';
  /**
   * Mailto link.
   */
  public const TYPE_LINK_MAILTO = 'LINK_MAILTO';
  /**
   * Videos Youtube videos.
   */
  public const TYPE_VIDEO_YOUTUBE = 'VIDEO_YOUTUBE';
  /**
   * Live streams (e.g., liveplayer.googleplex.com)
   */
  public const TYPE_VIDEO_LIVE = 'VIDEO_LIVE';
  /**
   * Other types. Google Groups.
   */
  public const TYPE_GROUPS = 'GROUPS';
  /**
   * Google News.
   */
  public const TYPE_NEWS = 'NEWS';
  /**
   * Google Sites.
   */
  public const TYPE_SITES = 'SITES';
  /**
   * Google Hangout.
   */
  public const TYPE_HANGOUT = 'HANGOUT';
  /**
   * Audio files.
   */
  public const TYPE_AUDIO = 'AUDIO';
  /**
   * Microsoft-specific file types.
   */
  public const TYPE_MS_WORD = 'MS_WORD';
  public const TYPE_MS_POWERPOINT = 'MS_POWERPOINT';
  public const TYPE_MS_EXCEL = 'MS_EXCEL';
  public const TYPE_MS_OUTLOOK = 'MS_OUTLOOK';
  /**
   * Access type, i.e., whether the user has access to the document or not.
   *
   * @var string
   */
  public $accessType;
  protected $debugInfoType = EnterpriseTopazSidekickCommonDebugInfo::class;
  protected $debugInfoDataType = '';
  /**
   * Document id.
   *
   * @var string
   */
  public $documentId;
  protected $driveDocumentMetadataType = EnterpriseTopazSidekickCommonDocumentDriveDocumentMetadata::class;
  protected $driveDocumentMetadataDataType = '';
  /**
   * Generic Drive-based url in the format of drive.google.com/open to be used
   * for deeplink
   *
   * @var string
   */
  public $genericUrl;
  protected $justificationType = EnterpriseTopazSidekickCommonDocumentJustification::class;
  protected $justificationDataType = '';
  /**
   * MIME type
   *
   * @var string
   */
  public $mimeType;
  /**
   * Document provenance.
   *
   * @deprecated
   * @var string
   */
  public $provenance;
  /**
   * Justification of why this document is being returned.
   *
   * @deprecated
   * @var string
   */
  public $reason;
  /**
   * A sampling of the text from the document.
   *
   * @var string
   */
  public $snippet;
  /**
   * Thumbnail URL.
   *
   * @var string
   */
  public $thumbnailUrl;
  /**
   * Title of the document.
   *
   * @var string
   */
  public $title;
  /**
   * Type of the document.
   *
   * @var string
   */
  public $type;
  /**
   * Absolute URL of the document.
   *
   * @var string
   */
  public $url;

  /**
   * Access type, i.e., whether the user has access to the document or not.
   *
   * Accepted values: UNKNOWN_ACCESS, ALLOWED, NOT_ALLOWED
   *
   * @param self::ACCESS_TYPE_* $accessType
   */
  public function setAccessType($accessType)
  {
    $this->accessType = $accessType;
  }
  /**
   * @return self::ACCESS_TYPE_*
   */
  public function getAccessType()
  {
    return $this->accessType;
  }
  /**
   * Information for debugging.
   *
   * @param EnterpriseTopazSidekickCommonDebugInfo $debugInfo
   */
  public function setDebugInfo(EnterpriseTopazSidekickCommonDebugInfo $debugInfo)
  {
    $this->debugInfo = $debugInfo;
  }
  /**
   * @return EnterpriseTopazSidekickCommonDebugInfo
   */
  public function getDebugInfo()
  {
    return $this->debugInfo;
  }
  /**
   * Document id.
   *
   * @param string $documentId
   */
  public function setDocumentId($documentId)
  {
    $this->documentId = $documentId;
  }
  /**
   * @return string
   */
  public function getDocumentId()
  {
    return $this->documentId;
  }
  /**
   * Drive document metadata.
   *
   * @param EnterpriseTopazSidekickCommonDocumentDriveDocumentMetadata $driveDocumentMetadata
   */
  public function setDriveDocumentMetadata(EnterpriseTopazSidekickCommonDocumentDriveDocumentMetadata $driveDocumentMetadata)
  {
    $this->driveDocumentMetadata = $driveDocumentMetadata;
  }
  /**
   * @return EnterpriseTopazSidekickCommonDocumentDriveDocumentMetadata
   */
  public function getDriveDocumentMetadata()
  {
    return $this->driveDocumentMetadata;
  }
  /**
   * Generic Drive-based url in the format of drive.google.com/open to be used
   * for deeplink
   *
   * @param string $genericUrl
   */
  public function setGenericUrl($genericUrl)
  {
    $this->genericUrl = $genericUrl;
  }
  /**
   * @return string
   */
  public function getGenericUrl()
  {
    return $this->genericUrl;
  }
  /**
   * Justification on why the document is selected.
   *
   * @param EnterpriseTopazSidekickCommonDocumentJustification $justification
   */
  public function setJustification(EnterpriseTopazSidekickCommonDocumentJustification $justification)
  {
    $this->justification = $justification;
  }
  /**
   * @return EnterpriseTopazSidekickCommonDocumentJustification
   */
  public function getJustification()
  {
    return $this->justification;
  }
  /**
   * MIME type
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * Document provenance.
   *
   * Accepted values: UNKNOWN_PROVENANCE, CALENDAR_DESCRIPTION,
   * CALENDAR_ATTACHMENT, MINED, CALENDAR_ASSIST_ATTACHMENT
   *
   * @deprecated
   * @param self::PROVENANCE_* $provenance
   */
  public function setProvenance($provenance)
  {
    $this->provenance = $provenance;
  }
  /**
   * @deprecated
   * @return self::PROVENANCE_*
   */
  public function getProvenance()
  {
    return $this->provenance;
  }
  /**
   * Justification of why this document is being returned.
   *
   * Accepted values: UNKNOWN, TRENDING_IN_COLLABORATORS, TRENDING_IN_DOMAIN,
   * FREQUENTLY_VIEWED, FREQUENTLY_EDITED, NEW_UPDATES, NEW_COMMENTS,
   * EVENT_DESCRIPTION, EVENT_ATTACHMENT, EVENT_METADATA_ATTACHMENT,
   * MINED_DOCUMENT, NEW_MENTIONS, NEW_SHARES
   *
   * @deprecated
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @deprecated
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * A sampling of the text from the document.
   *
   * @param string $snippet
   */
  public function setSnippet($snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return string
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * Thumbnail URL.
   *
   * @param string $thumbnailUrl
   */
  public function setThumbnailUrl($thumbnailUrl)
  {
    $this->thumbnailUrl = $thumbnailUrl;
  }
  /**
   * @return string
   */
  public function getThumbnailUrl()
  {
    return $this->thumbnailUrl;
  }
  /**
   * Title of the document.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Type of the document.
   *
   * Accepted values: UNKNOWN, DOCUMENT, PRESENTATION, SPREADSHEET, PDF, IMAGE,
   * BINARY_BLOB, FUSION_TABLE, FOLDER, DRAWING, VIDEO, FORM, LINK_URL, LINK_GO,
   * LINK_GOO_GL, LINK_BIT_LY, LINK_GMAIL, LINK_MAILTO, VIDEO_YOUTUBE,
   * VIDEO_LIVE, GROUPS, NEWS, SITES, HANGOUT, AUDIO, MS_WORD, MS_POWERPOINT,
   * MS_EXCEL, MS_OUTLOOK
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Absolute URL of the document.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickCommonDocument::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickCommonDocument');

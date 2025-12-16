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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1DocumentContent extends \Google\Model
{
  /**
   * The MIME type of the content. Supported types: * `application/pdf` (PDF,
   * only native PDFs are supported for now) * `text/html` (HTML) * `text/plain`
   * (TXT) * `application/xml` or `text/xml` (XML) * `application/json` (JSON) *
   * `application/vnd.openxmlformats-officedocument.wordprocessingml.document`
   * (DOCX) * `application/vnd.openxmlformats-
   * officedocument.presentationml.presentation` (PPTX) *
   * `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet` (XLSX)
   * * `application/vnd.ms-excel.sheet.macroenabled.12` (XLSM) The following
   * types are supported only if layout parser is enabled in the data store: *
   * `image/bmp` (BMP) * `image/gif` (GIF) * `image/jpeg` (JPEG) * `image/png`
   * (PNG) * `image/tiff` (TIFF) See https://www.iana.org/assignments/media-
   * types/media-types.xhtml.
   *
   * @var string
   */
  public $mimeType;
  /**
   * The content represented as a stream of bytes. The maximum length is
   * 1,000,000 bytes (1 MB / ~0.95 MiB). Note: As with all `bytes` fields, this
   * field is represented as pure binary in Protocol Buffers and base64-encoded
   * string in JSON. For example, `abc123!?$*&()'-=@~` should be represented as
   * `YWJjMTIzIT8kKiYoKSctPUB+` in JSON. See
   * https://developers.google.com/protocol-buffers/docs/proto3#json.
   *
   * @var string
   */
  public $rawBytes;
  /**
   * The URI of the content. Only Cloud Storage URIs (e.g. `gs://bucket-
   * name/path/to/file`) are supported. The maximum file size is 2.5 MB for
   * text-based formats, 200 MB for other formats.
   *
   * @var string
   */
  public $uri;

  /**
   * The MIME type of the content. Supported types: * `application/pdf` (PDF,
   * only native PDFs are supported for now) * `text/html` (HTML) * `text/plain`
   * (TXT) * `application/xml` or `text/xml` (XML) * `application/json` (JSON) *
   * `application/vnd.openxmlformats-officedocument.wordprocessingml.document`
   * (DOCX) * `application/vnd.openxmlformats-
   * officedocument.presentationml.presentation` (PPTX) *
   * `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet` (XLSX)
   * * `application/vnd.ms-excel.sheet.macroenabled.12` (XLSM) The following
   * types are supported only if layout parser is enabled in the data store: *
   * `image/bmp` (BMP) * `image/gif` (GIF) * `image/jpeg` (JPEG) * `image/png`
   * (PNG) * `image/tiff` (TIFF) See https://www.iana.org/assignments/media-
   * types/media-types.xhtml.
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
   * The content represented as a stream of bytes. The maximum length is
   * 1,000,000 bytes (1 MB / ~0.95 MiB). Note: As with all `bytes` fields, this
   * field is represented as pure binary in Protocol Buffers and base64-encoded
   * string in JSON. For example, `abc123!?$*&()'-=@~` should be represented as
   * `YWJjMTIzIT8kKiYoKSctPUB+` in JSON. See
   * https://developers.google.com/protocol-buffers/docs/proto3#json.
   *
   * @param string $rawBytes
   */
  public function setRawBytes($rawBytes)
  {
    $this->rawBytes = $rawBytes;
  }
  /**
   * @return string
   */
  public function getRawBytes()
  {
    return $this->rawBytes;
  }
  /**
   * The URI of the content. Only Cloud Storage URIs (e.g. `gs://bucket-
   * name/path/to/file`) are supported. The maximum file size is 2.5 MB for
   * text-based formats, 200 MB for other formats.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1DocumentContent::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1DocumentContent');

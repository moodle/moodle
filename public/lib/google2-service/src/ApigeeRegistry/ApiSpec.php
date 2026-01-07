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

namespace Google\Service\ApigeeRegistry;

class ApiSpec extends \Google\Model
{
  /**
   * Annotations attach non-identifying metadata to resources. Annotation keys
   * and values are less restricted than those of labels, but should be
   * generally used for small values of broad interest. Larger, topic- specific
   * metadata should be stored in Artifacts.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Input only. The contents of the spec. Provided by API callers when specs
   * are created or updated. To access the contents of a spec, use
   * GetApiSpecContents.
   *
   * @var string
   */
  public $contents;
  /**
   * Output only. Creation timestamp; when the spec resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * A detailed description.
   *
   * @var string
   */
  public $description;
  /**
   * A possibly-hierarchical name used to refer to the spec from other specs.
   *
   * @var string
   */
  public $filename;
  /**
   * Output only. A SHA-256 hash of the spec's contents. If the spec is gzipped,
   * this is the hash of the uncompressed spec.
   *
   * @var string
   */
  public $hash;
  /**
   * Labels attach identifying metadata to resources. Identifying metadata can
   * be used to filter list operations. Label keys and values can be no longer
   * than 64 characters (Unicode codepoints), can only contain lowercase
   * letters, numeric characters, underscores and dashes. International
   * characters are allowed. No more than 64 user labels can be associated with
   * one resource (System labels are excluded). See https://goo.gl/xmQnxf for
   * more information and examples of labels. System reserved label keys are
   * prefixed with `apigeeregistry.googleapis.com/` and cannot be changed.
   *
   * @var string[]
   */
  public $labels;
  /**
   * A style (format) descriptor for this spec that is specified as a [Media
   * Type](https://en.wikipedia.org/wiki/Media_type). Possible values include
   * `application/vnd.apigee.proto`, `application/vnd.apigee.openapi`, and
   * `application/vnd.apigee.graphql`, with possible suffixes representing
   * compression types. These hypothetical names are defined in the vendor tree
   * defined in RFC6838 (https://tools.ietf.org/html/rfc6838) and are not final.
   * Content types can specify compression. Currently only GZip compression is
   * supported (indicated with "+gzip").
   *
   * @var string
   */
  public $mimeType;
  /**
   * Resource name.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Revision creation timestamp; when the represented revision was
   * created.
   *
   * @var string
   */
  public $revisionCreateTime;
  /**
   * Output only. Immutable. The revision ID of the spec. A new revision is
   * committed whenever the spec contents are changed. The format is an
   * 8-character hexadecimal string.
   *
   * @var string
   */
  public $revisionId;
  /**
   * Output only. Last update timestamp: when the represented revision was last
   * modified.
   *
   * @var string
   */
  public $revisionUpdateTime;
  /**
   * Output only. The size of the spec file in bytes. If the spec is gzipped,
   * this is the size of the uncompressed spec.
   *
   * @var int
   */
  public $sizeBytes;
  /**
   * The original source URI of the spec (if one exists). This is an external
   * location that can be used for reference purposes but which may not be
   * authoritative since this external resource may change after the spec is
   * retrieved.
   *
   * @var string
   */
  public $sourceUri;

  /**
   * Annotations attach non-identifying metadata to resources. Annotation keys
   * and values are less restricted than those of labels, but should be
   * generally used for small values of broad interest. Larger, topic- specific
   * metadata should be stored in Artifacts.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Input only. The contents of the spec. Provided by API callers when specs
   * are created or updated. To access the contents of a spec, use
   * GetApiSpecContents.
   *
   * @param string $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return string
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Output only. Creation timestamp; when the spec resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * A detailed description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * A possibly-hierarchical name used to refer to the spec from other specs.
   *
   * @param string $filename
   */
  public function setFilename($filename)
  {
    $this->filename = $filename;
  }
  /**
   * @return string
   */
  public function getFilename()
  {
    return $this->filename;
  }
  /**
   * Output only. A SHA-256 hash of the spec's contents. If the spec is gzipped,
   * this is the hash of the uncompressed spec.
   *
   * @param string $hash
   */
  public function setHash($hash)
  {
    $this->hash = $hash;
  }
  /**
   * @return string
   */
  public function getHash()
  {
    return $this->hash;
  }
  /**
   * Labels attach identifying metadata to resources. Identifying metadata can
   * be used to filter list operations. Label keys and values can be no longer
   * than 64 characters (Unicode codepoints), can only contain lowercase
   * letters, numeric characters, underscores and dashes. International
   * characters are allowed. No more than 64 user labels can be associated with
   * one resource (System labels are excluded). See https://goo.gl/xmQnxf for
   * more information and examples of labels. System reserved label keys are
   * prefixed with `apigeeregistry.googleapis.com/` and cannot be changed.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * A style (format) descriptor for this spec that is specified as a [Media
   * Type](https://en.wikipedia.org/wiki/Media_type). Possible values include
   * `application/vnd.apigee.proto`, `application/vnd.apigee.openapi`, and
   * `application/vnd.apigee.graphql`, with possible suffixes representing
   * compression types. These hypothetical names are defined in the vendor tree
   * defined in RFC6838 (https://tools.ietf.org/html/rfc6838) and are not final.
   * Content types can specify compression. Currently only GZip compression is
   * supported (indicated with "+gzip").
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
   * Resource name.
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
   * Output only. Revision creation timestamp; when the represented revision was
   * created.
   *
   * @param string $revisionCreateTime
   */
  public function setRevisionCreateTime($revisionCreateTime)
  {
    $this->revisionCreateTime = $revisionCreateTime;
  }
  /**
   * @return string
   */
  public function getRevisionCreateTime()
  {
    return $this->revisionCreateTime;
  }
  /**
   * Output only. Immutable. The revision ID of the spec. A new revision is
   * committed whenever the spec contents are changed. The format is an
   * 8-character hexadecimal string.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Output only. Last update timestamp: when the represented revision was last
   * modified.
   *
   * @param string $revisionUpdateTime
   */
  public function setRevisionUpdateTime($revisionUpdateTime)
  {
    $this->revisionUpdateTime = $revisionUpdateTime;
  }
  /**
   * @return string
   */
  public function getRevisionUpdateTime()
  {
    return $this->revisionUpdateTime;
  }
  /**
   * Output only. The size of the spec file in bytes. If the spec is gzipped,
   * this is the size of the uncompressed spec.
   *
   * @param int $sizeBytes
   */
  public function setSizeBytes($sizeBytes)
  {
    $this->sizeBytes = $sizeBytes;
  }
  /**
   * @return int
   */
  public function getSizeBytes()
  {
    return $this->sizeBytes;
  }
  /**
   * The original source URI of the spec (if one exists). This is an external
   * location that can be used for reference purposes but which may not be
   * authoritative since this external resource may change after the spec is
   * retrieved.
   *
   * @param string $sourceUri
   */
  public function setSourceUri($sourceUri)
  {
    $this->sourceUri = $sourceUri;
  }
  /**
   * @return string
   */
  public function getSourceUri()
  {
    return $this->sourceUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApiSpec::class, 'Google_Service_ApigeeRegistry_ApiSpec');

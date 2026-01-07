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

class Artifact extends \Google\Model
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
   * Input only. The contents of the artifact. Provided by API callers when
   * artifacts are created or replaced. To access the contents of an artifact,
   * use GetArtifactContents.
   *
   * @var string
   */
  public $contents;
  /**
   * Output only. Creation timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. A SHA-256 hash of the artifact's contents. If the artifact is
   * gzipped, this is the hash of the uncompressed artifact.
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
   * prefixed with "registry.googleapis.com/" and cannot be changed.
   *
   * @var string[]
   */
  public $labels;
  /**
   * A content type specifier for the artifact. Content type specifiers are
   * Media Types (https://en.wikipedia.org/wiki/Media_type) with a possible
   * "schema" parameter that specifies a schema for the stored information.
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
   * Output only. The size of the artifact in bytes. If the artifact is gzipped,
   * this is the size of the uncompressed artifact.
   *
   * @var int
   */
  public $sizeBytes;
  /**
   * Output only. Last update timestamp.
   *
   * @var string
   */
  public $updateTime;

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
   * Input only. The contents of the artifact. Provided by API callers when
   * artifacts are created or replaced. To access the contents of an artifact,
   * use GetArtifactContents.
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
   * Output only. Creation timestamp.
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
   * Output only. A SHA-256 hash of the artifact's contents. If the artifact is
   * gzipped, this is the hash of the uncompressed artifact.
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
   * prefixed with "registry.googleapis.com/" and cannot be changed.
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
   * A content type specifier for the artifact. Content type specifiers are
   * Media Types (https://en.wikipedia.org/wiki/Media_type) with a possible
   * "schema" parameter that specifies a schema for the stored information.
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
   * Output only. The size of the artifact in bytes. If the artifact is gzipped,
   * this is the size of the uncompressed artifact.
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
   * Output only. Last update timestamp.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Artifact::class, 'Google_Service_ApigeeRegistry_Artifact');

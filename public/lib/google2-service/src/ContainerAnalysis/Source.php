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

namespace Google\Service\ContainerAnalysis;

class Source extends \Google\Collection
{
  protected $collection_key = 'additionalContexts';
  protected $additionalContextsType = SourceContext::class;
  protected $additionalContextsDataType = 'array';
  /**
   * If provided, the input binary artifacts for the build came from this
   * location.
   *
   * @var string
   */
  public $artifactStorageSourceUri;
  protected $contextType = SourceContext::class;
  protected $contextDataType = '';
  protected $fileHashesType = FileHashes::class;
  protected $fileHashesDataType = 'map';

  /**
   * If provided, some of the source code used for the build may be found in
   * these locations, in the case where the source repository had multiple
   * remotes or submodules. This list will not include the context specified in
   * the context field.
   *
   * @param SourceContext[] $additionalContexts
   */
  public function setAdditionalContexts($additionalContexts)
  {
    $this->additionalContexts = $additionalContexts;
  }
  /**
   * @return SourceContext[]
   */
  public function getAdditionalContexts()
  {
    return $this->additionalContexts;
  }
  /**
   * If provided, the input binary artifacts for the build came from this
   * location.
   *
   * @param string $artifactStorageSourceUri
   */
  public function setArtifactStorageSourceUri($artifactStorageSourceUri)
  {
    $this->artifactStorageSourceUri = $artifactStorageSourceUri;
  }
  /**
   * @return string
   */
  public function getArtifactStorageSourceUri()
  {
    return $this->artifactStorageSourceUri;
  }
  /**
   * If provided, the source code used for the build came from this location.
   *
   * @param SourceContext $context
   */
  public function setContext(SourceContext $context)
  {
    $this->context = $context;
  }
  /**
   * @return SourceContext
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * Hash(es) of the build source, which can be used to verify that the original
   * source integrity was maintained in the build. The keys to this map are file
   * paths used as build source and the values contain the hash values for those
   * files. If the build source came in a single package such as a gzipped
   * tarfile (.tar.gz), the FileHash will be for the single path to that file.
   *
   * @param FileHashes[] $fileHashes
   */
  public function setFileHashes($fileHashes)
  {
    $this->fileHashes = $fileHashes;
  }
  /**
   * @return FileHashes[]
   */
  public function getFileHashes()
  {
    return $this->fileHashes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Source::class, 'Google_Service_ContainerAnalysis_Source');

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

class GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadataReferenceDocumentMetadata extends \Google\Model
{
  /**
   * Document resource name.
   *
   * @var string
   */
  public $document;
  /**
   * Domain name from the document URI. Note that the `uri` field may contain a
   * URL that redirects to the actual website, in which case this will contain
   * the domain name of the target site.
   *
   * @var string
   */
  public $domain;
  /**
   * The mime type of the document. https://www.iana.org/assignments/media-
   * types/media-types.xhtml.
   *
   * @var string
   */
  public $mimeType;
  /**
   * Page identifier.
   *
   * @var string
   */
  public $pageIdentifier;
  /**
   * Title.
   *
   * @var string
   */
  public $title;
  /**
   * URI for the document. It may contain a URL that redirects to the actual
   * website.
   *
   * @var string
   */
  public $uri;

  /**
   * Document resource name.
   *
   * @param string $document
   */
  public function setDocument($document)
  {
    $this->document = $document;
  }
  /**
   * @return string
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * Domain name from the document URI. Note that the `uri` field may contain a
   * URL that redirects to the actual website, in which case this will contain
   * the domain name of the target site.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * The mime type of the document. https://www.iana.org/assignments/media-
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
   * Page identifier.
   *
   * @param string $pageIdentifier
   */
  public function setPageIdentifier($pageIdentifier)
  {
    $this->pageIdentifier = $pageIdentifier;
  }
  /**
   * @return string
   */
  public function getPageIdentifier()
  {
    return $this->pageIdentifier;
  }
  /**
   * Title.
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
   * URI for the document. It may contain a URL that redirects to the actual
   * website.
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
class_alias(GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadataReferenceDocumentMetadata::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AssistantGroundedContentTextGroundingMetadataReferenceDocumentMetadata');

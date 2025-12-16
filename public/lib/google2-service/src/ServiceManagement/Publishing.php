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

namespace Google\Service\ServiceManagement;

class Publishing extends \Google\Collection
{
  /**
   * Not useful.
   */
  public const ORGANIZATION_CLIENT_LIBRARY_ORGANIZATION_UNSPECIFIED = 'CLIENT_LIBRARY_ORGANIZATION_UNSPECIFIED';
  /**
   * Google Cloud Platform Org.
   */
  public const ORGANIZATION_CLOUD = 'CLOUD';
  /**
   * Ads (Advertising) Org.
   */
  public const ORGANIZATION_ADS = 'ADS';
  /**
   * Photos Org.
   */
  public const ORGANIZATION_PHOTOS = 'PHOTOS';
  /**
   * Street View Org.
   */
  public const ORGANIZATION_STREET_VIEW = 'STREET_VIEW';
  /**
   * Shopping Org.
   */
  public const ORGANIZATION_SHOPPING = 'SHOPPING';
  /**
   * Geo Org.
   */
  public const ORGANIZATION_GEO = 'GEO';
  /**
   * Generative AI - https://developers.generativeai.google
   */
  public const ORGANIZATION_GENERATIVE_AI = 'GENERATIVE_AI';
  protected $collection_key = 'methodSettings';
  /**
   * Used as a tracking tag when collecting data about the APIs developer
   * relations artifacts like docs, packages delivered to package managers, etc.
   * Example: "speech".
   *
   * @var string
   */
  public $apiShortName;
  /**
   * GitHub teams to be added to CODEOWNERS in the directory in GitHub
   * containing source code for the client libraries for this API.
   *
   * @var string[]
   */
  public $codeownerGithubTeams;
  /**
   * A prefix used in sample code when demarking regions to be included in
   * documentation.
   *
   * @var string
   */
  public $docTagPrefix;
  /**
   * Link to product home page. Example: https://cloud.google.com/asset-
   * inventory/docs/overview
   *
   * @var string
   */
  public $documentationUri;
  /**
   * GitHub label to apply to issues and pull requests opened for this API.
   *
   * @var string
   */
  public $githubLabel;
  protected $librarySettingsType = ClientLibrarySettings::class;
  protected $librarySettingsDataType = 'array';
  protected $methodSettingsType = MethodSettings::class;
  protected $methodSettingsDataType = 'array';
  /**
   * @var string
   */
  public $newIssueUri;
  /**
   * For whom the client library is being published.
   *
   * @var string
   */
  public $organization;
  /**
   * Optional link to proto reference documentation. Example:
   * https://cloud.google.com/pubsub/lite/docs/reference/rpc
   *
   * @var string
   */
  public $protoReferenceDocumentationUri;
  /**
   * Optional link to REST reference documentation. Example:
   * https://cloud.google.com/pubsub/lite/docs/reference/rest
   *
   * @var string
   */
  public $restReferenceDocumentationUri;

  /**
   * Used as a tracking tag when collecting data about the APIs developer
   * relations artifacts like docs, packages delivered to package managers, etc.
   * Example: "speech".
   *
   * @param string $apiShortName
   */
  public function setApiShortName($apiShortName)
  {
    $this->apiShortName = $apiShortName;
  }
  /**
   * @return string
   */
  public function getApiShortName()
  {
    return $this->apiShortName;
  }
  /**
   * GitHub teams to be added to CODEOWNERS in the directory in GitHub
   * containing source code for the client libraries for this API.
   *
   * @param string[] $codeownerGithubTeams
   */
  public function setCodeownerGithubTeams($codeownerGithubTeams)
  {
    $this->codeownerGithubTeams = $codeownerGithubTeams;
  }
  /**
   * @return string[]
   */
  public function getCodeownerGithubTeams()
  {
    return $this->codeownerGithubTeams;
  }
  /**
   * A prefix used in sample code when demarking regions to be included in
   * documentation.
   *
   * @param string $docTagPrefix
   */
  public function setDocTagPrefix($docTagPrefix)
  {
    $this->docTagPrefix = $docTagPrefix;
  }
  /**
   * @return string
   */
  public function getDocTagPrefix()
  {
    return $this->docTagPrefix;
  }
  /**
   * Link to product home page. Example: https://cloud.google.com/asset-
   * inventory/docs/overview
   *
   * @param string $documentationUri
   */
  public function setDocumentationUri($documentationUri)
  {
    $this->documentationUri = $documentationUri;
  }
  /**
   * @return string
   */
  public function getDocumentationUri()
  {
    return $this->documentationUri;
  }
  /**
   * GitHub label to apply to issues and pull requests opened for this API.
   *
   * @param string $githubLabel
   */
  public function setGithubLabel($githubLabel)
  {
    $this->githubLabel = $githubLabel;
  }
  /**
   * @return string
   */
  public function getGithubLabel()
  {
    return $this->githubLabel;
  }
  /**
   * Client library settings. If the same version string appears multiple times
   * in this list, then the last one wins. Settings from earlier settings with
   * the same version string are discarded.
   *
   * @param ClientLibrarySettings[] $librarySettings
   */
  public function setLibrarySettings($librarySettings)
  {
    $this->librarySettings = $librarySettings;
  }
  /**
   * @return ClientLibrarySettings[]
   */
  public function getLibrarySettings()
  {
    return $this->librarySettings;
  }
  /**
   * A list of API method settings, e.g. the behavior for methods that use the
   * long-running operation pattern.
   *
   * @param MethodSettings[] $methodSettings
   */
  public function setMethodSettings($methodSettings)
  {
    $this->methodSettings = $methodSettings;
  }
  /**
   * @return MethodSettings[]
   */
  public function getMethodSettings()
  {
    return $this->methodSettings;
  }
  /**
   * @param string $newIssueUri
   */
  public function setNewIssueUri($newIssueUri)
  {
    $this->newIssueUri = $newIssueUri;
  }
  /**
   * @return string
   */
  public function getNewIssueUri()
  {
    return $this->newIssueUri;
  }
  /**
   * For whom the client library is being published.
   *
   * Accepted values: CLIENT_LIBRARY_ORGANIZATION_UNSPECIFIED, CLOUD, ADS,
   * PHOTOS, STREET_VIEW, SHOPPING, GEO, GENERATIVE_AI
   *
   * @param self::ORGANIZATION_* $organization
   */
  public function setOrganization($organization)
  {
    $this->organization = $organization;
  }
  /**
   * @return self::ORGANIZATION_*
   */
  public function getOrganization()
  {
    return $this->organization;
  }
  /**
   * Optional link to proto reference documentation. Example:
   * https://cloud.google.com/pubsub/lite/docs/reference/rpc
   *
   * @param string $protoReferenceDocumentationUri
   */
  public function setProtoReferenceDocumentationUri($protoReferenceDocumentationUri)
  {
    $this->protoReferenceDocumentationUri = $protoReferenceDocumentationUri;
  }
  /**
   * @return string
   */
  public function getProtoReferenceDocumentationUri()
  {
    return $this->protoReferenceDocumentationUri;
  }
  /**
   * Optional link to REST reference documentation. Example:
   * https://cloud.google.com/pubsub/lite/docs/reference/rest
   *
   * @param string $restReferenceDocumentationUri
   */
  public function setRestReferenceDocumentationUri($restReferenceDocumentationUri)
  {
    $this->restReferenceDocumentationUri = $restReferenceDocumentationUri;
  }
  /**
   * @return string
   */
  public function getRestReferenceDocumentationUri()
  {
    return $this->restReferenceDocumentationUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Publishing::class, 'Google_Service_ServiceManagement_Publishing');

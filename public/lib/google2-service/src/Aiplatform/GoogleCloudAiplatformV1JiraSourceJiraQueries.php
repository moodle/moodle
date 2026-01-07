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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1JiraSourceJiraQueries extends \Google\Collection
{
  protected $collection_key = 'projects';
  protected $apiKeyConfigType = GoogleCloudAiplatformV1ApiAuthApiKeyConfig::class;
  protected $apiKeyConfigDataType = '';
  /**
   * A list of custom Jira queries to import. For information about JQL (Jira
   * Query Language), see https://support.atlassian.com/jira-service-management-
   * cloud/docs/use-advanced-search-with-jira-query-language-jql/
   *
   * @var string[]
   */
  public $customQueries;
  /**
   * Required. The Jira email address.
   *
   * @var string
   */
  public $email;
  /**
   * A list of Jira projects to import in their entirety.
   *
   * @var string[]
   */
  public $projects;
  /**
   * Required. The Jira server URI.
   *
   * @var string
   */
  public $serverUri;

  /**
   * Required. The SecretManager secret version resource name (e.g.
   * projects/{project}/secrets/{secret}/versions/{version}) storing the Jira
   * API key. See [Manage API tokens for your Atlassian
   * account](https://support.atlassian.com/atlassian-account/docs/manage-api-
   * tokens-for-your-atlassian-account/).
   *
   * @param GoogleCloudAiplatformV1ApiAuthApiKeyConfig $apiKeyConfig
   */
  public function setApiKeyConfig(GoogleCloudAiplatformV1ApiAuthApiKeyConfig $apiKeyConfig)
  {
    $this->apiKeyConfig = $apiKeyConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ApiAuthApiKeyConfig
   */
  public function getApiKeyConfig()
  {
    return $this->apiKeyConfig;
  }
  /**
   * A list of custom Jira queries to import. For information about JQL (Jira
   * Query Language), see https://support.atlassian.com/jira-service-management-
   * cloud/docs/use-advanced-search-with-jira-query-language-jql/
   *
   * @param string[] $customQueries
   */
  public function setCustomQueries($customQueries)
  {
    $this->customQueries = $customQueries;
  }
  /**
   * @return string[]
   */
  public function getCustomQueries()
  {
    return $this->customQueries;
  }
  /**
   * Required. The Jira email address.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * A list of Jira projects to import in their entirety.
   *
   * @param string[] $projects
   */
  public function setProjects($projects)
  {
    $this->projects = $projects;
  }
  /**
   * @return string[]
   */
  public function getProjects()
  {
    return $this->projects;
  }
  /**
   * Required. The Jira server URI.
   *
   * @param string $serverUri
   */
  public function setServerUri($serverUri)
  {
    $this->serverUri = $serverUri;
  }
  /**
   * @return string
   */
  public function getServerUri()
  {
    return $this->serverUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1JiraSourceJiraQueries::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1JiraSourceJiraQueries');

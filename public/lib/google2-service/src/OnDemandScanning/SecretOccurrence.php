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

namespace Google\Service\OnDemandScanning;

class SecretOccurrence extends \Google\Collection
{
  /**
   * Unspecified
   */
  public const KIND_SECRET_KIND_UNSPECIFIED = 'SECRET_KIND_UNSPECIFIED';
  /**
   * The secret kind is unknown.
   */
  public const KIND_SECRET_KIND_UNKNOWN = 'SECRET_KIND_UNKNOWN';
  /**
   * A Google Cloud service account key per:
   * https://cloud.google.com/iam/docs/creating-managing-service-account-keys
   */
  public const KIND_SECRET_KIND_GCP_SERVICE_ACCOUNT_KEY = 'SECRET_KIND_GCP_SERVICE_ACCOUNT_KEY';
  /**
   * A Google Cloud API key per:
   * https://cloud.google.com/docs/authentication/api-keys
   */
  public const KIND_SECRET_KIND_GCP_API_KEY = 'SECRET_KIND_GCP_API_KEY';
  /**
   * A Google Cloud OAuth2 client credentials per:
   * https://developers.google.com/identity/protocols/oauth2
   */
  public const KIND_SECRET_KIND_GCP_OAUTH2_CLIENT_CREDENTIALS = 'SECRET_KIND_GCP_OAUTH2_CLIENT_CREDENTIALS';
  /**
   * A Google Cloud OAuth2 access token per:
   * https://cloud.google.com/docs/authentication/token-types#access
   */
  public const KIND_SECRET_KIND_GCP_OAUTH2_ACCESS_TOKEN = 'SECRET_KIND_GCP_OAUTH2_ACCESS_TOKEN';
  /**
   * An Anthropic Admin API key.
   */
  public const KIND_SECRET_KIND_ANTHROPIC_ADMIN_API_KEY = 'SECRET_KIND_ANTHROPIC_ADMIN_API_KEY';
  /**
   * An Anthropic API key.
   */
  public const KIND_SECRET_KIND_ANTHROPIC_API_KEY = 'SECRET_KIND_ANTHROPIC_API_KEY';
  /**
   * An Azure access token.
   */
  public const KIND_SECRET_KIND_AZURE_ACCESS_TOKEN = 'SECRET_KIND_AZURE_ACCESS_TOKEN';
  /**
   * An Azure Identity Platform ID token.
   */
  public const KIND_SECRET_KIND_AZURE_IDENTITY_TOKEN = 'SECRET_KIND_AZURE_IDENTITY_TOKEN';
  /**
   * A Docker Hub personal access token.
   */
  public const KIND_SECRET_KIND_DOCKER_HUB_PERSONAL_ACCESS_TOKEN = 'SECRET_KIND_DOCKER_HUB_PERSONAL_ACCESS_TOKEN';
  /**
   * A GitHub App refresh token.
   */
  public const KIND_SECRET_KIND_GITHUB_APP_REFRESH_TOKEN = 'SECRET_KIND_GITHUB_APP_REFRESH_TOKEN';
  /**
   * A GitHub App server-to-server token.
   */
  public const KIND_SECRET_KIND_GITHUB_APP_SERVER_TO_SERVER_TOKEN = 'SECRET_KIND_GITHUB_APP_SERVER_TO_SERVER_TOKEN';
  /**
   * A GitHub App user-to-server token.
   */
  public const KIND_SECRET_KIND_GITHUB_APP_USER_TO_SERVER_TOKEN = 'SECRET_KIND_GITHUB_APP_USER_TO_SERVER_TOKEN';
  /**
   * A GitHub personal access token (classic).
   */
  public const KIND_SECRET_KIND_GITHUB_CLASSIC_PERSONAL_ACCESS_TOKEN = 'SECRET_KIND_GITHUB_CLASSIC_PERSONAL_ACCESS_TOKEN';
  /**
   * A GitHub fine-grained personal access token.
   */
  public const KIND_SECRET_KIND_GITHUB_FINE_GRAINED_PERSONAL_ACCESS_TOKEN = 'SECRET_KIND_GITHUB_FINE_GRAINED_PERSONAL_ACCESS_TOKEN';
  /**
   * A GitHub OAuth token.
   */
  public const KIND_SECRET_KIND_GITHUB_OAUTH_TOKEN = 'SECRET_KIND_GITHUB_OAUTH_TOKEN';
  /**
   * A Hugging Face API key.
   */
  public const KIND_SECRET_KIND_HUGGINGFACE_API_KEY = 'SECRET_KIND_HUGGINGFACE_API_KEY';
  /**
   * An OpenAI API key.
   */
  public const KIND_SECRET_KIND_OPENAI_API_KEY = 'SECRET_KIND_OPENAI_API_KEY';
  /**
   * A Perplexity API key.
   */
  public const KIND_SECRET_KIND_PERPLEXITY_API_KEY = 'SECRET_KIND_PERPLEXITY_API_KEY';
  /**
   * A Stripe secret key.
   */
  public const KIND_SECRET_KIND_STRIPE_SECRET_KEY = 'SECRET_KIND_STRIPE_SECRET_KEY';
  /**
   * A Stripe restricted key.
   */
  public const KIND_SECRET_KIND_STRIPE_RESTRICTED_KEY = 'SECRET_KIND_STRIPE_RESTRICTED_KEY';
  /**
   * A Stripe webhook secret.
   */
  public const KIND_SECRET_KIND_STRIPE_WEBHOOK_SECRET = 'SECRET_KIND_STRIPE_WEBHOOK_SECRET';
  protected $collection_key = 'statuses';
  /**
   * Required. Type of secret.
   *
   * @var string
   */
  public $kind;
  protected $locationsType = SecretLocation::class;
  protected $locationsDataType = 'array';
  protected $statusesType = SecretStatus::class;
  protected $statusesDataType = 'array';

  /**
   * Required. Type of secret.
   *
   * Accepted values: SECRET_KIND_UNSPECIFIED, SECRET_KIND_UNKNOWN,
   * SECRET_KIND_GCP_SERVICE_ACCOUNT_KEY, SECRET_KIND_GCP_API_KEY,
   * SECRET_KIND_GCP_OAUTH2_CLIENT_CREDENTIALS,
   * SECRET_KIND_GCP_OAUTH2_ACCESS_TOKEN, SECRET_KIND_ANTHROPIC_ADMIN_API_KEY,
   * SECRET_KIND_ANTHROPIC_API_KEY, SECRET_KIND_AZURE_ACCESS_TOKEN,
   * SECRET_KIND_AZURE_IDENTITY_TOKEN,
   * SECRET_KIND_DOCKER_HUB_PERSONAL_ACCESS_TOKEN,
   * SECRET_KIND_GITHUB_APP_REFRESH_TOKEN,
   * SECRET_KIND_GITHUB_APP_SERVER_TO_SERVER_TOKEN,
   * SECRET_KIND_GITHUB_APP_USER_TO_SERVER_TOKEN,
   * SECRET_KIND_GITHUB_CLASSIC_PERSONAL_ACCESS_TOKEN,
   * SECRET_KIND_GITHUB_FINE_GRAINED_PERSONAL_ACCESS_TOKEN,
   * SECRET_KIND_GITHUB_OAUTH_TOKEN, SECRET_KIND_HUGGINGFACE_API_KEY,
   * SECRET_KIND_OPENAI_API_KEY, SECRET_KIND_PERPLEXITY_API_KEY,
   * SECRET_KIND_STRIPE_SECRET_KEY, SECRET_KIND_STRIPE_RESTRICTED_KEY,
   * SECRET_KIND_STRIPE_WEBHOOK_SECRET
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Optional. Locations where the secret is detected.
   *
   * @param SecretLocation[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return SecretLocation[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * Optional. Status of the secret.
   *
   * @param SecretStatus[] $statuses
   */
  public function setStatuses($statuses)
  {
    $this->statuses = $statuses;
  }
  /**
   * @return SecretStatus[]
   */
  public function getStatuses()
  {
    return $this->statuses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecretOccurrence::class, 'Google_Service_OnDemandScanning_SecretOccurrence');

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

namespace Google\Service\IAMCredentials;

class SignJwtRequest extends \Google\Collection
{
  protected $collection_key = 'delegates';
  /**
   * The sequence of service accounts in a delegation chain. Each service
   * account must be granted the `roles/iam.serviceAccountTokenCreator` role on
   * its next service account in the chain. The last service account in the
   * chain must be granted the `roles/iam.serviceAccountTokenCreator` role on
   * the service account that is specified in the `name` field of the request.
   * The delegates must have the following format:
   * `projects/-/serviceAccounts/{ACCOUNT_EMAIL_OR_UNIQUEID}`. The `-` wildcard
   * character is required; replacing it with a project ID is invalid.
   *
   * @var string[]
   */
  public $delegates;
  /**
   * Required. The JWT payload to sign. Must be a serialized JSON object that
   * contains a JWT Claims Set. For example: `{"sub": "user@example.com", "iat":
   * 313435}` If the JWT Claims Set contains an expiration time (`exp`) claim,
   * it must be an integer timestamp that is not in the past and no more than 12
   * hours in the future.
   *
   * @var string
   */
  public $payload;

  /**
   * The sequence of service accounts in a delegation chain. Each service
   * account must be granted the `roles/iam.serviceAccountTokenCreator` role on
   * its next service account in the chain. The last service account in the
   * chain must be granted the `roles/iam.serviceAccountTokenCreator` role on
   * the service account that is specified in the `name` field of the request.
   * The delegates must have the following format:
   * `projects/-/serviceAccounts/{ACCOUNT_EMAIL_OR_UNIQUEID}`. The `-` wildcard
   * character is required; replacing it with a project ID is invalid.
   *
   * @param string[] $delegates
   */
  public function setDelegates($delegates)
  {
    $this->delegates = $delegates;
  }
  /**
   * @return string[]
   */
  public function getDelegates()
  {
    return $this->delegates;
  }
  /**
   * Required. The JWT payload to sign. Must be a serialized JSON object that
   * contains a JWT Claims Set. For example: `{"sub": "user@example.com", "iat":
   * 313435}` If the JWT Claims Set contains an expiration time (`exp`) claim,
   * it must be an integer timestamp that is not in the past and no more than 12
   * hours in the future.
   *
   * @param string $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return string
   */
  public function getPayload()
  {
    return $this->payload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SignJwtRequest::class, 'Google_Service_IAMCredentials_SignJwtRequest');

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

namespace Google\Service\Pubsub;

class PushConfig extends \Google\Model
{
  /**
   * Optional. Endpoint configuration attributes that can be used to control
   * different aspects of the message delivery. The only currently supported
   * attribute is `x-goog-version`, which you can use to change the format of
   * the pushed message. This attribute indicates the version of the data
   * expected by the endpoint. This controls the shape of the pushed message
   * (i.e., its fields and metadata). If not present during the
   * `CreateSubscription` call, it will default to the version of the Pub/Sub
   * API used to make such call. If not present in a `ModifyPushConfig` call,
   * its value will not be changed. `GetSubscription` calls will always return a
   * valid version, even if the subscription was created without this attribute.
   * The only supported values for the `x-goog-version` attribute are: *
   * `v1beta1`: uses the push format defined in the v1beta1 Pub/Sub API. * `v1`
   * or `v1beta2`: uses the push format defined in the v1 Pub/Sub API. For
   * example: `attributes { "x-goog-version": "v1" }`
   *
   * @var string[]
   */
  public $attributes;
  protected $noWrapperType = NoWrapper::class;
  protected $noWrapperDataType = '';
  protected $oidcTokenType = OidcToken::class;
  protected $oidcTokenDataType = '';
  protected $pubsubWrapperType = PubsubWrapper::class;
  protected $pubsubWrapperDataType = '';
  /**
   * Optional. A URL locating the endpoint to which messages should be pushed.
   * For example, a Webhook endpoint might use `https://example.com/push`.
   *
   * @var string
   */
  public $pushEndpoint;

  /**
   * Optional. Endpoint configuration attributes that can be used to control
   * different aspects of the message delivery. The only currently supported
   * attribute is `x-goog-version`, which you can use to change the format of
   * the pushed message. This attribute indicates the version of the data
   * expected by the endpoint. This controls the shape of the pushed message
   * (i.e., its fields and metadata). If not present during the
   * `CreateSubscription` call, it will default to the version of the Pub/Sub
   * API used to make such call. If not present in a `ModifyPushConfig` call,
   * its value will not be changed. `GetSubscription` calls will always return a
   * valid version, even if the subscription was created without this attribute.
   * The only supported values for the `x-goog-version` attribute are: *
   * `v1beta1`: uses the push format defined in the v1beta1 Pub/Sub API. * `v1`
   * or `v1beta2`: uses the push format defined in the v1 Pub/Sub API. For
   * example: `attributes { "x-goog-version": "v1" }`
   *
   * @param string[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Optional. When set, the payload to the push endpoint is not wrapped.
   *
   * @param NoWrapper $noWrapper
   */
  public function setNoWrapper(NoWrapper $noWrapper)
  {
    $this->noWrapper = $noWrapper;
  }
  /**
   * @return NoWrapper
   */
  public function getNoWrapper()
  {
    return $this->noWrapper;
  }
  /**
   * Optional. If specified, Pub/Sub will generate and attach an OIDC JWT token
   * as an `Authorization` header in the HTTP request for every pushed message.
   *
   * @param OidcToken $oidcToken
   */
  public function setOidcToken(OidcToken $oidcToken)
  {
    $this->oidcToken = $oidcToken;
  }
  /**
   * @return OidcToken
   */
  public function getOidcToken()
  {
    return $this->oidcToken;
  }
  /**
   * Optional. When set, the payload to the push endpoint is in the form of the
   * JSON representation of a PubsubMessage (https://cloud.google.com/pubsub/doc
   * s/reference/rpc/google.pubsub.v1#pubsubmessage).
   *
   * @param PubsubWrapper $pubsubWrapper
   */
  public function setPubsubWrapper(PubsubWrapper $pubsubWrapper)
  {
    $this->pubsubWrapper = $pubsubWrapper;
  }
  /**
   * @return PubsubWrapper
   */
  public function getPubsubWrapper()
  {
    return $this->pubsubWrapper;
  }
  /**
   * Optional. A URL locating the endpoint to which messages should be pushed.
   * For example, a Webhook endpoint might use `https://example.com/push`.
   *
   * @param string $pushEndpoint
   */
  public function setPushEndpoint($pushEndpoint)
  {
    $this->pushEndpoint = $pushEndpoint;
  }
  /**
   * @return string
   */
  public function getPushEndpoint()
  {
    return $this->pushEndpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PushConfig::class, 'Google_Service_Pubsub_PushConfig');

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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1ChangeOfferRequest extends \Google\Collection
{
  protected $collection_key = 'parameters';
  /**
   * Optional. The billing account resource name that is used to pay for this
   * entitlement when setting up billing on a trial subscription. This field is
   * only relevant for multi-currency accounts. It should be left empty for
   * single currency accounts.
   *
   * @var string
   */
  public $billingAccount;
  /**
   * Required. New Offer. Format: accounts/{account_id}/offers/{offer_id}.
   *
   * @var string
   */
  public $offer;
  protected $parametersType = GoogleCloudChannelV1Parameter::class;
  protected $parametersDataType = 'array';
  /**
   * Optional. Price reference ID for the offer. Only for offers that require
   * additional price information. Used to guarantee that the pricing is
   * consistent between quoting the offer and placing the order.
   *
   * @var string
   */
  public $priceReferenceId;
  /**
   * Optional. Purchase order id provided by the reseller.
   *
   * @var string
   */
  public $purchaseOrderId;
  /**
   * Optional. You can specify an optional unique request ID, and if you need to
   * retry your request, the server will know to ignore the request if it's
   * complete. For example, you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if it received the original operation with the same request ID. If it
   * did, it will ignore the second request. The request ID must be a valid
   * [UUID](https://tools.ietf.org/html/rfc4122) with the exception that zero
   * UUID is not supported (`00000000-0000-0000-0000-000000000000`).
   *
   * @var string
   */
  public $requestId;

  /**
   * Optional. The billing account resource name that is used to pay for this
   * entitlement when setting up billing on a trial subscription. This field is
   * only relevant for multi-currency accounts. It should be left empty for
   * single currency accounts.
   *
   * @param string $billingAccount
   */
  public function setBillingAccount($billingAccount)
  {
    $this->billingAccount = $billingAccount;
  }
  /**
   * @return string
   */
  public function getBillingAccount()
  {
    return $this->billingAccount;
  }
  /**
   * Required. New Offer. Format: accounts/{account_id}/offers/{offer_id}.
   *
   * @param string $offer
   */
  public function setOffer($offer)
  {
    $this->offer = $offer;
  }
  /**
   * @return string
   */
  public function getOffer()
  {
    return $this->offer;
  }
  /**
   * Optional. Parameters needed to purchase the Offer. To view the available
   * Parameters refer to the Offer.parameter_definitions from the desired offer.
   *
   * @param GoogleCloudChannelV1Parameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudChannelV1Parameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. Price reference ID for the offer. Only for offers that require
   * additional price information. Used to guarantee that the pricing is
   * consistent between quoting the offer and placing the order.
   *
   * @param string $priceReferenceId
   */
  public function setPriceReferenceId($priceReferenceId)
  {
    $this->priceReferenceId = $priceReferenceId;
  }
  /**
   * @return string
   */
  public function getPriceReferenceId()
  {
    return $this->priceReferenceId;
  }
  /**
   * Optional. Purchase order id provided by the reseller.
   *
   * @param string $purchaseOrderId
   */
  public function setPurchaseOrderId($purchaseOrderId)
  {
    $this->purchaseOrderId = $purchaseOrderId;
  }
  /**
   * @return string
   */
  public function getPurchaseOrderId()
  {
    return $this->purchaseOrderId;
  }
  /**
   * Optional. You can specify an optional unique request ID, and if you need to
   * retry your request, the server will know to ignore the request if it's
   * complete. For example, you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if it received the original operation with the same request ID. If it
   * did, it will ignore the second request. The request ID must be a valid
   * [UUID](https://tools.ietf.org/html/rfc4122) with the exception that zero
   * UUID is not supported (`00000000-0000-0000-0000-000000000000`).
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1ChangeOfferRequest::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1ChangeOfferRequest');

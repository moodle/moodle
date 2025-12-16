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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1ProductEventDetail extends \Google\Collection
{
  protected $collection_key = 'productDetails';
  /**
   * Optional. The id or name of the associated shopping cart. This id is used
   * to associate multiple items added or present in the cart before purchase.
   * This can only be set for `add-to-cart`, `remove-from-cart`, `checkout-
   * start`, `purchase-complete`, or `shopping-cart-page-view` events.
   *
   * @var string
   */
  public $cartId;
  /**
   * Required for `add-to-list` and `remove-from-list` events. The id or name of
   * the list that the item is being added to or removed from. Other event types
   * should not set this field.
   *
   * @var string
   */
  public $listId;
  protected $pageCategoriesType = GoogleCloudRecommendationengineV1beta1CatalogItemCategoryHierarchy::class;
  protected $pageCategoriesDataType = 'array';
  protected $productDetailsType = GoogleCloudRecommendationengineV1beta1ProductDetail::class;
  protected $productDetailsDataType = 'array';
  protected $purchaseTransactionType = GoogleCloudRecommendationengineV1beta1PurchaseTransaction::class;
  protected $purchaseTransactionDataType = '';
  /**
   * At least one of search_query or page_categories is required for `search`
   * events. Other event types should not set this field. The user's search
   * query as UTF-8 encoded text with a length limit of 5 KiB.
   *
   * @var string
   */
  public $searchQuery;

  /**
   * Optional. The id or name of the associated shopping cart. This id is used
   * to associate multiple items added or present in the cart before purchase.
   * This can only be set for `add-to-cart`, `remove-from-cart`, `checkout-
   * start`, `purchase-complete`, or `shopping-cart-page-view` events.
   *
   * @param string $cartId
   */
  public function setCartId($cartId)
  {
    $this->cartId = $cartId;
  }
  /**
   * @return string
   */
  public function getCartId()
  {
    return $this->cartId;
  }
  /**
   * Required for `add-to-list` and `remove-from-list` events. The id or name of
   * the list that the item is being added to or removed from. Other event types
   * should not set this field.
   *
   * @param string $listId
   */
  public function setListId($listId)
  {
    $this->listId = $listId;
  }
  /**
   * @return string
   */
  public function getListId()
  {
    return $this->listId;
  }
  /**
   * Required for `category-page-view` events. At least one of search_query or
   * page_categories is required for `search` events. Other event types should
   * not set this field. The categories associated with a category page.
   * Category pages include special pages such as sales or promotions. For
   * instance, a special sale page may have the category hierarchy: categories :
   * ["Sales", "2017 Black Friday Deals"].
   *
   * @param GoogleCloudRecommendationengineV1beta1CatalogItemCategoryHierarchy[] $pageCategories
   */
  public function setPageCategories($pageCategories)
  {
    $this->pageCategories = $pageCategories;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1CatalogItemCategoryHierarchy[]
   */
  public function getPageCategories()
  {
    return $this->pageCategories;
  }
  /**
   * The main product details related to the event. This field is required for
   * the following event types: * `add-to-cart` * `add-to-list` * `checkout-
   * start` * `detail-page-view` * `purchase-complete` * `refund` * `remove-
   * from-cart` * `remove-from-list` This field is optional for the following
   * event types: * `page-visit` * `shopping-cart-page-view` - note that
   * 'product_details' should be set for this unless the shopping cart is empty.
   * * `search` (highly encouraged) In a `search` event, this field represents
   * the products returned to the end user on the current page (the end user may
   * have not finished broswing the whole page yet). When a new page is returned
   * to the end user, after pagination/filtering/ordering even for the same
   * query, a new SEARCH event with different product_details is desired. The
   * end user may have not finished broswing the whole page yet. This field is
   * not allowed for the following event types: * `category-page-view` * `home-
   * page-view`
   *
   * @param GoogleCloudRecommendationengineV1beta1ProductDetail[] $productDetails
   */
  public function setProductDetails($productDetails)
  {
    $this->productDetails = $productDetails;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1ProductDetail[]
   */
  public function getProductDetails()
  {
    return $this->productDetails;
  }
  /**
   * Optional. A transaction represents the entire purchase transaction.
   * Required for `purchase-complete` events. Optional for `checkout-start`
   * events. Other event types should not set this field.
   *
   * @param GoogleCloudRecommendationengineV1beta1PurchaseTransaction $purchaseTransaction
   */
  public function setPurchaseTransaction(GoogleCloudRecommendationengineV1beta1PurchaseTransaction $purchaseTransaction)
  {
    $this->purchaseTransaction = $purchaseTransaction;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1PurchaseTransaction
   */
  public function getPurchaseTransaction()
  {
    return $this->purchaseTransaction;
  }
  /**
   * At least one of search_query or page_categories is required for `search`
   * events. Other event types should not set this field. The user's search
   * query as UTF-8 encoded text with a length limit of 5 KiB.
   *
   * @param string $searchQuery
   */
  public function setSearchQuery($searchQuery)
  {
    $this->searchQuery = $searchQuery;
  }
  /**
   * @return string
   */
  public function getSearchQuery()
  {
    return $this->searchQuery;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1ProductEventDetail::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1ProductEventDetail');

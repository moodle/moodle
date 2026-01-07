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

namespace Google\Service\Merchant;

class ReportRow extends \Google\Model
{
  protected $bestSellersBrandViewType = BestSellersBrandView::class;
  protected $bestSellersBrandViewDataType = '';
  protected $bestSellersProductClusterViewType = BestSellersProductClusterView::class;
  protected $bestSellersProductClusterViewDataType = '';
  protected $competitiveVisibilityBenchmarkViewType = CompetitiveVisibilityBenchmarkView::class;
  protected $competitiveVisibilityBenchmarkViewDataType = '';
  protected $competitiveVisibilityCompetitorViewType = CompetitiveVisibilityCompetitorView::class;
  protected $competitiveVisibilityCompetitorViewDataType = '';
  protected $competitiveVisibilityTopMerchantViewType = CompetitiveVisibilityTopMerchantView::class;
  protected $competitiveVisibilityTopMerchantViewDataType = '';
  protected $nonProductPerformanceViewType = NonProductPerformanceView::class;
  protected $nonProductPerformanceViewDataType = '';
  protected $priceCompetitivenessProductViewType = PriceCompetitivenessProductView::class;
  protected $priceCompetitivenessProductViewDataType = '';
  protected $priceInsightsProductViewType = PriceInsightsProductView::class;
  protected $priceInsightsProductViewDataType = '';
  protected $productPerformanceViewType = ProductPerformanceView::class;
  protected $productPerformanceViewDataType = '';
  protected $productViewType = ProductView::class;
  protected $productViewDataType = '';

  /**
   * @param BestSellersBrandView
   */
  public function setBestSellersBrandView(BestSellersBrandView $bestSellersBrandView)
  {
    $this->bestSellersBrandView = $bestSellersBrandView;
  }
  /**
   * @return BestSellersBrandView
   */
  public function getBestSellersBrandView()
  {
    return $this->bestSellersBrandView;
  }
  /**
   * @param BestSellersProductClusterView
   */
  public function setBestSellersProductClusterView(BestSellersProductClusterView $bestSellersProductClusterView)
  {
    $this->bestSellersProductClusterView = $bestSellersProductClusterView;
  }
  /**
   * @return BestSellersProductClusterView
   */
  public function getBestSellersProductClusterView()
  {
    return $this->bestSellersProductClusterView;
  }
  /**
   * @param CompetitiveVisibilityBenchmarkView
   */
  public function setCompetitiveVisibilityBenchmarkView(CompetitiveVisibilityBenchmarkView $competitiveVisibilityBenchmarkView)
  {
    $this->competitiveVisibilityBenchmarkView = $competitiveVisibilityBenchmarkView;
  }
  /**
   * @return CompetitiveVisibilityBenchmarkView
   */
  public function getCompetitiveVisibilityBenchmarkView()
  {
    return $this->competitiveVisibilityBenchmarkView;
  }
  /**
   * @param CompetitiveVisibilityCompetitorView
   */
  public function setCompetitiveVisibilityCompetitorView(CompetitiveVisibilityCompetitorView $competitiveVisibilityCompetitorView)
  {
    $this->competitiveVisibilityCompetitorView = $competitiveVisibilityCompetitorView;
  }
  /**
   * @return CompetitiveVisibilityCompetitorView
   */
  public function getCompetitiveVisibilityCompetitorView()
  {
    return $this->competitiveVisibilityCompetitorView;
  }
  /**
   * @param CompetitiveVisibilityTopMerchantView
   */
  public function setCompetitiveVisibilityTopMerchantView(CompetitiveVisibilityTopMerchantView $competitiveVisibilityTopMerchantView)
  {
    $this->competitiveVisibilityTopMerchantView = $competitiveVisibilityTopMerchantView;
  }
  /**
   * @return CompetitiveVisibilityTopMerchantView
   */
  public function getCompetitiveVisibilityTopMerchantView()
  {
    return $this->competitiveVisibilityTopMerchantView;
  }
  /**
   * @param NonProductPerformanceView
   */
  public function setNonProductPerformanceView(NonProductPerformanceView $nonProductPerformanceView)
  {
    $this->nonProductPerformanceView = $nonProductPerformanceView;
  }
  /**
   * @return NonProductPerformanceView
   */
  public function getNonProductPerformanceView()
  {
    return $this->nonProductPerformanceView;
  }
  /**
   * @param PriceCompetitivenessProductView
   */
  public function setPriceCompetitivenessProductView(PriceCompetitivenessProductView $priceCompetitivenessProductView)
  {
    $this->priceCompetitivenessProductView = $priceCompetitivenessProductView;
  }
  /**
   * @return PriceCompetitivenessProductView
   */
  public function getPriceCompetitivenessProductView()
  {
    return $this->priceCompetitivenessProductView;
  }
  /**
   * @param PriceInsightsProductView
   */
  public function setPriceInsightsProductView(PriceInsightsProductView $priceInsightsProductView)
  {
    $this->priceInsightsProductView = $priceInsightsProductView;
  }
  /**
   * @return PriceInsightsProductView
   */
  public function getPriceInsightsProductView()
  {
    return $this->priceInsightsProductView;
  }
  /**
   * @param ProductPerformanceView
   */
  public function setProductPerformanceView(ProductPerformanceView $productPerformanceView)
  {
    $this->productPerformanceView = $productPerformanceView;
  }
  /**
   * @return ProductPerformanceView
   */
  public function getProductPerformanceView()
  {
    return $this->productPerformanceView;
  }
  /**
   * @param ProductView
   */
  public function setProductView(ProductView $productView)
  {
    $this->productView = $productView;
  }
  /**
   * @return ProductView
   */
  public function getProductView()
  {
    return $this->productView;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportRow::class, 'Google_Service_Merchant_ReportRow');

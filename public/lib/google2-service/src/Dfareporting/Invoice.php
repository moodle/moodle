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

namespace Google\Service\Dfareporting;

class Invoice extends \Google\Collection
{
  public const INVOICE_TYPE_INVOICE_TYPE_UNSPECIFIED = 'INVOICE_TYPE_UNSPECIFIED';
  public const INVOICE_TYPE_INVOICE_TYPE_CREDIT = 'INVOICE_TYPE_CREDIT';
  public const INVOICE_TYPE_INVOICE_TYPE_INVOICE = 'INVOICE_TYPE_INVOICE';
  protected $collection_key = 'replacedInvoiceIds';
  protected $internal_gapi_mappings = [
        "campaignSummaries" => "campaign_summaries",
  ];
  protected $campaignSummariesType = CampaignSummary::class;
  protected $campaignSummariesDataType = 'array';
  /**
   * The originally issued invoice that is being adjusted by this invoice, if
   * applicable. May appear on invoice PDF as *Reference invoice number*.
   *
   * @var string
   */
  public $correctedInvoiceId;
  /**
   * Invoice currency code in ISO 4217 format.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * The invoice due date.
   *
   * @var string
   */
  public $dueDate;
  /**
   * ID of this invoice.
   *
   * @var string
   */
  public $id;
  /**
   * The type of invoice document.
   *
   * @var string
   */
  public $invoiceType;
  /**
   * The date when the invoice was issued.
   *
   * @var string
   */
  public $issueDate;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#invoice".
   *
   * @var string
   */
  public $kind;
  /**
   * The ID of the payments account the invoice belongs to. Appears on the
   * invoice PDF as *Billing Account Number*.
   *
   * @var string
   */
  public $paymentsAccountId;
  /**
   * The ID of the payments profile the invoice belongs to. Appears on the
   * invoice PDF as *Billing ID*.
   *
   * @var string
   */
  public $paymentsProfileId;
  /**
   * The URL to download a PDF copy of the invoice. Note that this URL is user
   * specific and requires a valid OAuth 2.0 access token to access. The access
   * token must be provided in an *Authorization: Bearer* HTTP header. The URL
   * will only be usable for 7 days from when the api is called.
   *
   * @var string
   */
  public $pdfUrl;
  /**
   * Purchase order number associated with the invoice.
   *
   * @var string
   */
  public $purchaseOrderNumber;
  /**
   * The originally issued invoice(s) that is being cancelled by this invoice,
   * if applicable. May appear on invoice PDF as *Replaced invoice numbers*.
   * Note: There may be multiple replaced invoices due to consolidation of
   * multiple invoices into a single invoice.
   *
   * @var string[]
   */
  public $replacedInvoiceIds;
  /**
   * The invoice service end date.
   *
   * @var string
   */
  public $serviceEndDate;
  /**
   * The invoice service start date.
   *
   * @var string
   */
  public $serviceStartDate;
  /**
   * The pre-tax subtotal amount, in micros of the invoice's currency.
   *
   * @var string
   */
  public $subtotalAmountMicros;
  /**
   * The invoice total amount, in micros of the invoice's currency.
   *
   * @var string
   */
  public $totalAmountMicros;
  /**
   * The sum of all taxes in invoice, in micros of the invoice's currency.
   *
   * @var string
   */
  public $totalTaxAmountMicros;

  /**
   * The list of summarized campaign information associated with this invoice.
   *
   * @param CampaignSummary[] $campaignSummaries
   */
  public function setCampaignSummaries($campaignSummaries)
  {
    $this->campaignSummaries = $campaignSummaries;
  }
  /**
   * @return CampaignSummary[]
   */
  public function getCampaignSummaries()
  {
    return $this->campaignSummaries;
  }
  /**
   * The originally issued invoice that is being adjusted by this invoice, if
   * applicable. May appear on invoice PDF as *Reference invoice number*.
   *
   * @param string $correctedInvoiceId
   */
  public function setCorrectedInvoiceId($correctedInvoiceId)
  {
    $this->correctedInvoiceId = $correctedInvoiceId;
  }
  /**
   * @return string
   */
  public function getCorrectedInvoiceId()
  {
    return $this->correctedInvoiceId;
  }
  /**
   * Invoice currency code in ISO 4217 format.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * The invoice due date.
   *
   * @param string $dueDate
   */
  public function setDueDate($dueDate)
  {
    $this->dueDate = $dueDate;
  }
  /**
   * @return string
   */
  public function getDueDate()
  {
    return $this->dueDate;
  }
  /**
   * ID of this invoice.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The type of invoice document.
   *
   * Accepted values: INVOICE_TYPE_UNSPECIFIED, INVOICE_TYPE_CREDIT,
   * INVOICE_TYPE_INVOICE
   *
   * @param self::INVOICE_TYPE_* $invoiceType
   */
  public function setInvoiceType($invoiceType)
  {
    $this->invoiceType = $invoiceType;
  }
  /**
   * @return self::INVOICE_TYPE_*
   */
  public function getInvoiceType()
  {
    return $this->invoiceType;
  }
  /**
   * The date when the invoice was issued.
   *
   * @param string $issueDate
   */
  public function setIssueDate($issueDate)
  {
    $this->issueDate = $issueDate;
  }
  /**
   * @return string
   */
  public function getIssueDate()
  {
    return $this->issueDate;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#invoice".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The ID of the payments account the invoice belongs to. Appears on the
   * invoice PDF as *Billing Account Number*.
   *
   * @param string $paymentsAccountId
   */
  public function setPaymentsAccountId($paymentsAccountId)
  {
    $this->paymentsAccountId = $paymentsAccountId;
  }
  /**
   * @return string
   */
  public function getPaymentsAccountId()
  {
    return $this->paymentsAccountId;
  }
  /**
   * The ID of the payments profile the invoice belongs to. Appears on the
   * invoice PDF as *Billing ID*.
   *
   * @param string $paymentsProfileId
   */
  public function setPaymentsProfileId($paymentsProfileId)
  {
    $this->paymentsProfileId = $paymentsProfileId;
  }
  /**
   * @return string
   */
  public function getPaymentsProfileId()
  {
    return $this->paymentsProfileId;
  }
  /**
   * The URL to download a PDF copy of the invoice. Note that this URL is user
   * specific and requires a valid OAuth 2.0 access token to access. The access
   * token must be provided in an *Authorization: Bearer* HTTP header. The URL
   * will only be usable for 7 days from when the api is called.
   *
   * @param string $pdfUrl
   */
  public function setPdfUrl($pdfUrl)
  {
    $this->pdfUrl = $pdfUrl;
  }
  /**
   * @return string
   */
  public function getPdfUrl()
  {
    return $this->pdfUrl;
  }
  /**
   * Purchase order number associated with the invoice.
   *
   * @param string $purchaseOrderNumber
   */
  public function setPurchaseOrderNumber($purchaseOrderNumber)
  {
    $this->purchaseOrderNumber = $purchaseOrderNumber;
  }
  /**
   * @return string
   */
  public function getPurchaseOrderNumber()
  {
    return $this->purchaseOrderNumber;
  }
  /**
   * The originally issued invoice(s) that is being cancelled by this invoice,
   * if applicable. May appear on invoice PDF as *Replaced invoice numbers*.
   * Note: There may be multiple replaced invoices due to consolidation of
   * multiple invoices into a single invoice.
   *
   * @param string[] $replacedInvoiceIds
   */
  public function setReplacedInvoiceIds($replacedInvoiceIds)
  {
    $this->replacedInvoiceIds = $replacedInvoiceIds;
  }
  /**
   * @return string[]
   */
  public function getReplacedInvoiceIds()
  {
    return $this->replacedInvoiceIds;
  }
  /**
   * The invoice service end date.
   *
   * @param string $serviceEndDate
   */
  public function setServiceEndDate($serviceEndDate)
  {
    $this->serviceEndDate = $serviceEndDate;
  }
  /**
   * @return string
   */
  public function getServiceEndDate()
  {
    return $this->serviceEndDate;
  }
  /**
   * The invoice service start date.
   *
   * @param string $serviceStartDate
   */
  public function setServiceStartDate($serviceStartDate)
  {
    $this->serviceStartDate = $serviceStartDate;
  }
  /**
   * @return string
   */
  public function getServiceStartDate()
  {
    return $this->serviceStartDate;
  }
  /**
   * The pre-tax subtotal amount, in micros of the invoice's currency.
   *
   * @param string $subtotalAmountMicros
   */
  public function setSubtotalAmountMicros($subtotalAmountMicros)
  {
    $this->subtotalAmountMicros = $subtotalAmountMicros;
  }
  /**
   * @return string
   */
  public function getSubtotalAmountMicros()
  {
    return $this->subtotalAmountMicros;
  }
  /**
   * The invoice total amount, in micros of the invoice's currency.
   *
   * @param string $totalAmountMicros
   */
  public function setTotalAmountMicros($totalAmountMicros)
  {
    $this->totalAmountMicros = $totalAmountMicros;
  }
  /**
   * @return string
   */
  public function getTotalAmountMicros()
  {
    return $this->totalAmountMicros;
  }
  /**
   * The sum of all taxes in invoice, in micros of the invoice's currency.
   *
   * @param string $totalTaxAmountMicros
   */
  public function setTotalTaxAmountMicros($totalTaxAmountMicros)
  {
    $this->totalTaxAmountMicros = $totalTaxAmountMicros;
  }
  /**
   * @return string
   */
  public function getTotalTaxAmountMicros()
  {
    return $this->totalTaxAmountMicros;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Invoice::class, 'Google_Service_Dfareporting_Invoice');

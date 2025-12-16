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

namespace Google\Service\AndroidPublisher;

class OrderHistory extends \Google\Collection
{
  protected $collection_key = 'partialRefundEvents';
  protected $cancellationEventType = CancellationEvent::class;
  protected $cancellationEventDataType = '';
  protected $partialRefundEventsType = PartialRefundEvent::class;
  protected $partialRefundEventsDataType = 'array';
  protected $processedEventType = ProcessedEvent::class;
  protected $processedEventDataType = '';
  protected $refundEventType = RefundEvent::class;
  protected $refundEventDataType = '';

  /**
   * Details of when the order was canceled.
   *
   * @param CancellationEvent $cancellationEvent
   */
  public function setCancellationEvent(CancellationEvent $cancellationEvent)
  {
    $this->cancellationEvent = $cancellationEvent;
  }
  /**
   * @return CancellationEvent
   */
  public function getCancellationEvent()
  {
    return $this->cancellationEvent;
  }
  /**
   * Details of the partial refund events for this order.
   *
   * @param PartialRefundEvent[] $partialRefundEvents
   */
  public function setPartialRefundEvents($partialRefundEvents)
  {
    $this->partialRefundEvents = $partialRefundEvents;
  }
  /**
   * @return PartialRefundEvent[]
   */
  public function getPartialRefundEvents()
  {
    return $this->partialRefundEvents;
  }
  /**
   * Details of when the order was processed.
   *
   * @param ProcessedEvent $processedEvent
   */
  public function setProcessedEvent(ProcessedEvent $processedEvent)
  {
    $this->processedEvent = $processedEvent;
  }
  /**
   * @return ProcessedEvent
   */
  public function getProcessedEvent()
  {
    return $this->processedEvent;
  }
  /**
   * Details of when the order was fully refunded.
   *
   * @param RefundEvent $refundEvent
   */
  public function setRefundEvent(RefundEvent $refundEvent)
  {
    $this->refundEvent = $refundEvent;
  }
  /**
   * @return RefundEvent
   */
  public function getRefundEvent()
  {
    return $this->refundEvent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrderHistory::class, 'Google_Service_AndroidPublisher_OrderHistory');

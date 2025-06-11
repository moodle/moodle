import { Injectable } from '@angular/core';
import {FeedErrorModalComponent} from "./feed-error-modal/feed-error-modal.component";

@Injectable({
  providedIn: 'root'
})
export class ErrorReporterService {

  private errorModal: FeedErrorModalComponent;

  constructor() { }

  public registerModal(modal: FeedErrorModalComponent) {
    this.errorModal = modal;
  }

  public relayError(error: any) {
    this.errorModal.displayError(error);
  }
}

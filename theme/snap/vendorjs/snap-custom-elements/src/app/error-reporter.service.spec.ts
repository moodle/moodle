import { TestBed } from '@angular/core/testing';

import { ErrorReporterService } from './error-reporter.service';
import {FeedErrorModalComponent} from "./feed-error-modal/feed-error-modal.component";

export class MockErrorReporterService {
  registerModal(modal: FeedErrorModalComponent) {
  }
  relayError(error: any) {
  }
}

describe('ErrorReporterService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: ErrorReporterService = TestBed.get(ErrorReporterService);
    expect(service).toBeTruthy();
  });
});

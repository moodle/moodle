import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FeedErrorModalComponent } from './feed-error-modal.component';
import {StringService} from "../string.service";
import {MockStringService} from "../string.service.spec";
import {ErrorReporterService} from "../error-reporter.service";
import {MockErrorReporterService} from "../error-reporter.service.spec";

describe('FeedErrorModalComponent', () => {
  let component: FeedErrorModalComponent;
  let fixture: ComponentFixture<FeedErrorModalComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FeedErrorModalComponent ],
      providers: [
        {
          provide: StringService,
          useClass: MockStringService
        },
        {
          provide: ErrorReporterService,
          useClass: MockErrorReporterService
        }
      ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FeedErrorModalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should update the message error', () => {
    let message = 'Error message';
    component.displayError(message);
    expect(component.error.message).toBe(message);
  });
});

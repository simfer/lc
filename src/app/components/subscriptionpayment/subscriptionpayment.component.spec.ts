import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SubscriptionpaymentComponent } from './subscriptionpayment.component';

describe('SubscriptionpaymentComponent', () => {
  let component: SubscriptionpaymentComponent;
  let fixture: ComponentFixture<SubscriptionpaymentComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SubscriptionpaymentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SubscriptionpaymentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

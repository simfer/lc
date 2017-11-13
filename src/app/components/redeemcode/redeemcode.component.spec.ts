import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RedeemcodeComponent } from './redeemcode.component';

describe('RedeemcodeComponent', () => {
  let component: RedeemcodeComponent;
  let fixture: ComponentFixture<RedeemcodeComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RedeemcodeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RedeemcodeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

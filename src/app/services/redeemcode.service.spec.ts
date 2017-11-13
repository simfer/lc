import { TestBed, inject } from '@angular/core/testing';

import { RedeemcodeService } from './redeemcode.service';

describe('RedeemcodeService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [RedeemcodeService]
    });
  });

  it('should be created', inject([RedeemcodeService], (service: RedeemcodeService) => {
    expect(service).toBeTruthy();
  }));
});

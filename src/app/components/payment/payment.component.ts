import { Component, OnInit } from '@angular/core';
import {Router} from "@angular/router";
import {AlertService} from '../../services/alert.service';

@Component({
  selector: 'app-payment',
  templateUrl: './payment.component.html',
  styleUrls: ['../../app.component.css']
})
export class PaymentComponent implements OnInit {
  favoritePayMethod: string = 'Paypal';
  paymentDisabled: boolean = false;

  payMethods = [
    'Paypal',
    'Visa',
    'American Express',
    'Mastercard',
  ];

  constructor(private router: Router,private alertService: AlertService) { }

  ngOnInit() {
  }

  paymentClick(event): void {
    if(event.value === 'Paypal') {
      this.paymentDisabled = false;
    } else this.paymentDisabled = true;
  }

  payNow(): void {
    this.router.navigate(['/paypal']);
  }

}

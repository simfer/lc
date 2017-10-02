import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from "@angular/router";
import { AlertService } from '../../services/alert.service';
import { Location} from "@angular/common";

@Component({
  selector: 'app-payment',
  templateUrl: './payment.component.html',
  styleUrls: ['../../app.component.css']
})
export class PaymentComponent implements OnInit {
  favoritePayMethod: string = 'Paypal';
  paymentDisabled: boolean = false;

  order: any;

  amount = '';

  payMethods = [
    'Paypal',
    'Visa',
    'American Express',
    'Mastercard',
  ];

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private location: Location,
    private alertService: AlertService) { }

  ngOnInit() {
    this.order = JSON.parse(sessionStorage.getItem("currentOrder"));
    console.log(this.order.totalAmount);
    //sessionStorage.removeItem('amount');
  }

  paymentClick(event): void {
    if(event.value === 'Paypal') {
      this.paymentDisabled = false;
    } else this.paymentDisabled = true;
  }

  payNow(): void {
    this.router.navigate(['/paypal']);
  }

  goBack() {
    this.location.back();
  }

}

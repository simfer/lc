import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-subscribe',
  templateUrl: './subscribe.component.html',
  styleUrls: ['./subscribe.component.css']
})
export class SubscribeComponent implements OnInit {
  favoritePayMethod: string = 'Paypal';

  payMethods = [
    'Paypal',
    'Visa',
    'American Express',
    'Mastercard',
  ];

  constructor() { }

  ngOnInit() {
  }

}

import { Component, OnInit } from '@angular/core';
import { Router } from "@angular/router";
import { Location} from "@angular/common";

@Component({
  selector: 'app-ordersummary',
  templateUrl: './ordersummary.component.html',
  styleUrls: ['../../app.component.css']
})
export class OrdersummaryComponent implements OnInit {
  order: any;
  constructor(
    private router: Router,
    private location: Location) {
  }

  ngOnInit() {
    // gets the current order from the session storage
    this.order = JSON.parse(sessionStorage.getItem("currentOrder"));
  }

  confirmOrder() {
    // if the user confirms the order, navigates to the payment page
    this.router.navigate(['/payment']);
  }

  goBack() {
    this.location.back();
  }
}

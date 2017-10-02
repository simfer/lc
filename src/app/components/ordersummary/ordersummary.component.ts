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
    this.order = JSON.parse(sessionStorage.getItem("currentOrder"));
  }

  confirmOrder() {
    this.router.navigate(['/payment']);
  }

  goBack() {
    sessionStorage.removeItem('currentOrder');
    this.location.back();
  }
}

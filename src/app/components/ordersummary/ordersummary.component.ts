import {Component, OnInit} from '@angular/core';
import {Order} from "../../interfaces/order";
import {OrderService} from "../../services/order.service";
import { DatePipe } from '@angular/common';
import {MdSnackBar} from "@angular/material";
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
    private location: Location,
    private orderService: OrderService,
    private datePipe: DatePipe,
    private snackBar: MdSnackBar) {
    this.order = JSON.parse(localStorage.getItem("currentOrder"));
  }

  ngOnInit() {
  }

  confirmOrder() {
    const currentDate = new Date();
    const customer = JSON.parse(localStorage.getItem("currentCustomer"));
    let o = <Order>{};
    console.log(this.order);

    let regions = '';
    for (var i=0;i<this.order.regions.length;i++) {
      regions += this.order.regions[i].region + '|';
    }
    regions = regions.slice(0, -1);

    o.idcustomer = customer.idcustomer;
    o.idproduct = this.order.product;
    o.idcolor = this.order.color;
    o.amount = this.order.totalAmount;
    o.quantity = this.order.quantity;
    o.regions = regions;
    o.orderdate = this.datePipe.transform(currentDate, 'yyyy-MM-dd hh:mm:ss');
    o.idstatus = '0';
    console.log(o);


    this.orderService.add(o)
      .then(response => {
        let orderNumber = response['lastInsertID'];
        let snackBarRef = this.snackBar.open('Ordine n.' + orderNumber + ' effettuato con successo!',null,{
          extraClasses: ['snackbar-class'],
          duration: 5000
        });
        localStorage.removeItem('currentOrder');
        this.router.navigate(['/home']);
      });


  }

  goBack() {
    this.location.back();
  }
}

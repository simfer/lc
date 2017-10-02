import { Component, OnInit } from '@angular/core';
import { MdIconRegistry } from '@angular/material';
import { DomSanitizer } from '@angular/platform-browser';
import {Http, Headers, Response} from '@angular/http';
import 'rxjs/add/operator/map';
import {Router} from "@angular/router";
import { Order } from "../../interfaces/order";
import { DatePipe } from '@angular/common';
import { OrderService} from "../../services/order.service";
import {MdSnackBar} from '@angular/material';

@Component({
  selector: 'app-paypal',
  templateUrl: './paypal.component.html',
  styleUrls: ['../../app.component.css']
})
export class PaypalComponent implements OnInit {
  // token = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE1MDQxOTE0ODUsImp0aSI6IlhxbmxPNW5yd2NLempDK3YzSW1CR2FuK0xDY3kzbzdaNmw1eTFNcTNcL28wPSIsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDgwXC9sb3ZlY2hhbGxlbmdlXC9zZXJ2ZXIiLCJuYmYiOjE1MDQxOTE0ODUsImV4cCI6MTUwNDc5NjI4NSwiZGF0YSI6eyJ1c2VySWQiOiIxIiwidXNlck5hbWUiOiJhZG1pbiJ9fQ.2UBtH7LgRHnCagmshMwajLoubnjaGHSS7QsZEPjMHmgIFhoNLktl7eJn2_pEE2WVNdDt4pHniXqvLPXp-wO9LA';
  // private headers = new Headers({'Content-Type': 'application/json', 'Authorization': this.token});
  private headers = new Headers({'Content-Type': 'application/json'});
  private result;

  currentOrder: any;
  currentCustomer: any;

  constructor(private mdIconRegistry: MdIconRegistry,
              private sanitizer: DomSanitizer,
              private http: Http,
              private datePipe: DatePipe,
              private orderService: OrderService,
              private snackBar: MdSnackBar,
              private router: Router) { }

  ngOnInit() {
    this.currentOrder = JSON.parse(sessionStorage.getItem('currentOrder'));
    this.currentCustomer = JSON.parse(localStorage.getItem("currentCustomer"));

    this.mdIconRegistry.addSvgIconInNamespace('img', 'paypal',
      this.sanitizer.bypassSecurityTrustResourceUrl('/assets/images/PayPal.svg'));
  }

  simulatePayment(): void {
    switch (this.currentOrder.type) {
      case 'product':
        const currentDate = new Date();
        let o = <Order>{};

        let regions = '';
        for (var i=0;i<this.currentOrder.regions.length;i++) {
          regions += this.currentOrder.regions[i].region + '|';
        }
        regions = regions.slice(0, -1);

        o.idcustomer = this.currentCustomer.idcustomer;
        o.idproduct = this.currentOrder.product;
        o.idcolor = this.currentOrder.color;
        o.amount = this.currentOrder.totalAmount;
        o.quantity = this.currentOrder.quantity;
        o.regions = regions;
        o.orderdate = this.datePipe.transform(currentDate, 'yyyy-MM-dd hh:mm:ss');

        this.orderService.add(o)
          .then(response => {
            let orderNumber = response['lastInsertID'];
            let snackBarRef = this.snackBar.open('Ordine n.' + orderNumber + ' effettuato con successo!',null,{
              extraClasses: ['snackbar-class'],
              duration: 5000
            });
            sessionStorage.removeItem('currentOrder');
            this.router.navigate(['/home']);
          });
        break;

      case 'subscription':
        this.http.put('/api/v1/customersubscribe/'+this.currentCustomer.idcustomer,{} , {headers: this.headers})
          .map(response => response.json())
          .subscribe(result => {
            this.result = result;
            this.currentCustomer['subscribed'] = '1';
            localStorage.setItem('currentCustomer', JSON.stringify(this.currentCustomer));
            let snackBarRef = this.snackBar.open('Pagamento avvenuto con successo!',null,{
              extraClasses: ['snackbar-class'],
              duration: 5000
            });
            sessionStorage.removeItem('currentOrder');
            this.router.navigate(['/subscribe']);
          });
        break;
      default:
    }
  }
}

/*
export class PaypalComponent implements OnInit {
  private didPaypalScriptLoad: boolean = false;
  private loading: boolean = true;

  private paypalConfig: any = {
    env: 'sandbox',
    client: {
      sandbox: 'AWlMGZwpQbS0dq_r2Dt0ejp1TxDm72JD7Pt4Uc2mYlihAE3FU5axxS9wr4HcnVc13gB7TcbYDVLp9Vne',
      production: 'xxxxxxxxxx'
    },
    commit: true,
    payment: (data, actions) => {
      return actions.payment.create({
        payment: {
          transactions: [
            { amount: { total: '0.01', currency: 'EUR' } }
          ]
        }
      });
    },
    onAuthorize: (data, actions) => {
      // show success page
    }
  };

  ngAfterViewChecked() {
    if(!this.didPaypalScriptLoad) {
      this.loadPaypalScript().then(() => {
        paypal.Button.render(this.paypalConfig, '#paypal-button');
        this.loading = false;
      });
    }
  }

  loadPaypalScript(): Promise<any> {
    this.didPaypalScriptLoad = true;
    return new Promise((resolve, reject) => {
      const scriptElement = document.createElement('script');
      scriptElement.src = 'https://www.paypalobjects.com/api/checkout.js';
      scriptElement.onload = resolve;
      document.body.appendChild(scriptElement);
    });
  }

  constructor() { }

  ngOnInit() {
    }

}

 */

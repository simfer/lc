import { Component, OnInit } from '@angular/core';
import { MdIconRegistry } from '@angular/material';
import { DomSanitizer } from '@angular/platform-browser';
import {Http, Headers, Response} from '@angular/http';
import 'rxjs/add/operator/map';
import {Router} from "@angular/router";

import {MdSnackBar} from '@angular/material';

@Component({
  selector: 'app-paypal',
  templateUrl: './paypal.component.html',
  styleUrls: ['./paypal.component.css']
})
export class PaypalComponent implements OnInit {
  // token = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE1MDQxOTE0ODUsImp0aSI6IlhxbmxPNW5yd2NLempDK3YzSW1CR2FuK0xDY3kzbzdaNmw1eTFNcTNcL28wPSIsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDgwXC9sb3ZlY2hhbGxlbmdlXC9zZXJ2ZXIiLCJuYmYiOjE1MDQxOTE0ODUsImV4cCI6MTUwNDc5NjI4NSwiZGF0YSI6eyJ1c2VySWQiOiIxIiwidXNlck5hbWUiOiJhZG1pbiJ9fQ.2UBtH7LgRHnCagmshMwajLoubnjaGHSS7QsZEPjMHmgIFhoNLktl7eJn2_pEE2WVNdDt4pHniXqvLPXp-wO9LA';
  // private headers = new Headers({'Content-Type': 'application/json', 'Authorization': this.token});
  private headers = new Headers({'Content-Type': 'application/json'});
  private customerId = 2;
  private result;

  simulatePayment(): void {
    console.log("payed");
    this.http.put('/api/v1/customersubscribe/'+this.customerId,{} , {headers: this.headers})
      .map(response => response.json())
      .subscribe(result => {
        this.result = result;
        let currentCustomer = JSON.parse(localStorage.getItem('currentCustomer'));
        currentCustomer['subscribed'] = '1';
        localStorage.setItem('currentCustomer', JSON.stringify(currentCustomer));
        alert('Pagamento avvenuto con successo');
        console.log(this.result);
        this.router.navigate(['/subscribe']);
      });
  }

  constructor(private mdIconRegistry: MdIconRegistry,
              private sanitizer: DomSanitizer,
              private http: Http,
              private router: Router) { }

  ngOnInit() {
    this.mdIconRegistry.addSvgIconInNamespace('img', 'paypal',
      this.sanitizer.bypassSecurityTrustResourceUrl('/assets/images/PayPal.svg'));
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

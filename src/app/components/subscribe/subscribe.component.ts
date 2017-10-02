import { Component, OnInit } from '@angular/core';
import { Router} from "@angular/router";

@Component({
  selector: 'app-subscribe',
  templateUrl: './subscribe.component.html',
  styleUrls: ['../../app.component.css']
})
export class SubscribeComponent implements OnInit {

  constructor(private router: Router) { }

  ngOnInit() {
  }

  subscribeMe() {
    let order = {
      type: 'subscription',
      totalAmount: '5'
    };

    console.log(order);

    sessionStorage.setItem('currentOrder', JSON.stringify(order));

    this.router.navigate(['/payment']);
  }
}

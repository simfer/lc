import {Component, OnInit, ViewContainerRef} from '@angular/core';
import {Router} from "@angular/router";
import {MdDialog, MdDialogRef, MD_DIALOG_DATA} from '@angular/material';
import {DialogsService} from '../../services/dialogs.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['../../app.component.css']
})
export class HomeComponent implements OnInit {
  public result: any;

  constructor(
    private dialogsService: DialogsService,
    private router:Router,
    public dialog: MdDialog,
    public viewContainerRef: ViewContainerRef
  ) { }

  ngOnInit() {
    let k = {value1:34,value2:76};
    this.cookie_create('mybestcookie',JSON.stringify(k),1);
  }

  redeemCode():void {
    this.router.navigate(['/redeemcode']);
  }

  buyDiamonds():void {
    this.router.navigate(['/order']);
  }

  openOrdersList() {
    let k = this.cookie_read('mybestcookie');
    console.log(k);
    console.log(JSON.parse(k).value1);
    this.router.navigate(['/orderslist']);
  }

  cookie_create(name,value,days) {
    var expires, date;

    if (days) {
      date = new Date();
      date.setTime(date.getTime()+(days*24*60*60*1000));
      expires = "; expires="+date.toGMTString();
    }
    else expires = "";
    document.cookie = name+"="+value+expires+"; path=/";

    expires = date = null;
  };

  cookie_read(name) {
    var nameEQ = name + "=",
      ca = document.cookie.split(';'),
      len = ca.length,
      i, c;

    for(i = 0; i < len; ++i) {
      c = ca[i];
      while (c.charAt(0) === ' ') c = c.substring(1); //,c.length);
      if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length); //,c.length);
    }

    nameEQ = name = ca = i = c = len = null;
    return null;
  };

}

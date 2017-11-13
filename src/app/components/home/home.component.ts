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
  }

  redeemCode():void {
    this.router.navigate(['/redeemcode']);
  }

  buyDiamonds():void {
    this.router.navigate(['/order']);
  }

  openOrdersList() {
    this.router.navigate(['/orderslist']);
  }
}

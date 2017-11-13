import { Component, OnInit } from '@angular/core';
import { MdDialogRef } from "@angular/material";

@Component({
  selector: 'app-alertdialog',
  templateUrl: './alertdialog.component.html',
  styleUrls: ['./alertdialog.component.css']
})
export class AlertdialogComponent {
  public title: string;
  public message: string;

  constructor(public dialogRef: MdDialogRef<AlertdialogComponent>) {

  }

}


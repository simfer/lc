import { Observable } from 'rxjs/Rx';
import {AlertdialogComponent} from '../components/alertdialog/alertdialog.component';
import { MdDialogRef, MdDialog, MdDialogConfig } from '@angular/material';
import { Injectable } from '@angular/core';

@Injectable()
export class DialogsService {

  constructor(private dialog: MdDialog) { }

  public confirm(title: string, message: string): Observable<boolean> {

    let dialogRef: MdDialogRef<AlertdialogComponent>;

    dialogRef = this.dialog.open(AlertdialogComponent);

    dialogRef.componentInstance.title = title;
    dialogRef.componentInstance.message = message;

    return dialogRef.afterClosed();
  }
}

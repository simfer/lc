import { Component, OnInit, ElementRef, ViewChild } from '@angular/core';
import {DataSource} from '@angular/cdk/collections';
import {BehaviorSubject} from 'rxjs/BehaviorSubject';
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/observable/merge';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/debounceTime';
import 'rxjs/add/operator/distinctUntilChanged';
import 'rxjs/add/observable/fromEvent';
import {OrderService} from "../../services/order.service";
import 'rxjs/add/operator/map';
import { CustomerOrder} from "../../interfaces/customer-order";
import {Localstorage} from "../../interfaces/localstorage";
import { Location} from "@angular/common";

@Component({
  selector: 'app-orders-list',
  templateUrl: './orders-list.component.html',
  styleUrls: ['../../app.component.css']
})
export class OrdersListComponent implements OnInit {
  displayedColumns = ['idorder','product','categories','provinces','orderdate','status'];
  exampleDatabase = new ExampleDatabase();
  dataSource: ExampleDataSource | null;
  customerId = '';

  @ViewChild('filter') filter: ElementRef;

  constructor(
    private location: Location,
    private service: OrderService) {
    let ls: Localstorage = JSON.parse(localStorage.getItem('currentCustomer'));

    if (ls) {
       this.customerId = ls.idcustomer;
    }
  }

  ngOnInit() {
    this.dataSource = new ExampleDataSource(this.exampleDatabase);

    Observable.fromEvent(this.filter.nativeElement, 'keyup')
      .debounceTime(150)
      .distinctUntilChanged()
      .subscribe(() => {
        if (!this.dataSource) { return; }
        this.dataSource.filter = this.filter.nativeElement.value;
        console.log('keyup');
      });


    this.service.getCustomerOrders(this.customerId).then(data => {
      data.map(function (row) {
        row.amount += ' €';
      });
      this.exampleDatabase.loadData(data);
    });

  }

  goBack() {
    this.location.back();
  }
}

export class ExampleDatabase {
  dataChange: BehaviorSubject<CustomerOrder[]> = new BehaviorSubject<CustomerOrder[]>([]);
  get data(): CustomerOrder[] { return this.dataChange.value; }

  loadData(data) {
    this.dataChange.next(data);
  }
}


export class ExampleDataSource extends DataSource<any> {
  _filterChange = new BehaviorSubject('');
  get filter(): string { return this._filterChange.value; }
  set filter(filter: string) { this._filterChange.next(filter); }

  constructor(private _exampleDatabase: ExampleDatabase) {
    super();
  }

  connect(): Observable<CustomerOrder[]> {
    const displayDataChanges = [
      this._exampleDatabase.dataChange,
      this._filterChange,
    ];

    //return this._exampleDatabase.dataChange;
    return Observable.merge(...displayDataChanges).map(() => {
      return this._exampleDatabase.data.slice().filter((item: CustomerOrder) => {
        let searchStr = (item.product + item.status).toLowerCase();
        return searchStr.indexOf(this.filter.toLowerCase()) != -1;
      });
    });

  }

  disconnect() {}
}

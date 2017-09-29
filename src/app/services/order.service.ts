import {Injectable} from '@angular/core';
import {Order} from '../interfaces/order';
import { CustomerOrder} from "../interfaces/customer-order";
import {Headers, Http} from '@angular/http';
import 'rxjs/add/operator/toPromise';

@Injectable()
export class OrderService {
  private host = window.location.hostname;
  private headers = new Headers({'Content-Type': 'application/json'});
  private ordersURL = '/api/v1/orders/';

  constructor(private http: Http) {}

  /**
   * Return all orders
   * @returns {Promise<Order[]>}
   */
  getOrders(): Promise<Order[]> {
    return this.http.get(this.ordersURL)
      .toPromise()
      .then(response => {
        return response.json() as Order[];
      })
      .catch(this.handleError);
  }

  /**
   * Returns order based on id
   * @param id:string
   * @returns {Promise<Order>}
   */
  getOrder(id: string): Promise<Order> {
    const url = `${this.ordersURL}${id}`;
    return this.http.get(url)
      .toPromise()
      .then(response => response.json() as Order)
      .catch(this.handleError);
  }

  /**
   * Returns order based on id
   * @param id:string
   * @returns {Promise<Order>}
   */
  getCustomerOrders(idcustomer: string): Promise<Order> {
    let s = '/api/v1/customerorders/';
    const url = `${s}${idcustomer}`;
    return this.http.get(url)
      .toPromise()
      .then(response => response.json() as CustomerOrder)
      .catch(this.handleError);
  }

  /**
   * Adds new order
   * @param order:Order
   * @returns {Promise<Order>}
   */
  add(order: Order): Promise<Order> {
    return this.http.post(this.ordersURL, JSON.stringify(order), {headers: this.headers})
      .toPromise()
      .then(response => response.json() as Order)
      .catch(this.handleError);
  }

  /**
   * Updates order that matches to id
   * @param order:Order
   * @returns {Promise<Order>}
   */
  update(order: Order): Promise<Order> {
    return this.http.put(`${this.ordersURL}${order.idorder}`, JSON.stringify(order), {headers: this.headers})
      .toPromise()
      .then(response => response.json() as Order)
      .catch(this.handleError);
  }

  /**
   * Removes order
   * @param id:string
   * @returns {Promise<Order>}
   */
  remove(id: string): Promise<any> {
    return this.http.delete(`${this.ordersURL}${id}`)
      .toPromise()
      .then(response => console.log(response))
      .catch(this.handleError);
  }

  /**
   * Handles error thrown during HTTP call
   * @param error:any
   * @returns {Promise<never>}
   */
  private handleError(error: any): Promise<any> {
    console.error('An error occurred', error); // for demo purposes only
    return Promise.reject(error.message || error);
  }
}

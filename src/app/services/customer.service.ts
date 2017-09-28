import {Injectable} from '@angular/core';
import {Customer} from '../interfaces/customer';
import {Headers, Http} from '@angular/http';
import 'rxjs/add/operator/toPromise';

@Injectable()
export class CustomerService {
  private host = window.location.hostname;
  private headers = new Headers({'Content-Type': 'application/json'});
  private customersURL = '/api/v1/customers/';

  constructor(private http: Http) {}

  /**
   * Return all customers
   * @returns {Promise<Customer[]>}
   */
  getCustomers(): Promise<Customer[]> {
    return this.http.get(this.customersURL)
      .toPromise()
      .then(response => {
        return response.json() as Customer[];
      })
      .catch(this.handleError);
  }

  /**
   * Returns customer based on id
   * @param id:string
   * @returns {Promise<Customer>}
   */
  getCustomer(id: string): Promise<Customer> {
    const url = `${this.customersURL}${id}`;
    return this.http.get(url)
      .toPromise()
      .then(response => response.json() as Customer)
      .catch(this.handleError);
  }

  /**
   * Adds new customer
   * @param customer:Customer
   * @returns {Promise<Customer>}
   */
  add(customer: Customer): Promise<Customer> {
    return this.http.post(this.customersURL, JSON.stringify(customer), {headers: this.headers})
      .toPromise()
      .then(response => response.json() as Customer)
      .catch(this.handleError);
  }

  /**
   * Adds new customer
   * @param customer:Customer
   * @returns {Promise<Customer>}
   */
  checkExistingUsername(username: string): Promise<any> {
    let apiURL = '/api/v1/checkexistingusername/';
    return this.http.post(apiURL, JSON.stringify({username:username}), {headers: this.headers})
      .toPromise()
      .then(response => response.json())
      .catch(this.handleError);
  }

  /**
   * Updates customer that matches to id
   * @param customer:Customer
   * @returns {Promise<Customer>}
   */
  update(customer: Customer): Promise<Customer> {
    return this.http.put(`${this.customersURL}${customer.idcustomer}`, JSON.stringify(customer), {headers: this.headers})
      .toPromise()
      .then(response => response.json() as Customer)
      .catch(this.handleError);
  }

  /**
   * Removes customer
   * @param id:string
   * @returns {Promise<Customer>}
   */
  remove(id: string): Promise<any> {
    return this.http.delete(`${this.customersURL}${id}`)
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

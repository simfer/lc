import {Injectable} from '@angular/core';
import {Product} from '../interfaces/product';
import {Headers, Http} from '@angular/http';
import 'rxjs/add/operator/toPromise';

@Injectable()
export class ProductService {
  private host = window.location.hostname;
  private headers = new Headers({'Content-Type': 'application/json'});
  private productsURL = 'server/api/v1/products/';

  constructor(private http: Http) {}

  /**
   * Return all products
   * @returns {Promise<Product[]>}
   */
  getProducts(): Promise<Product[]> {
    return this.http.get(this.productsURL)
      .toPromise()
      .then(response => {
        return response.json() as Product[];
      })
      .catch(this.handleError);
  }

  /**
   * Returns product based on id
   * @param id:string
   * @returns {Promise<Product>}
   */
  getProduct(id: string): Promise<Product> {
    const url = `${this.productsURL}${id}`;
    return this.http.get(url)
      .toPromise()
      .then(response => response.json() as Product)
      .catch(this.handleError);
  }

  /**
   * Adds new product
   * @param product:Product
   * @returns {Promise<Product>}
   */
  add(product: Product): Promise<Product> {
    return this.http.post(this.productsURL, JSON.stringify(product), {headers: this.headers})
      .toPromise()
      .then(response => response.json() as Product)
      .catch(this.handleError);
  }

  /**
   * Updates product that matches to id
   * @param product:Product
   * @returns {Promise<Product>}
   */
  update(product: Product): Promise<Product> {
    return this.http.put(`${this.productsURL}${product.idproduct}`, JSON.stringify(product), {headers: this.headers})
      .toPromise()
      .then(response => response.json() as Product)
      .catch(this.handleError);
  }

  /**
   * Removes product
   * @param id:string
   * @returns {Promise<Product>}
   */
  remove(id: string): Promise<any> {
    return this.http.delete(`${this.productsURL}${id}`)
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

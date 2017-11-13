import {Injectable} from '@angular/core';
import {Province} from '../interfaces/province';
import {Headers, Http} from '@angular/http';
import 'rxjs/add/operator/toPromise';

@Injectable()
export class ProvinceService {
  private host = window.location.hostname;
  private headers = new Headers({'Content-Type': 'application/json'});
  private provincesURL = 'server/api/v1/provinces/';

  constructor(private http: Http) {}

  /**
   * Return all provinces
   * @returns {Promise<Province[]>}
   */
  getProvinces(): Promise<Province[]> {
    return this.http.get(this.provincesURL)
      .toPromise()
      .then(response => {
        return response.json() as Province[];
      })
      .catch(this.handleError);
  }

  /**
   * Returns province based on id
   * @param id:string
   * @returns {Promise<Province>}
   */
  getProvince(id: string): Promise<Province> {
    const url = `${this.provincesURL}${id}`;
    return this.http.get(url)
      .toPromise()
      .then(response => response.json() as Province)
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

import {Injectable} from '@angular/core';
import {City} from '../interfaces/city';
import {Headers, Http} from '@angular/http';
import 'rxjs/add/operator/toPromise';

@Injectable()
export class CitiesService {
  private host = window.location.hostname;
  private headers = new Headers({'Content-Type': 'application/json'});
  private citiesURL = 'server/api/v1/cities/';

  constructor(private http: Http) {}

  /**
   * Return all cities
   * @returns {Promise<City[]>}
   */
  getCities(): Promise<City[]> {
    return this.http.get(this.citiesURL)
      .toPromise()
      .then(response => {
        return response.json() as City[];
      })
      .catch(this.handleError);
  }

  /**
   * Returns city based on id
   * @param id:string
   * @returns {Promise<City>}
   */
  getCity(id: string): Promise<City> {
    const url = `${this.citiesURL}${id}`;
    return this.http.get(url)
      .toPromise()
      .then(response => response.json() as City)
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

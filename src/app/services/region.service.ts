import {Injectable} from '@angular/core';
import {Region} from '../interfaces/region';
import {Headers, Http} from '@angular/http';
import 'rxjs/add/operator/toPromise';

@Injectable()
export class RegionService {
  private host = window.location.hostname;
  private headers = new Headers({'Content-Type': 'application/json'});
  private regionsURL = '/api/v1/regions/';

  constructor(private http: Http) {}

  /**
   * Return all regions
   * @returns {Promise<Region[]>}
   */
  getRegions(): Promise<Region[]> {
    return this.http.get(this.regionsURL)
      .toPromise()
      .then(response => {
        return response.json() as Region[];
      })
      .catch(this.handleError);
  }

  /**
   * Returns region based on id
   * @param id:string
   * @returns {Promise<Region>}
   */
  getRegion(id: string): Promise<Region> {
    const url = `${this.regionsURL}${id}`;
    return this.http.get(url)
      .toPromise()
      .then(response => response.json() as Region)
      .catch(this.handleError);
  }

  /**
   * Adds new region
   * @param region:Region
   * @returns {Promise<Region>}
   */
  add(region: Region): Promise<Region> {
    return this.http.post(this.regionsURL, JSON.stringify(region), {headers: this.headers})
      .toPromise()
      .then(response => response.json() as Region)
      .catch(this.handleError);
  }

  /**
   * Updates region that matches to id
   * @param region:Region
   * @returns {Promise<Region>}
   */
  update(region: Region): Promise<Region> {
    return this.http.put(`${this.regionsURL}${region.idregion}`, JSON.stringify(region), {headers: this.headers})
      .toPromise()
      .then(response => response.json() as Region)
      .catch(this.handleError);
  }

  /**
   * Removes region
   * @param id:string
   * @returns {Promise<Region>}
   */
  remove(id: string): Promise<any> {
    return this.http.delete(`${this.regionsURL}${id}`)
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

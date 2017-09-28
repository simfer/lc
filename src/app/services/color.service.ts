import {Injectable} from '@angular/core';
import {Color} from '../interfaces/color';
import {Headers, Http} from '@angular/http';
import 'rxjs/add/operator/toPromise';

@Injectable()
export class ColorService {
  private host = window.location.hostname;
  private headers = new Headers({'Content-Type': 'application/json'});
  private colorsURL = '/api/v1/colors/';

  constructor(private http: Http) {}

  /**
   * Return all colors
   * @returns {Promise<Color[]>}
   */
  getColors(): Promise<Color[]> {
    return this.http.get(this.colorsURL)
      .toPromise()
      .then(response => {
        return response.json() as Color[];
      })
      .catch(this.handleError);
  }

  /**
   * Returns color based on id
   * @param id:string
   * @returns {Promise<Color>}
   */
  getColor(id: string): Promise<Color> {
    const url = `${this.colorsURL}${id}`;
    return this.http.get(url)
      .toPromise()
      .then(response => response.json() as Color)
      .catch(this.handleError);
  }

  /**
   * Adds new color
   * @param color:Color
   * @returns {Promise<Color>}
   */
  add(color: Color): Promise<Color> {
    return this.http.post(this.colorsURL, JSON.stringify(color), {headers: this.headers})
      .toPromise()
      .then(response => response.json() as Color)
      .catch(this.handleError);
  }

  /**
   * Updates color that matches to id
   * @param color:Color
   * @returns {Promise<Color>}
   */
  update(color: Color): Promise<Color> {
    return this.http.put(`${this.colorsURL}${color.idcolor}`, JSON.stringify(color), {headers: this.headers})
      .toPromise()
      .then(response => response.json() as Color)
      .catch(this.handleError);
  }

  /**
   * Removes color
   * @param id:string
   * @returns {Promise<Color>}
   */
  remove(id: string): Promise<any> {
    return this.http.delete(`${this.colorsURL}${id}`)
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

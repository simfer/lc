import {Injectable} from '@angular/core';
import {Category} from '../interfaces/category';
import {Headers, Http} from '@angular/http';
import 'rxjs/add/operator/toPromise';

@Injectable()
export class CategoryService {
  private host = window.location.hostname;
  private headers = new Headers({'Content-Type': 'application/json'});
  private categoriesURL = 'server/api/v1/categories/';

  constructor(private http: Http) {}

  /**
   * Return all categories
   * @returns {Promise<Category[]>}
   */
  getCategories(): Promise<Category[]> {
    return this.http.get(this.categoriesURL)
      .toPromise()
      .then(response => {
        return response.json() as Category[];
      })
      .catch(this.handleError);
  }

  /**
   * Returns category based on id
   * @param id:string
   * @returns {Promise<Category>}
   */
  getCategory(id: string): Promise<Category> {
    const url = `${this.categoriesURL}${id}`;
    return this.http.get(url)
      .toPromise()
      .then(response => response.json() as Category)
      .catch(this.handleError);
  }

  /**
   * Adds new category
   * @param category:Category
   * @returns {Promise<Category>}
   */
  add(category: Category): Promise<Category> {
    return this.http.post(this.categoriesURL, JSON.stringify(category), {headers: this.headers})
      .toPromise()
      .then(response => response.json() as Category)
      .catch(this.handleError);
  }

  /**
   * Updates category that matches to id
   * @param category:Category
   * @returns {Promise<Category>}
   */
  update(category: Category): Promise<Category> {
    return this.http.put(`${this.categoriesURL}${category.idcategory}`, JSON.stringify(category), {headers: this.headers})
      .toPromise()
      .then(response => response.json() as Category)
      .catch(this.handleError);
  }

  /**
   * Removes category
   * @param id:string
   * @returns {Promise<Category>}
   */
  remove(id: string): Promise<any> {
    return this.http.delete(`${this.categoriesURL}${id}`)
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

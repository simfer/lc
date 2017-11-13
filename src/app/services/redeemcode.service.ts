import { Injectable } from '@angular/core';
import {Http, Headers, Response} from '@angular/http';
import 'rxjs/add/operator/map';
import { Redeemcode } from '../interfaces/redeemcode';
import { DatePipe } from '@angular/common';

@Injectable()
export class RedeemcodeService {
  private headers = new Headers({'Content-Type': 'application/json'});

  constructor(private http: Http, private datePipe: DatePipe) {}

  //http://localhost:8080/lovechallenge/server/api/v1/availableorders?redeemcode=00120315&category=3
  checkRedeemCode(codeToRedeem:string) {
    return this.http.post('server/api/v1/checkredeemcode', JSON.stringify({code: codeToRedeem}), {headers: this.headers});
  }

  getAvailableOrders(codeToRedeem:string, category:string): Promise<Redeemcode> {
    return this.http.get('server/api/v1/availableorders?redeemcode='+ codeToRedeem +'&category=' + category,{headers: this.headers})
      .toPromise()
      .then(response => {
        return response.json() as Redeemcode;
      })
      .catch(this.handleError);
  }

  getAvailableCategories(codeToRedeem:string) {
    return this.http.get('server/api/v1/availablecategories/'+codeToRedeem,  {headers: this.headers});
  }

  redeemCode(codeToRedeem:number, customer:string) {
    const currentDate = new Date();
    return this.http.put('server/api/v1/redeemcodes/'+codeToRedeem, JSON.stringify({
      "redeemed" : "1",
      "redeemedby": customer,
      "redeemedat": this.datePipe.transform(currentDate, 'yyyy-MM-dd hh:mm:ss')
    }), {headers: this.headers});
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

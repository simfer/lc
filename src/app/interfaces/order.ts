export interface Order {
  idorder: number;
  idcustomer: number;
  customer: string;
  idproduct: number;
  productDescription: string;
  productType: string;
  categories: string[];
  provinces: string[];
  quantity: number;
  amount: number;
  orderdate: string;
  idstatus: string;
  active: string;
  changedby: string;
  changedat: string;
}

import { Component, OnInit } from '@angular/core';
import { Router } from "@angular/router";
import { Province} from '../../interfaces/province';
import { ProvinceService } from '../../services/province.service';
import { CategoryService } from "../../services/category.service";
import { Product } from "../../interfaces/product";
import { Category } from "../../interfaces/category";
import { ProductService } from "../../services/product.service";
import { Location} from "@angular/common";
import { Order } from "../../interfaces/order";

@Component({
  selector: 'app-order',
  templateUrl: './order.component.html',
  styleUrls: ['../../app.component.css']
})
export class OrderComponent implements OnInit {
  provinces: Province[]; //list of provinces
  products: Product[]; //list of products
  categories: Category[]; //list of categories

  selectedProduct = '';
  selectedCategories = '';
  selectedProvinces = '';

  constructor(
    private router: Router,
    private location: Location,
    private productService: ProductService,
    private provinceService: ProvinceService,
    private categoryService: CategoryService
  ) { }

  ngOnInit() {
    let currentOrder = sessionStorage.getItem('currentOrder'); //reads the current order from the session
    console.log(currentOrder);
    // *** here must be added the ability to read from the existing session variable ***
    this.getProvinces(); //reads all the provinces
    this.getProducts(); //reads all the products
    this.getCategories(); //reads all the categories
  }

  // call the service to load all the products
  getProducts() {
    this.productService.getProducts()
      .then(products => {
        this.products = products;
      });
  }

  // call the service to load all the categories
  getCategories() {
    this.categoryService.getCategories()
      .then(categories => {
        this.categories = categories;
      });
  }

  // call the service to load all the provinces
  getProvinces() {
    this.provinceService.getProvinces()
      .then(provinces => {
        this.provinces = provinces;
      });
  }

  orderNow() {
    let order_provinces = [];
    let order_categories = [];

    // creates an array with the selected provinces
    for (var i=0;i<this.selectedProvinces.length;i++) {
      order_provinces.push({province: this.provinces[this.selectedProvinces[i]].idprovince,provinceDescription:this.provinces[this.selectedProvinces[i]].description});
    }
    // creates an array with the selected categories
    for (var i=0;i<this.selectedCategories.length;i++) {
      order_categories.push({category: this.categories[this.selectedCategories[i]].idcategory,categoryDescription:this.categories[this.selectedCategories[i]].description});
    }


    // creates the "order" object
    let order = <Order>{};
    order.productType = 'product';
    order.idproduct = this.products[this.selectedProduct].idproduct;
    order.productDescription = this.products[this.selectedProduct].description;
    order.categories = order_categories;
    order.amount = this.products[this.selectedProduct].price;
    order.quantity = this.products[this.selectedProduct].quantity;
    order.provinces = order_provinces;

    // stores the "order" object into a session variable
    sessionStorage.setItem('currentOrder', JSON.stringify(order));

    // navigates to order summary
    this.router.navigate(['/ordersummary']);

  }

  goBack(e) {
    e.preventDefault();
    this.location.back();
  }
}

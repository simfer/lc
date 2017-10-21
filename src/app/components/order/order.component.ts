import { Component, OnInit } from '@angular/core';
import { Router } from "@angular/router";
import { Province} from '../../interfaces/province';
import { ProvinceService } from '../../services/province.service';
import { Product } from "../../interfaces/product";
import { Category } from "../../interfaces/category";
import { ProductService } from "../../services/product.service";
import { CategoryService } from "../../services/category.service";
import { Location} from "@angular/common";

@Component({
  selector: 'app-order',
  templateUrl: './order.component.html',
  styleUrls: ['../../app.component.css']
})
export class OrderComponent implements OnInit {
  provinces: Province[];
  products: Product[];
  categories: Category[];

  selectedProduct = '';
  selectedCategories = '';
  selectedProvinces = '';

  constructor(
    private router: Router,
    private location: Location,
    private provinceService: ProvinceService,
    private productService: ProductService,
    private categoryService: CategoryService
  ) { }

  ngOnInit() {
    this.getProvinces();
    this.getProducts();
    this.getCategories();
  }

  getProducts() {
    this.productService.getProducts()
      .then(products => {
        this.products = products;
      });
  }
  getCategories() {
    this.categoryService.getCategories()
      .then(categories => {
        this.categories = categories;
      });
  }
  getProvinces() {
    this.provinceService.getProvinces()
      .then(provinces => {
        this.provinces = provinces;
      });
  }

  orderNow() {
    let provinces = [];
    let categories = [];

    for (var i=0;i<this.selectedProvinces.length;i++) {
      provinces.push({province: this.provinces[this.selectedProvinces[i]].idprovince,provinceDescription:this.provinces[this.selectedProvinces[i]].description});
    }
    for (var i=0;i<this.selectedCategories.length;i++) {
      categories.push({category: this.categories[this.selectedCategories[i]].idcategory,categoryDescription:this.categories[this.selectedCategories[i]].description});
    }
    console.log(this.selectedCategories.length);
    console.log(categories);
    let order = {
      type: 'product',
      product: this.products[this.selectedProduct].idproduct,
      productDescription: this.products[this.selectedProduct].description,
      categories: categories,
      totalAmount: this.products[this.selectedProduct].price,
      quantity: this.products[this.selectedProduct].quantity,
      provinces: provinces
    };

    console.log(order);

    sessionStorage.setItem('currentOrder', JSON.stringify(order));
    this.router.navigate(['/ordersummary']);

  }

  goBack(e) {
    e.preventDefault();
    this.location.back();
  }
}

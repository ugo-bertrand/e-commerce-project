import { createRouter, createWebHistory } from 'vue-router'
import Home from '../components/Home.vue'
import Login from '../components/UserLogin.vue'
import Signup from '../components/UserSignup.vue'
import UserInformation from '../components/UserInformation.vue'
import UserEditInformation from '../components/UserEditInformation.vue'
import ProductList from '../components/ProductList.vue'
import ProductDetail from '../components/ProductDetail.vue'
import OrderList from '../components/OrderList.vue'
import OrderDetail from '../components/OrderDetail.vue'
import EditProduct from '../components/EditProduct.vue'
import Cart from '../components/Cart.vue'
import AddProduct from '../components/AddProduct.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home
  },
  {
    path: '/login',
    name: 'login',
    component: Login
  },
  {
    path: '/signup',
    name: 'signup',
    component: Signup
  },
  {
    path: '/user',
    name: 'user',
    component: UserInformation
  },
  {
    path: '/user/edit',
    name: 'user-edit',
    component: UserEditInformation
  },
  {
    path: '/products',
    name: 'products',
    component: ProductList
  },
  {
    path: '/product/:id',
    name: 'productDetail',
    component: ProductDetail
  },
  {
    path: '/orders',
    name: 'orders',
    component: OrderList
  },
  {
    path: '/order/:id',
    name: 'orderDetail',
    component: OrderDetail
  },
  {
    path: '/product/edit/:id',
    name: 'editProduct',
    component: EditProduct
  }, 
  {
    path: '/cart',
    name: 'cart',
    component: Cart
  },
  {
    path: '/product/add',
    name: 'addProduct',
    component: AddProduct
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router

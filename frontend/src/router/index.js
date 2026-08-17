import { createRouter, createWebHistory } from 'vue-router'
import store from '../store'

import Home                from '../components/Home'
import Login               from '../components/Login'
import Register            from '../components/Register'
import ForgotPassword      from '../components/ForgotPassword'
import Dashboard           from '../components/Dashboard'
import BusinessDashboard   from '../components/BusinessDashboard'
import BusinessProducts    from '../components/BusinessProducts'
import PurchaseOrders      from '../components/PurchaseOrders'
import OutgoingOrders      from '../components/OutgoingOrders'
import NewProduct          from '../components/NewProduct'
import BusinessBatches     from '../components/BusinessBatches'
import NewSupplier         from '../components/NewSupplier'
import MySuppliers         from '../components/MySuppliers'
import NewBatch            from '../components/NewBatch'
import CreateOrder         from '../components/CreateOrder'
import ReceivedBatches     from '../components/ReceivedBatches'
import DReceivedBatches    from '../components/DReceivedBatches'
import AddProducts         from '../components/AddProducts'
import DProducts           from '../components/DProducts'
import CreateRetailerOrder from '../components/CreateRetailerOrder'
import RetailerOrders      from '../components/RetailerOrders'
import Shop                from '../components/Shop'
import RetailerOutgoingOrders from '../components/RetailerOutgoingOrders'
import SendMoney           from '../components/SendMoney'
import Wallet              from '../components/Wallet'
import ManufacturerInventory  from '../components/ManufacturerInventory'
import DistributorInventory   from '../components/DistributorInventory'
import RetailerInventory      from '../components/RetailerInventory'
import Message             from '../components/Message'
import RetailerSales       from '../components/RetailerSales'
import UserTransactions    from '../components/UserTransactions'
import EditProduct         from '../components/EditProduct'
import Account             from '../components/Account'
import CreateShop          from '../components/CreateShop'
import Manufacturers       from '../components/Manufacturers'
import Retailers           from '../components/Retailers'
import Distributors        from '../components/Distributors'
import ProductStock        from '../components/ProductStock'
import AddRetailerProduct  from '../components/AddRetailerProduct'
import RetailerProducts    from '../components/RetailerProducts'
import ManufacturerProducts   from '../components/ManufacturerProducts'
import DistributorProducts    from '../components/DistributorProducts'
import RetailerProductList    from '../components/RetailerProductList'
import Loans               from '../components/Loans'
import RetailerWebsiteOrders  from '../components/RetailerWebsiteOrders'
import MyShoppingOrders    from '../components/MyShoppingOrders'

// Routes that do NOT require authentication
const PUBLIC_ROUTES = ['home', 'login', 'register', 'forgot-password',
                       'manufacturers', 'retailers', 'distributors']

const routes = [
  { path: '/',          name: 'home',          component: Home },
  { path: '/login',     name: 'login',         component: Login },
  { path: '/register',  name: 'register',      component: Register },
  { path: '/forgot-password', name: 'forgot-password', component: ForgotPassword },

  // ── Authenticated: personal ────────────────────────────────────
  { path: '/dashboard',          name: 'dashboard',          component: Dashboard,        meta: { requiresAuth: true } },
  { path: '/account',            name: 'account',            component: Account,          meta: { requiresAuth: true } },
  { path: '/wallet',             name: 'wallet',             component: Wallet,           meta: { requiresAuth: true } },
  { path: '/send-money',         name: 'send-money',         component: SendMoney,        meta: { requiresAuth: true } },
  { path: '/user-transactions',  name: 'user-transactions',  component: UserTransactions, meta: { requiresAuth: true } },
  { path: '/loans',              name: 'loans',              component: Loans,            meta: { requiresAuth: true } },
  { path: '/shop',               name: 'shop',               component: Shop,             meta: { requiresAuth: true } },
  { path: '/my-shopping-orders', name: 'my-shopping-orders', component: MyShoppingOrders, meta: { requiresAuth: true } },
  { path: '/message',            name: 'message',            component: Message },

  // ── Authenticated: business ────────────────────────────────────
  { path: '/create-shop',          name: 'create-shop',        component: CreateShop,       meta: { requiresAuth: true } },
  { path: '/business-dashboard',   name: 'business-dashboard', component: BusinessDashboard,meta: { requiresAuth: true, requiresBusiness: true } },
  { path: '/business-products/:business_id',   name: 'business-products',   component: BusinessProducts,   meta: { requiresAuth: true } },
  { path: '/purchase-orders/:business_id',     name: 'purchase-orders',     component: PurchaseOrders,     meta: { requiresAuth: true } },
  { path: '/outgoing-orders/:business_id',     name: 'outgoing-orders',     component: OutgoingOrders,     meta: { requiresAuth: true } },
  { path: '/new-product/:business_id',         name: 'new-product',         component: NewProduct,         meta: { requiresAuth: true } },
  { path: '/business-batches/:business_id',    name: 'business-batches',    component: BusinessBatches,    meta: { requiresAuth: true } },
  { path: '/new-supplier/:business_id',        name: 'new-supplier',        component: NewSupplier,        meta: { requiresAuth: true } },
  { path: '/my-suppliers/:business_id',        name: 'my-suppliers',        component: MySuppliers,        meta: { requiresAuth: true } },
  { path: '/new-batch/:business_id',           name: 'new-batch',           component: NewBatch,           meta: { requiresAuth: true } },
  { path: '/create-order/:business_id',        name: 'create-order',        component: CreateOrder,        meta: { requiresAuth: true } },
  { path: '/d-received-batches/:business_id',  name: 'd-received-batches',  component: DReceivedBatches,   meta: { requiresAuth: true } },
  { path: '/received-batches/:business_id',    name: 'received-batches',    component: ReceivedBatches,    meta: { requiresAuth: true } },
  { path: '/add-products/:business_id',        name: 'add-products',        component: AddProducts,        meta: { requiresAuth: true } },
  { path: '/d-products/:business_id',          name: 'd-products',          component: DProducts,          meta: { requiresAuth: true } },
  { path: '/create-retailer-order/:business_id', name: 'create-retailer-order', component: CreateRetailerOrder, meta: { requiresAuth: true } },
  { path: '/retailer-orders/:business_id',     name: 'retailer-orders',     component: RetailerOrders,     meta: { requiresAuth: true } },
  { path: '/retailer-outgoing-orders/:business_id', name: 'retailer-outgoing-orders', component: RetailerOutgoingOrders, meta: { requiresAuth: true } },
  { path: '/manufacturer-inventory/:business_id', name: 'manufacturer-inventory', component: ManufacturerInventory, meta: { requiresAuth: true } },
  { path: '/distributor-inventory/:business_id',  name: 'distributor-inventory',  component: DistributorInventory,  meta: { requiresAuth: true } },
  { path: '/retailer-inventory/:business_id',     name: 'retailer-inventory',     component: RetailerInventory,     meta: { requiresAuth: true } },
  { path: '/retailer-sales',                   name: 'retailer-sales',      component: RetailerSales,      meta: { requiresAuth: true } },
  { path: '/edit-product/:product_id',         name: 'edit-product',        component: EditProduct,        meta: { requiresAuth: true } },
  { path: '/add-retailer-product/:business_id', name: 'add-retailer-product', component: AddRetailerProduct, meta: { requiresAuth: true } },
  { path: '/r-products/:business_id',          name: 'r-products',          component: RetailerProducts,   meta: { requiresAuth: true } },
  { path: '/manufacturer-products/:business_id', name: 'manufacturer-products', component: ManufacturerProducts, meta: { requiresAuth: true } },
  { path: '/distributor-products/:business_id',  name: 'distributor-products',  component: DistributorProducts,  meta: { requiresAuth: true } },
  { path: '/retailer-product-list/:business_id', name: 'retailer-product-list', component: RetailerProductList,  meta: { requiresAuth: true } },
  { path: '/product-stock/:product_id',        name: 'product-stock',       component: ProductStock,       meta: { requiresAuth: true } },
  { path: '/retailer-website-orders/:business_id', name: 'retailer-website-orders', component: RetailerWebsiteOrders, meta: { requiresAuth: true } },

  // ── Public browsing ────────────────────────────────────────────
  { path: '/manufacturers', name: 'manufacturers', component: Manufacturers },
  { path: '/retailers',     name: 'retailers',     component: Retailers },
  { path: '/distributors',  name: 'distributors',  component: Distributors },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  scrollBehavior() {
    return { top: 0, behavior: 'smooth' }
  },
})

// ── Global navigation guard ─────────────────────────────────────────────
router.beforeEach((to, _from, next) => {
  const loggedIn    = store.getters.isLoggedIn
  const isBusiness  = store.getters.isBusiness
  const hasShop     = store.getters.hasShop

  if (to.meta.requiresAuth && !loggedIn) {
    // Not logged in — redirect to login, then come back
    return next({ name: 'login', query: { redirect: to.fullPath } })
  }

  if (to.meta.requiresBusiness && (!isBusiness || !hasShop)) {
    // Tried to access business dashboard without a shop
    return next({ name: 'create-shop' })
  }

  next()
})

export default router

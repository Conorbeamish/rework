import { createRouter, createWebHistory } from 'vue-router'
import PlaceOrderView from '../views/PlaceOrderView.vue'
import StockOverviewView from '../views/StockOverviewView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'order',
      component: PlaceOrderView,
    },
    {
      path: '/stock',
      name: 'stock',
      component: StockOverviewView,
    },
  ],
})

export default router

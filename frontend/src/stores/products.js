import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useProductsStore = defineStore('products', () => {
  const products = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchProducts() {
    loading.value = true
    error.value = null
    try {
      const response = await fetch('http://localhost:8000/api/products')
      const data = await response.json()
      products.value = data.data
    } catch (e) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  async function placeOrder(productUuid, quantity) {
    error.value = null
    try {
      const response = await fetch('http://localhost:8000/api/orders', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          product_uuid: productUuid,
          quantity: parseInt(quantity),
        }),
      })
      const data = await response.json()
      if (!response.ok) {
        throw new Error(data.message || 'Failed to place order')
      }
      await fetchProducts()
      return data
    } catch (e) {
      error.value = e.message
      throw e
    }
  }

  return { products, loading, error, fetchProducts, placeOrder }
})

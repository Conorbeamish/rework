<script setup>
import { ref, onMounted, computed } from 'vue'
import { useProductsStore } from '@/stores/products'

const store = useProductsStore()
const selectedProduct = ref('')
const quantity = ref(1)
const message = ref('')
const messageType = ref('')
const isSubmitting = ref(false)

const selectedProductData = computed(() => {
  return store.products.find(p => p.uuid === selectedProduct.value)
})

const orderTotal = computed(() => {
  if (!selectedProductData.value) return 0
  return (parseFloat(selectedProductData.value.price) * quantity.value).toFixed(2)
})

onMounted(() => {
  store.fetchProducts()
})

async function submitOrder() {
  if (!selectedProduct.value) {
    message.value = 'Please select a product'
    messageType.value = 'error'
    return
  }
  if (quantity.value < 1) {
    message.value = 'Quantity must be at least 1'
    messageType.value = 'error'
    return
  }
  
  isSubmitting.value = true
  try {
    const result = await store.placeOrder(selectedProduct.value, quantity.value)
    message.value = `Order placed successfully! Order ID: ${result.data.order_uuid}`
    messageType.value = 'success'
    selectedProduct.value = ''
    quantity.value = 1
  } catch (e) {
    message.value = e.message
    messageType.value = 'error'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="place-order">
    <div class="page-header">
      <h1>Place Order</h1>
      <p class="subtitle">Create a new order from available inventory</p>
    </div>

    <div class="order-card">
      <div v-if="message" :class="['message', messageType]">
        <span class="message-icon">{{ messageType === 'success' ? '✓' : '⚠' }}</span>
        {{ message }}
      </div>

      <div v-if="store.loading" class="loading">
        <div class="spinner"></div>
        <span>Loading products...</span>
      </div>
      
      <form v-else @submit.prevent="submitOrder">
        <div class="form-group">
          <label for="product">
            <span class="label-icon">📦</span>
            Select Product
          </label>
          <select id="product" v-model="selectedProduct">
            <option value="">Choose a product...</option>
            <option 
              v-for="product in store.products" 
              :key="product.uuid" 
              :value="product.uuid"
              :disabled="product.immediate_despatch < 1"
            >
              {{ product.title }} - £{{ parseFloat(product.price).toFixed(2) }} ({{ product.immediate_despatch }} available)
            </option>
          </select>
        </div>

        <div class="form-group">
          <label for="quantity">
            <span class="label-icon">🔢</span>
            Quantity
          </label>
          <input 
            id="quantity" 
            type="number" 
            v-model="quantity" 
            min="1"
            :max="selectedProductData?.immediate_despatch || 999"
          />
        </div>

        <div v-if="selectedProductData" class="order-summary">
          <div class="summary-row">
            <span>Product:</span>
            <span>{{ selectedProductData.title }}</span>
          </div>
          <div class="summary-row">
            <span>Unit Price:</span>
            <span>£{{ parseFloat(selectedProductData.price).toFixed(2) }}</span>
          </div>
          <div class="summary-row">
            <span>Quantity:</span>
            <span>{{ quantity }}</span>
          </div>
          <div class="summary-row total">
            <span>Total:</span>
            <span>£{{ orderTotal }}</span>
          </div>
        </div>

        <button type="submit" :disabled="isSubmitting || !selectedProduct">
          <span v-if="isSubmitting" class="btn-spinner"></span>
          {{ isSubmitting ? 'Processing...' : 'Place Order' }}
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.place-order {
  max-width: 500px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 25px;
}

h1 {
  font-size: 28px;
  color: #1e3a5f;
  margin-bottom: 5px;
}

.subtitle {
  color: #666;
  font-size: 15px;
}

.order-card {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  padding: 40px;
  color: #666;
}

.spinner {
  width: 24px;
  height: 24px;
  border: 3px solid #e0e0e0;
  border-top-color: #1e3a5f;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
  font-weight: 600;
  color: #374151;
  font-size: 14px;
}

.label-icon {
  font-size: 16px;
}

select, input {
  width: 100%;
  padding: 12px 14px;
  font-size: 15px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  background: white;
}

select:focus, input:focus {
  outline: none;
  border-color: #1e3a5f;
  box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
}

select {
  cursor: pointer;
}

.order-summary {
  background: #f8fafc;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 20px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  font-size: 14px;
  color: #4b5563;
}

.summary-row:not(:last-child) {
  border-bottom: 1px solid #e5e7eb;
}

.summary-row.total {
  font-weight: 700;
  font-size: 18px;
  color: #1e3a5f;
  padding-top: 12px;
  border-bottom: none;
}

button {
  width: 100%;
  padding: 14px 20px;
  font-size: 16px;
  font-weight: 600;
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

button:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}

.btn-spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.message {
  padding: 14px 16px;
  margin-bottom: 20px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
}

.message-icon {
  font-size: 18px;
}

.message.success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.message.error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}
</style>

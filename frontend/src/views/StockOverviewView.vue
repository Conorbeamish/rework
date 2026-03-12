<script setup>
import { onMounted, ref } from 'vue'
import { useProductsStore } from '@/stores/products'

const store = useProductsStore()
const expandedProduct = ref(null)

onMounted(() => {
  store.fetchProducts()
})

function toggleExpand(uuid) {
  expandedProduct.value = expandedProduct.value === uuid ? null : uuid
}
</script>

<template>
  <div class="stock-overview">
    <div class="page-header">
      <h1>Stock Overview</h1>
      <p class="subtitle">View inventory levels across all warehouses</p>
    </div>

    <div v-if="store.loading" class="loading">
      <div class="spinner"></div>
      <span>Loading inventory data...</span>
    </div>
    
    <div v-else-if="store.error" class="error-message">
      <span class="error-icon">⚠️</span>
      {{ store.error }}
    </div>
    
    <div v-else class="table-container">
      <table>
        <thead>
          <tr>
            <th class="expand-col"></th>
            <th>Product</th>
            <th>Price</th>
            <th class="number-col">Total Qty</th>
            <th class="number-col">Allocated</th>
            <th class="number-col">Physical Qty</th>
            <th class="number-col">Threshold</th>
            <th class="number-col highlight-col">Available</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="product in store.products" :key="product.uuid">
            <tr @click="toggleExpand(product.uuid)" class="product-row">
              <td class="expand-cell">
                <span class="expand-icon" :class="{ expanded: expandedProduct === product.uuid }">▶</span>
              </td>
              <td class="product-name">{{ product.title }}</td>
              <td class="price">£{{ parseFloat(product.price).toFixed(2) }}</td>
              <td class="number-col">{{ product.warehouse_stock.reduce((sum, s) => sum + s.quantity, 0) }}</td>
              <td class="number-col allocated">{{ product.allocated_to_orders }}</td>
              <td class="number-col">{{ product.physical_quantity }}</td>
              <td class="number-col threshold">{{ product.total_threshold }}</td>
              <td class="number-col available" :class="{ low: product.immediate_despatch < 20 }">
                {{ product.immediate_despatch }}
              </td>
            </tr>
            <tr v-if="expandedProduct === product.uuid" class="warehouse-row">
              <td colspan="8">
                <div class="warehouse-details">
                  <h4>Warehouse Breakdown</h4>
                  <table class="warehouse-table">
                    <thead>
                      <tr>
                        <th>Warehouse</th>
                        <th>Quantity</th>
                        <th>Threshold</th>
                        <th>Available</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="stock in product.warehouse_stock" :key="stock.warehouse_uuid">
                        <td>
                          <span class="warehouse-icon">🏭</span>
                          {{ stock.warehouse_name }}
                        </td>
                        <td>{{ stock.quantity }}</td>
                        <td>{{ stock.threshold }}</td>
                        <td class="available">{{ stock.quantity - stock.threshold }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.stock-overview {
  padding: 0;
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

.loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  padding: 60px;
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

.error-message {
  background: #fee2e2;
  color: #991b1b;
  padding: 15px 20px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
  color: white;
  padding: 14px 16px;
  text-align: left;
  font-weight: 500;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

td {
  padding: 14px 16px;
  border-bottom: 1px solid #eee;
}

.expand-col {
  width: 40px;
}

.expand-cell {
  text-align: center;
}

.expand-icon {
  display: inline-block;
  color: #666;
  transition: transform 0.2s ease;
  font-size: 10px;
}

.expand-icon.expanded {
  transform: rotate(90deg);
}

.product-row {
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.product-row:hover {
  background-color: #f8fafc;
}

.product-name {
  font-weight: 500;
  color: #333;
}

.price {
  color: #1e3a5f;
  font-weight: 600;
}

.number-col {
  text-align: center;
  font-family: 'Monaco', 'Consolas', monospace;
}

.allocated {
  color: #b45309;
  background: #fef3c7;
  border-radius: 4px;
}

.threshold {
  color: #6b7280;
}

.available {
  color: #059669;
  font-weight: 600;
  background: #d1fae5;
  border-radius: 4px;
}

.available.low {
  color: #dc2626;
  background: #fee2e2;
}

.highlight-col {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
}

.warehouse-row td {
  padding: 0;
  background: #f8fafc;
}

.warehouse-details {
  padding: 20px 30px;
}

.warehouse-details h4 {
  color: #1e3a5f;
  margin-bottom: 12px;
  font-size: 14px;
  font-weight: 600;
}

.warehouse-table {
  width: 100%;
  max-width: 500px;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.warehouse-table th {
  background: #e2e8f0;
  color: #475569;
  padding: 10px 14px;
  font-size: 12px;
}

.warehouse-table td {
  padding: 10px 14px;
  font-size: 14px;
}

.warehouse-icon {
  margin-right: 8px;
}
</style>

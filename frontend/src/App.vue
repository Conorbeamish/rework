<script setup>
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
  <header>
    <div class="header-content">
      <div class="logo">
        <span class="logo-icon">📦</span>
        <span class="logo-text">Warehouse Manager</span>
      </div>
      <nav>
        <RouterLink to="/" class="nav-link">
          <span class="nav-icon">🛒</span>
          Place Order
        </RouterLink>
        <RouterLink to="/stock" class="nav-link">
          <span class="nav-icon">📊</span>
          Stock Overview
        </RouterLink>
      </nav>
    </div>
  </header>

  <main>
    <RouterView />
  </main>
</template>

<style scoped>
header {
  background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
  padding: 0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
}

.logo-icon {
  font-size: 28px;
}

.logo-text {
  color: white;
  font-size: 22px;
  font-weight: 600;
  letter-spacing: 0.5px;
}

nav {
  display: flex;
  gap: 8px;
}

.nav-link {
  color: rgba(255, 255, 255, 0.9);
  text-decoration: none;
  font-size: 15px;
  padding: 10px 20px;
  border-radius: 8px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.1);
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

.nav-link.router-link-exact-active {
  background: rgba(255, 255, 255, 0.25);
  color: white;
  font-weight: 500;
}

.nav-icon {
  font-size: 16px;
}

main {
  max-width: 1400px;
  margin: 0 auto;
  padding: 30px;
}
</style>

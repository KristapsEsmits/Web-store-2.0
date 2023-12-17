<template>
  <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
    <router-link to="/admin">
      <button class="btn">Home</button>
    </router-link>
    <router-link to="/test">
      <button class="btn">Test</button>
    </router-link>
    <router-link to="/admin/brands">
      <button class="btn">Brands</button>
    </router-link>
    <router-link to="/admin/items">
      <button class="btn">Items</button>
    </router-link>
  </div>
</template>

<style scoped>
.nav {
  position: fixed;
  top: 59.7px;
  left: 0;
  height: 100%;
  background-color: #f8f9fa; /* Add your preferred background color */
  padding-top: 20px; /* Add padding as needed */
}

.btn{
  outline: none !important;
    border: none !important;        
    text-decoration: none !important;
    box-shadow: none !important;
}
</style>

<template>
  <div class="skeleton-card"></div>
</template>

<style scoped>
.skeleton-card {
  width: 280px;
  height: 150px;
  background: #f5f5f5;
  border-radius: 4px;
  position: relative;
  overflow: hidden;
  margin-bottom: 20px;
}

.skeleton-card::after {
  content: '';
  display: block;
  position: absolute;
  top: 0;
  left: -150px;
  height: 100%;
  width: 150px;
  background: linear-gradient(to right, transparent 0%, #f0f0f0 50%, transparent 100%);
  animation: loading 1.5s infinite;
}

@keyframes loading {
  0% {
    left: -150px;
  }
  50% {
    left: 100%;
  }
  100% {
    left: 100%;
  }
}
</style>
  
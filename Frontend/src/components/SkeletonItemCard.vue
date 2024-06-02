<template>
  <div class="skeleton-card">
    <div class="skeleton-img"></div>
    <div class="skeleton-pill"></div>
    <div class="skeleton-text"></div>
    <div class="skeleton-price"></div>
    <div class="skeleton-btn"></div>
  </div>
</template>

<style scoped>
@keyframes shimmer {
  0% {
    background-position: -200px 0;
  }
  100% {
    background-position: 200px 0;
  }
}

.skeleton-card {
  overflow: hidden;
  width: 100%;
  height: 340px;
  background: #eeeeee;
  border-radius: 4px;
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 10px;
  border: 1px solid #ccc;
}

.skeleton-img,
.skeleton-pill,
.skeleton-text,
.skeleton-price,
.skeleton-btn {
  background: linear-gradient(90deg, #f9f9f9 25%, #f0f0f0 50%, #f9f9f9 75%);
  background-size: 400% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 4px;
}

.skeleton-img {
  height: 160px;
  margin: 5px;
}

.skeleton-pill {
  height: 20px;
  width: 40%;
  margin-top: -5px;
}

.skeleton-text {
  height: 50px;
  margin-top: -10px;
}

.skeleton-price {
  height: 20px;
  width: 60%;
  margin-top: -10px;
}

.skeleton-btn {
  height: 30px;
  width: 40%;
  margin: 0 auto;
}
</style>
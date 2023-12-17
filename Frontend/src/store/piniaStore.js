import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    isLoggedIn: false,
  }),
  actions: {
    setLoggedIn(isLoggedIn) {
      this.isLoggedIn = isLoggedIn;
    },
  },
});

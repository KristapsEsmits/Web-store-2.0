<template>
    <div class="container">
      <div class="card">
        <div class="card-header">
          <h4>Login</h4>
        </div>
        <div class="card-body">
            <!-- <ul class="alert alert-danger" v-if="Object.keys(errorList).length > 0">
                <li class="mb-0 ms-3" v-for="(error, index) in errorList" :key="index">
                {{ error[0] }}
                </li>
            </ul>  -->
            <form>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" v-model="user.email" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" v-model="user.password" placeholder="Password">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input">
                <label class="form-check-label" for="rememberMe">Remember Me</label>
            </div>
            <button type="button" @click="loginUser" class="btn btn-primary">Login</button>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        user: {
          email: '',
          password: '',
        },
      };
    },
    methods: {
      loginUser() {
        axios.post('http://127.0.0.1:8000/api/login', this.user)
          .then((response) => {
            
          })
          .catch((error) => {
           
          });
      },
    },
  };
  </script>
  
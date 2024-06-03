<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      errorList: {},
      successMessage: '',
      model: {
        name: '',
        surname: '',
        register: {
          password_confirmation: '',
          password: '',
          phone: '',
          email: '',
        },
      },
    };
  },

  methods: {
    saveRegister() {
      // Hashing password before sending to backend
      const hashedPassword = this.model.register.password;
      axios.post('/register', {
        ...this.model.register,
        password: hashedPassword,
      })
          .then((res) => {
            this.successMessage = res.data.message;

            this.model.register.name = '';
            this.model.register.surname = '';
            this.model.register.password = '';
            this.model.register.password_confirmation = '';
            this.model.register.phone = '';
            this.model.register.email = '';

            this.errorList = {};
          })

          .catch((error) => {
            if (error.response) {
              console.log(error.response.data);
              this.errorList = error.response.data.errors;
            } else if (error.request) {
              console.log(error.request);
            } else {
              console.log('Error', error.message);
            }
          });
    },
  },
};
</script>

<template>
  <div class="attribute-container">
    <div class="logo">
      <img alt="Logo" src="\favicon.ico" style="height: 60px; width: 60px;">
    </div>
    <div class="logoText">
      <h1>Create an Account</h1>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-body">
        <div v-if="successMessage" class="alert alert-success">
          {{ successMessage }}
        </div>
        <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
          <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
            {{ error[0] }}
          </li>
        </ul>
        <form>
          <div class="form-floating mb-3">
            <input v-model="model.register.name" class="form-control" placeholder="Name" type="text">
            <label for="Name">Name</label>
          </div>
          <div class="form-floating mb-3">
            <input v-model="model.register.surname" class="form-control" placeholder="Surname" type="text">
            <label for="Surname">Surname</label>
          </div>
          <div class="form-floating mb-3">
            <input v-model="model.register.password" class="form-control" placeholder="Password" type="password">
            <label for="Password">Password</label>
          </div>
          <div class="form-floating mb-3">
            <input v-model="model.register.password_confirmation" class="form-control" placeholder="Repeat Password"
                   type="password">
            <label for="Repeat_password">Repeat password</label>
          </div>
          <div class="form-floating mb-3">
            <input v-model="model.register.phone" class="form-control" placeholder="Phone" type="phone">
            <label for="Phone">Phone</label>
          </div>
          <div class="form-floating mb-3">
            <input v-model="model.register.email" class="form-control" placeholder="Enter email" type="email">
            <label for="Email">Email address</label>
          </div>
          <button class="btn btn-primary registerbtn" type="button" @click="saveRegister">Register</button>
        </form>
      </div>
    </div>
    <div class="Member">
      <p>Already a member?
        <RouterLink to="/login">Login</RouterLink>
      </p>
    </div>
  </div>
</template>

<style scoped>
.logo, .logoText {
  display: flex;
  justify-content: center;
}

.card {
  display: flex;
  justify-content: center;
  width: 380px;
  margin: 0 auto;
}

.registerbtn {
  display: flex;
  justify-content: center;
  width: 100%;
  height: 40px;
}

.form-control:focus {
  border-color: #b3b3b3;
  box-shadow: none;
}

.Member {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

@media screen and (max-width: 375px) {
  .card {
    width: 300px;
  }
}
</style>
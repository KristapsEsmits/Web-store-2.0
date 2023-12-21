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
                console.log(res.data);
                this.successMessage = res.data.message; 
                // this.$router.push('/login');

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
                    // Handle response error (e.g., HTTP status 422)
                    console.log(error.response.data);
                    this.errorList = error.response.data.errors;
                } else if (error.request) {
                    // Handle network error (e.g., server not reachable)
                    console.log(error.request);
                } else {
                    // Handle other errors
                    console.log('Error', error.message);
                }
            });
        },
    },
};
</script>

<template>
    <div class="container">
        <div class="logo">
            <img src="\favicon.ico" alt="Logo" style="height: 60px; width: 60px;">
        </div>
        <div class="logoText">
            <h1>Create an Account</h1>  
        </div>
        <div class="card">
             <div class="card-body">
                <div v-if="successMessage" class="alert alert-success">
                    {{ successMessage }}
                </div>
                <ul class="alert alert-danger" v-if="Object.keys(errorList).length > 0">
                    <li class="mb-0 ms-3" v-for="(error, index) in errorList" :key="index">
                    {{ error[0] }}
                    </li>
                </ul> 
                <form>
                    <div class="form-floating mb-3">
                        <input type="text" v-model="model.register.name" class="form-control" placeholder="Name">
                        <label for="Name">Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" v-model="model.register.surname" class="form-control" placeholder="Surname">
                        <label for="Surname">Surname</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" v-model="model.register.password" class="form-control" placeholder="Password">
                        <label for="Password">Password</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" v-model="model.register.password_confirmation" class="form-control" placeholder="Repeat Password">
                        <label for="Repeat_password">Repeat password</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="phone" v-model="model.register.phone" class="form-control" placeholder="Phone">
                        <label for="Phone">Phone</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" v-model="model.register.email" class="form-control" placeholder="Enter email">
                        <label for="Email">Email address</label>
                    </div>
                        <button type="button" @click="saveRegister" class="btn btn-primary registerbtn">Register</button>
                    </form>
             </div>
        </div>
        <div class="Member">
            <p>Already a member? <RouterLink to="/login">Login</RouterLink></p>
        </div>
    </div>
</template>

<style scoped>
    .logo, .logoText{
        display: flex;
        justify-content: center;
    }

    .container {
        margin-top: 20px;
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

    .Member {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
</style>
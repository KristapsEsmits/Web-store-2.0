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
        <div class="card">
            <div class="card-header">
                <h4>Register</h4>
             </div>
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
                    <div class="mb-3">
                        <label for="Name">Name</label>
                        <input type="text" v-model="model.register.name" class="form-control" placeholder="Name">
                    </div>
                    <div class="mb-3">
                        <label for="Surname">Surname</label>
                        <input type="text" v-model="model.register.surname" class="form-control" placeholder="Surname">
                    </div>
                    <div class="mb-3">
                        <label for="Password">Password</label>
                        <input type="password" v-model="model.register.password" class="form-control" placeholder="Password">
                    </div>
                    <div class="mb-3">
                        <label for="Repeat_password">Repeat password</label>
                        <input type="password" v-model="model.register.password_confirmation" class="form-control" placeholder="Repeat Password">
                    </div>
                    <div class="mb-3">
                        <label for="Phone">Phone</label>
                        <input type="phone" v-model="model.register.phone" class="form-control" placeholder="Phone">
                    </div>
                    <div class="mb-3">
                        <label for="Email">Email address</label>
                        <input type="email" v-model="model.register.email" class="form-control" placeholder="Enter email">
                    </div>
                        <button type="button" @click="saveRegister" class="btn btn-primary">Register</button>
                    </form>
             </div>
        </div>
    </div>
</template>
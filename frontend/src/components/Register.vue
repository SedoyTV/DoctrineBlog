<template>
    <div>
        <header class="header">
            <div class="container">
                <div class="row justify-content-between align-items-center">
                    <div class="col d-flex align-items-center">
                        <h1>Мой блог</h1>
                    </div>
                    <div class="col text-center">
                        <img src="../../public/2.png" alt="" class="logo">
                    </div>
                    <div class="col text-end d-flex justify-content-end align-items-center">

                        <div class="d-flex align-items-center">
                            <button class="custom-btn custom-btn-secondary" @click="goToPosts">Посты</button>
                            <button class="custom-btn custom-btn-danger" @click="goToLogin">Войти</button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="container mt-5">
            <div class="row justify-content-center mt-5">
                <div class="col-md-6">
                    <div class="form-body shadow">
                            <h1 class="form-title text-center mb-4">Регистрация</h1>
                            <form @submit.prevent="register">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Имя</label>
                                    <input type="text" class="form-control" v-model="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" v-model="email" required
                                           autocomplete="username">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label register-password">Пароль</label>
                                    <input type="password" class="form-control" v-model="password" required
                                           autocomplete="current-password">
                                </div>
                                <button type="submit" class="custom-btn custom-btn-primary w-100">Зарегистрироваться
                                </button>
                            </form>

                    </div>
                </div>
            </div>
        </div>
        <div class="bubble-container"></div>
    </div>
</template>

<script>
import axios from 'axios';
import { createBubbles } from '../js/bubble.js';

export default {
    mounted() {
        createBubbles(this.$el);
    },
    data() {
        return {
            name: '',
            email: '',
            password: ''
        };
    },
    methods: {
        async register() {
            try {
                const response = await axios.post('/api/register', {
                    name: this.name,
                    email: this.email,
                    password: this.password
                });
                localStorage.setItem('token', response.data.token);
                localStorage.setItem('user', JSON.stringify(response.data.user));
                this.$router.push('/');
            } catch (error) {
                alert('Registration failed');
                console.error(error.response.data);
            }
        },

        goToLogin() {
            this.$router.push('/login');
        },
        goToPosts() {
            this.$router.push('/');
        },

    },

};
</script>
<style scoped>
@import "../styles/button.css";
@import "../styles/form.css";
</style>

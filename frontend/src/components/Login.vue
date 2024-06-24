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
                            <button class="custom-btn custom-btn-danger" @click="goToRegister">Регистрация</button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="form-body shadow">
                            <h1 class="form-title text-center mb-4">Логин</h1>
                            <form @submit.prevent="login">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" v-model="email" required autocomplete="username">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Пароль</label>
                                    <input type="password" class="form-control" v-model="password" required autocomplete="current-password">
                                </div>
                                <button type="submit" class="custom-btn custom-btn-primary w-100">Войти</button>
                            </form>
                            <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
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
            email: '',
            password: '',
            user: null,
            error: null
        };
    },
    methods: {
        async login() {
            this.error = null;
            try {
                const response = await axios.post('/api/login', {
                    email: this.email,
                    password: this.password
                });
                console.log('Login successful:', response.data.user);
                localStorage.setItem('token', response.data.token);
                localStorage.setItem('user', JSON.stringify(response.data.user));
                this.$router.push('/');
            } catch (error) {
                console.error('Login error:', error);
                console.log('Error response:', error.response);
                if (error.response && error.response.status === 401) {
                    this.error = 'Неверные данные для входа. Пожалуйста, попробуйте снова.';
                } else {
                    this.error = 'Произошла ошибка. Пожалуйста, попробуйте снова позже.';
                }
            }
        },
        goToPosts() {
            this.$router.push('/');
        },
        goToRegister() {
            this.$router.push('/register');
        }
    },

};
</script>

<style scoped>
@import "../styles/button.css";
@import "../styles/form.css";
</style>

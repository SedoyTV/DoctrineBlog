import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import Posts from '../components/Posts.vue';
import Categories from "../components/Categories.vue";
import PostDetail from "../components/PostDetail.vue";

const routes = [
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/', component: Posts },
    { path: '/categories', component: Categories },
    { path: '/post/:id', name: 'PostDetail' , component: PostDetail},
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;

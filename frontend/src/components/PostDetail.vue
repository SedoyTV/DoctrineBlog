<template>
    <div>
        <header class="header">
            <div class="container">
                <div class="row justify-content-between align-items-center">
                    <div class="col d-flex align-items-center">
                        <h1>Мой блог</h1>
                        <span v-if="user" class="user-name ms-3">{{ user.name }}</span>
                    </div>
                    <div class="col text-center">
                        <img src="../../public/2.png" alt="" class="logo" @click="goToPosts">
                    </div>
                    <div class="col text-end d-flex justify-content-end align-items-center">
                        <div v-if="user" class="d-flex align-items-center">
                            <button class="custom-btn custom-btn-secondary" @click="goToCategories">Категории</button>
                            <button class="custom-btn custom-btn-danger" @click="logout">Выйти</button>
                        </div>
                        <div v-else class="d-flex align-items-center">
                            <button class="custom-btn custom-btn-secondary" @click="goToLogin">Войти</button>
                            <button class="custom-btn custom-btn-danger" @click="goToRegister">Регистрация</button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="container mt-5" v-if="post">
            <div class="row justify-content-center mt-5">
                <div class="col-md-12 ">
                    <h1 class="main-title text-center mb-4">Пост №{{post.id}}</h1>
                    <div class="post-item">
                        <div class="main-body">
                            <h1 class="post-title text-center mb-4">{{ post.title }}</h1>
                            <hr>
                            <div v-if="post.image" class="post-image-container">
                                <a :href="post.image" target="_blank">
                                    <img :src="post.image" alt="Post Image" class="post-image">
                                </a>
                            </div>
                            <hr>
                            <p class="post-description">{{ post.description }}</p>
                            <hr>
                            <div class="d-flex justify-content-between mt-4">
                                <p class="post-meta">Категория: {{ getCategoryNames(post.categories) }}</p>
                                <p class="post-meta">Автор: {{ post.user.name }}</p>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-center mt-4" v-if="user && canEditOrDelete(post)">
                                <button class="custom-btn custom-btn-primary btn-ed-del" @click="editPost(post)">
                                    <i class="fas fa-edit"></i> Изменить
                                </button>
                                <button class="custom-btn custom-btn-primary btn-ed-del" @click="deletePost(post.id)">
                                    <i class="fas fa-trash"></i> Удалить
                                </button>
                            </div>
                            <router-link to="/" class="custom-btn custom-btn-danger mt-3 d-block mx-auto btn-ed-del">
                                <i class="fas fa-arrow-left"></i> К постам
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="text-center mt-5">
            <p>Загрузка...</p>
        </div>

        <div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header justify-content-center">
                        <h5 class="form-title w-100 text-center" id="postModalLabel">Редактировать пост</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="form-body">
                        <form @submit.prevent="updatePost()" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Название</label>
                                <input type="text" class="form-control" v-model="postForm.title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Описание</label>
                                <textarea class="form-control" v-model="postForm.description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="categories" class="form-label">Категории</label>
                                <div v-for="category in categories" :key="category.id">
                                    <input type="checkbox" :value="category.id" v-model="postForm.category_ids" class="category-checkbox">
                                    <label>{{ category.title }}</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Изображение</label>
                                <input type="file" class="form-control" @change="handleImageUpload">
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="custom-btn custom-btn-primary">Изменить</button>
                            </div>
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
            post: null,
            user: null,
            postForm: {
                id: null,
                title: '',
                description: '',
                category_ids: [],
                image: null
            },
            categories: [],
            isEditing: false
        };
    },
    async created() {
        this.loadUser();
        await this.fetchPost();
        await this.fetchCategories();
    },
    methods: {
        loadUser() {
            const userData = localStorage.getItem('user');
            if (userData) {
                this.user = JSON.parse(userData);
            }
        },
        async fetchPost() {
            const postId = this.$route.params.id;
            try {
                const token = localStorage.getItem('token');
                const response = await axios.get(`/api/posts/${postId}`, {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });

                this.post = response.data;
                this.post.image = this.post.image ? `/storage/${this.post.image}` : null;
            } catch (error) {
                console.error('Error fetching post:', error);
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get('/api/categories');
                if (!response || !response.data) {
                    throw new Error('Invalid response from server when fetching categories');
                }
                this.categories = response.data;
            } catch (error) {
                alert('Ошибка при получении категорий');
            }
        },
        editPost(post) {
            this.isEditing = true;
            this.postForm = {
                id: post.id,
                title: post.title,
                description: post.description,
                category_ids: post.categories.map(category => category.id),
                image: null
            };
            new window.bootstrap.Modal(document.getElementById('postModal')).show();
            document.body.classList.add('modal-open');
        },
        handleImageUpload(event) {
            const file = event.target.files[0];
            this.postForm.image = file;
        },
        async updatePost() {
            try {
                const token = localStorage.getItem('token');
                const formData = new FormData();
                formData.append('title', this.postForm.title);
                formData.append('description', this.postForm.description);
                this.postForm.category_ids.forEach(id => formData.append('category_ids[]', id));
                if (this.postForm.image) {
                    formData.append('image', this.postForm.image);
                }

                const response = await axios.post(`/api/posts/${this.postForm.id}`, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data',
                        'X-HTTP-Method-Override': 'PUT'
                    }
                });
                this.post = response.data;
                this.post.image = this.post.image ? `/storage/${this.post.image}` : null;
                this.closeModal();
            } catch (error) {
                alert('Ошибка при обновлении поста');
            }
        },
        async deletePost(id) {
            try {
                const token = localStorage.getItem('token');
                await axios.delete(`/api/posts/${id}`, {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });
                this.$router.push('/');
            } catch (error) {
                alert('Ошибка при удалении поста');
            }
        },
        canEditOrDelete(post) {
            return this.user && post.user.id && post.user.id === this.user.id;
        },
        closeModal() {
            const modalElement = document.getElementById('postModal');
            const modalInstance = window.bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
                document.body.classList.remove('modal-open');
            }
        },
        goToPosts() {
            this.$router.push('/');
        },
        goToCategories() {
            this.$router.push('/categories');
        },
        goToLogin() {
            this.$router.push('/login');
        },
        goToRegister() {
            this.$router.push('/register');
        },
        logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            this.user = null;
            this.$router.push('/login');
        },
        getCategoryNames(categories) {
            return categories.map(category => category.title).join(', ');
        }
    },

};
</script>

<style scoped>
@import "../styles/button.css";
@import "../styles/form.css";
@import "../styles/username.css";

.btn-ed-del i{
    margin-right: 5px;
    padding: 0 5px;
}

.btn-ed-del:hover i{
    animation: icon-bounce 0.5s;
}

@keyframes icon-bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

.main-title {
    font-size: 60px;
    color: #343a40;
}

.post-item {
    background: linear-gradient(90deg, rgb(255, 255, 255), #d3d3d3,rgb(216, 216, 216));
    color: #000000;
    border: none;
    padding: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 10px;
    box-shadow: 15px 15px 15px #434343;
    margin: 7% 0;
    overflow: hidden;
    box-sizing: border-box;
    text-overflow: ellipsis;
    white-space: nowrap;
    word-break: break-word;
}
.post-item hr {
    height: 3px;
    background-color: #ffffff;
    border: none;
    margin: 15px 0;
}
.post-item:hover {
    background-color: rgba(0, 0, 0, 0.92);
    transform: translateY(-5px);
    box-shadow: 20px 20px 20px #1e1e1e;
}

.post-title {
    font-weight: bold;
    font-size: 40px;
    color: #000000;

}

.post-description {
    font-size: 20px;
    color: #000000;
    font-style: italic;
    text-align: center;
}

.post-title, .post-description {
    margin-bottom: 10px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal;
    word-wrap: break-word;
}

.post-meta {
    font-size: 16px;
    font-weight: bold;
    max-width: 50%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    word-break: break-word;
    color: #000000;
}
.post-image-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
    padding: 0 15px;
}

.post-image {
    max-width: 100%;
    max-height: 700px;
    width: auto;
    height: auto;
    border-radius: 10px;
    transition: 1.5s;
}
.category-checkbox {
    accent-color: black;
    margin-right: 5px;
}
</style>

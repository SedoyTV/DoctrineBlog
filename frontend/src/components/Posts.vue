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
        <div class="container mt-5">
            <div class="row justify-content-center mt-5">
                <div class="col-md-12" >
                    <div class="main-body">
                        <h1 class="main-title text-center mb-4">Посты</h1>
                        <div v-if="posts.length === 0">
                            <p>Постов нет.</p>
                        </div>
                        <div v-else>
                            <ul class="post-group">
                                <li class="post-item" v-for="post in paginatedPosts" :key="post.id">
                                    <router-link v-if="user" :to="{ name: 'PostDetail', params: { id: post.id } }" class="post-link">
                                        <h5 class="post-title mb-1 text-center">{{ post.title }}</h5>
                                        <hr>
                                        <p class="post-description mb-1 text-center">{{ post.description }}</p>
                                        <div v-if="post.image" class="post-image-container">
                                            <img :src="post.image" alt="Post Image" class="post-image">
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <p class="post-meta">Категория: {{ getCategoryNames(post.categories) }}</p>
                                            <p class="post-meta">Автор: {{ post.user.name }}</p>
                                        </div>
                                    </router-link>
                                    <template v-else>
                                        <h5 class="post-title mb-1 text-center">{{ post.title }}</h5>
                                        <hr>
                                        <p class="post-description mb-1 text-center">{{ post.description }}</p>
                                        <div v-if="post.image" class="post-image-container">
                                            <img :src="post.image" alt="Post Image" class="post-image">
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <p class="post-meta">Категория: {{ getCategoryNames(post.categories) }}</p>
                                            <p class="post-meta">Автор: {{ post.user.name }}</p>
                                        </div>
                                    </template>
                                </li>
                            </ul>
                        </div>
                        <button class="custom-btn custom-btn-primary mt-3 mx-auto d-block" @click="createPost" v-if="user">Создать пост</button>
                    </div>
                    <nav aria-label="Page navigation example" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">Предыдущая</a>
                            </li>
                            <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
                                <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                            </li>
                            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">Следующая</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header justify-content-center">
                            <h5 class="form-title w-100 text-center" id="postModalLabel">Создать пост</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="form-body">
                            <form @submit.prevent="storePost()" enctype="multipart/form-data">
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
                                        <input type="checkbox" :value="category.id" v-model="postForm.category_ids" class="post-category-group">
                                        <label class="post-category-item" style="font-style: italic;">{{ category.title }}</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Изображение</label>
                                    <input type="file" class="form-control form-label" @change="handleImageUpload">
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="custom-btn custom-btn-primary">Сохранить</button>
                                </div>
                            </form>
                        </div>
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
            posts: [],
            categories: [],
            postForm: {
                id: null,
                title: '',
                description: '',
                category_ids: [],
                image: null
            },
            isEditing: false,
            user: null,
            currentPage: 1,
            postsPerPage: 5
        };
    },
    computed: {
        paginatedPosts() {
            const start = (this.currentPage - 1) * this.postsPerPage;
            const end = start + this.postsPerPage;
            return this.posts.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.posts.length / this.postsPerPage);
        }
    },
    async created() {
        this.loadUser();
        await this.fetchPosts();
        await this.fetchCategories();
    },
    methods: {
        loadUser() {
            try {
                const userData = localStorage.getItem('user');
                if (userData) {
                    this.user = JSON.parse(userData);
                }
            } catch (error) {
                console.error('Ошибка при загрузке пользователя из localStorage:', error);
            }
        },

        async fetchPosts() {
            try {
                const postsResponse = await axios.get('/api/posts');
                if (!postsResponse || !postsResponse.data) {
                    throw new Error('Invalid response from server when fetching posts');
                }
                const posts = postsResponse.data;
                this.posts = posts.map(post => ({
                    ...post,
                    image: post.image ? `/storage/${post.image}` : null
                }));
            } catch (error) {
                alert('Ошибка при получении постов');
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
        createPost() {
            if (!this.user) {
                this.$router.push('/login');
            } else {
                this.isEditing = false;
                this.postForm = {
                    id: null,
                    title: '',
                    description: '',
                    category_ids: [],
                    image: null
                };
                new window.bootstrap.Modal(document.getElementById('postModal')).show();
                document.body.classList.add('modal-open');
            }
        },

        handleImageUpload(event) {
            const file = event.target.files[0];
            this.postForm.image = file;
        },
        async storePost() {
            try {
                const token = localStorage.getItem('token');
                const formData = new FormData();
                formData.append('title', this.postForm.title);
                formData.append('description', this.postForm.description);
                this.postForm.category_ids.forEach(id => formData.append('category_ids[]', id));
                if (this.postForm.image) {
                    formData.append('image', this.postForm.image);
                }

                const response = await axios.post('/api/posts', formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });
                this.posts.push(response.data);
                await this.fetchPosts();
                this.closeModal();
            } catch (error) {
                alert('Ошибка при создании поста');
            }
        },

        getCategoryNames(categories) {
            return categories.map(category => category.title).join(', ');
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
        changePage(page) {
            if (page > 0 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },

    },

};
</script>

<style scoped>
@import "../styles/button.css";
@import "../styles/form.css";
@import "../styles/username.css";
@import "../styles/pagination.css";
.main-title {
    font-size: 60px;
    color: #343a40;
}

.post-item {
    background: linear-gradient(180deg, rgb(255, 255, 255), #7e7d7d,#7e7d7d,#7e7d7d, #7e7d7d, rgb(255, 255, 255));;
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
    font-size: 25px;
    color: white;
    font-style: italic;
}

.post-title, .post-description {
    margin-bottom: 10px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    word-break: break-word;
}

.post-meta {
    font-size: 16px;
    font-weight: bold;
    color: #000000;
    max-width: 50%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    word-break: break-word;

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
    max-height: 300px;
    width: auto;
    height: auto;
    border-radius: 10px;
}

.post-link {
    color: inherit;
    text-decoration: none;
}

.post-category-group {
    accent-color: #000000;
}
</style>


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
                            <button class="custom-btn custom-btn-secondary" @click="goToPosts">Посты</button>
                            <button class="custom-btn custom-btn-danger" @click="logout">Выйти</button>
                        </div>
                        <div v-else class="d-flex align-items-center">
                            <button class="custom-btn custom-btn-secondary" @click="goToLogin">Войти</button>
                            <button class="custom-btn custom-btn-danger" @click="goToRegister">Зарегистрироваться</button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="container mt-5">
            <div class="row justify-content-center mt-5">
                <div class="col-md-10">
                    <div class="main-body">
                        <h1 class="text-center mb-4">Категории</h1>
                        <div v-if="categories.length === 0">
                            <p>Категорий нет</p>
                        </div>
                        <div v-else class="category-group">
                            <div class="category-item" v-for="category in paginatedCategories" :key="category.id">
                                <h5 class="category-title mb-1 text-center">{{ category.title }}</h5>
                                <p class="category-description mb-1 text-center">Автор: {{ category.user.name }}</p>

                                <div class="d-flex justify-content-between" v-if="canEditOrDelete(category)">
                                    <button class="custom-btn custom-btn-primary btn-ed-del" @click="editCategory(category)"><i class="fas fa-edit"></i></button>
                                    <button class="custom-btn custom-btn-primary btn-ed-del" @click="deleteCategory(category.id)"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <button class="custom-btn custom-btn-primary mt-3 mx-auto d-block" @click="createCategory">Создать категорию</button>
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
            <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title w-100 text-center" id="categoryModalLabel">{{ isEditing ? 'Редактирование' : 'Создать категорию' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="isEditing ? updateCategory() : storeCategory()">
                                <div class="mb-3">
                                    <label for="title" class="form-label d-flex justify-content-center">Название</label>
                                    <input type="text" class="form-control" v-model="categoryForm.title" required>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="custom-btn custom-btn-primary">{{ isEditing ? 'Сохранить' : 'Создать' }}</button>
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
            categories: [],
            categoryForm: {
                id: null,
                title: ''
            },
            isEditing: false,
            user: null,
            currentPage: 1,
            categoriesPerPage: 10
        };
    },
    computed: {
        paginatedCategories() {
            const start = (this.currentPage - 1) * this.categoriesPerPage;
            const end = start + this.categoriesPerPage;
            return this.categories.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.categories.length / this.categoriesPerPage);
        }
    },
    async created() {
        this.loadUser();
        await this.fetchCategories();
    },
    methods: {
        loadUser() {
            const userData = localStorage.getItem('user');
            if (userData) {
                this.user = JSON.parse(userData);
                console.log('Loaded User:', this.user);
            }
        },
        async fetchCategories() {
            try {
                const token = localStorage.getItem('token');
                const response = await axios.get('/api/categories', {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });
                this.categories = response.data;
            } catch (error) {
                alert('Ошибка при получении категорий');
                console.error(error.response.data);
            }
        },
        createCategory() {
            this.isEditing = false;
            this.categoryForm = {
                id: null,
                title: ''
            };
            const modal = new window.bootstrap.Modal(document.getElementById('categoryModal'));
            modal.show();
        },
        editCategory(category) {
            this.isEditing = true;
            this.categoryForm = {
                id: category.id,
                title: category.title
            };
            const modal = new window.bootstrap.Modal(document.getElementById('categoryModal'));
            modal.show();
        },
        async storeCategory() {
            try {
                const token = localStorage.getItem('token');
                const response = await axios.post('/api/categories', this.categoryForm, {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });
                this.categories.push(response.data);

                const modal = window.bootstrap.Modal.getInstance(document.getElementById('categoryModal'));
                modal.hide();
            } catch (error) {
                alert('Ошибка при создании категории');
                console.error(error.response.data);
            }
        },
        async updateCategory() {
            try {
                const token = localStorage.getItem('token');
                const response = await axios.put(`/api/categories/${this.categoryForm.id}`, this.categoryForm, {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });
                const index = this.categories.findIndex(category => category.id === this.categoryForm.id);
                this.categories.splice(index, 1, response.data);
                const modal = window.bootstrap.Modal.getInstance(document.getElementById('categoryModal'));
                modal.hide();
            } catch (error) {
                alert('Ошибка при обновлении категории');
                console.error(error.response.data);
            }
        },
        async deleteCategory(id) {
            try {
                const token = localStorage.getItem('token');
                await axios.delete(`/api/categories/${id}`, {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });
                this.categories = this.categories.filter(category => category.id !== id);
            } catch (error) {
                alert('Ошибка при удалении категории');
                console.error(error.response.data);
            }
        },
        canEditOrDelete(category) {
            return this.user && category.user && category.user.id === this.user.id;
        },
        changePage(page) {
            if (page > 0 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },
        goToPosts() {
            this.$router.push('/');
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
        }

    },


};
</script>

<style scoped>
@import "../styles/button.css";
@import "../styles/form.css";
@import "../styles/username.css";
@import "../styles/pagination.css";


.category-title {
    font-size: 25px;
    color: white;
}
.category-description {
    font-size: 16px;
}

.category-group {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

.category-item {
    background: linear-gradient(135deg, #252525, #9f9f9f, #252525);
    color: #000000;
    border: none;
    padding: 20px;
    transition: 0.5s;
    border-radius: 10px;
    box-shadow: 10px 10px 10px #8a8a8a;
    margin: 7%;
    overflow: hidden;
    box-sizing: border-box;
    text-overflow: ellipsis;
    white-space: nowrap;
    word-break: break-word;
}

.category-item:hover {
    background: linear-gradient(-135deg,#252525, #9f9f9f, #252525);
    transform: translateY(-5px);
    box-shadow: 20px 20px 20px #8a8a8a;
}

.category-item h5,
.category-item p {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    word-break: break-word;
}


.btn-ed-del {
    width: 15%;
    height: 30px;
    margin: 0;
    padding: 0;
}

</style>

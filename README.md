# DoctrineBlog API

![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![Docker](https://img.shields.io/badge/Docker-19.03-blue)
![Doctrine](https://img.shields.io/badge/Doctrine-2-red)
![PHP](https://img.shields.io/badge/PHP-8.0-purple)

Этот проект представляет собой API для управления статьями блога. API предоставляет функционал для создания, хранения, получения статей

## Установка

<h2 align="center">Установка и запуск</h2>

1. Запустите командную оболочку Bash


2. Клонируйте репозиторий:
    ```
   git clone git@github.com:SedoyTV/Blog.git
    ```

3. Перейдите в директорию проекта:
    ```
    cd Blog
   ```

4. В командной строке введите
   ```
    docker-compose up --build -d
   ```

5. В командной строке введите
   ```
   ./setup.sh
   ```

6. Дождитесь выполнения скрипта


7. Откройте браузер и перейдите по адресу
   ```
   http://localhost:8000
   ```
8. База данных доступна по адресу
   ```
   http://localhost:8081
   ```

### Создание статьи

- Поля title и description являются обязательными
- **URL:** `POST /api/posts`
  - **Пример запроса:**
    ```json
    {
        "title": "Название статьи",
        "description": "Описание"
    }
    ```
    - **Пример ответа:**
      ```json
      {
            "id": 10,
            "title": "Название статьи",
            "description": "Описание",
            "created_at": {
                "date": "2024-06-12 16:29:07.891492",
                "timezone_type": 3,
                "timezone": "UTC"
            },
            "updated_at": {
                "date": "2024-06-12 16:29:07.891491",
                "timezone_type": 3,
                "timezone": "UTC"
            }
      }
      ```

    - **Пример ответа если не передать title или description :**
    ```json
    {
      "message":
    {
          "title": [
              "The title field is required."
          ],
          "description": [
              "The description field is required."
          ]
    }
    }
    

    ```
### Получение всех статей

- **URL:** `GET /api/posts`

  - **Пример ответа если статьи найдены:**
    ```json
    [
      {
          "id": 10,
          "title": "pass",
          "description": "avas@A.ru",
          "created_at": {
              "date": "2024-06-12 16:29:07.000000",
              "timezone_type": 3,
              "timezone": "UTC"
          },
          "updated_at": {
              "date": "2024-06-12 16:29:07.000000",
              "timezone_type": 3,
              "timezone": "UTC"
          }
      },
      {
          "id": 9,
          "title": "pass",
          "description": "avas@A.ru",
          "created_at": {
              "date": "2024-06-12 16:27:05.000000",
              "timezone_type": 3,
              "timezone": "UTC"
          },
          "updated_at": {
              "date": "2024-06-12 16:27:05.000000",
              "timezone_type": 3,
              "timezone": "UTC"
          }
      },
      {
          "id": 8,
          "title": "pass",
          "description": "avas@A.ru",
          "created_at": {
              "date": "2024-06-12 15:20:57.000000",
              "timezone_type": 3,
              "timezone": "UTC"
          },
          "updated_at": {
              "date": "2024-06-12 15:20:57.000000",
              "timezone_type": 3,
              "timezone": "UTC"
          }
      }
    ]
    ```
- **Пример ответа если статьи не найдены:**
  ```json
  {
      "message": "К сожалению статей нет"
  }
  ```






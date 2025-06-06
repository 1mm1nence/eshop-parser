Для запуску проєкту:

### 1. Білдимо та запускаємо контейнери:
```bash
  docker-compose up -d --build
 ```

### 2. Інсталюємо залежності:
```bash
  docker exec symfony_app composer install
 ```

### 3. Відтворюємо міграції:
```bash
  docker exec -it symfony_app php bin/console doctrine:migrations:migrate
 ```

### 4. Виконуємо команду для парсингу продуктів з розетки:
```bash
  docker exec symfony_app php bin/console app:parse:rozetka
 ```

CSV файл знаходитиметься у папці var/. 

Посилання на REST endpoint: http://localhost:8080/api/products <br>
Посилання на трігер парсингу за допомогою веб контролера: http://localhost:8080/parse_manual (тех завдання можна трактувати по різному, зробив на всяк випадок)


Інтерфейс RabbitMQ: http://localhost:15672
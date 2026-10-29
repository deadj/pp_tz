## Стек

- **PHP 8.3**
- **Laravel 13.x**
- **Драйвер кэша:** `database`

## Как подключить решение

```bash
1. composer install
2. cp .env.example .env
3. создать БД, занить DB_DATABASE, DB_USERNAME, DB_PASSWORD в .env
4. php artisan key:generate
5. php artisan migrate
6. php artisan db:seed // для заполнения БД
```

## Как протестировать
```bash
php artisan test
```

## Как устроены ключи и инвалидация

### Ключи кэша

| Метод | TTL | Шаблон ключа |
|-------|-----|--------------|
| `getMostPopularStories($number)` | 1 час | `stories:popular:{number}` |
| `getStoriesForTag($tagSlug)` | 6 часов | `stories:tag:{slug}` |

Кэширование через `Cache::tags()->remember()`. Ключи разных методов и разных значений параметров не пересекаются.

### Инвалидация

Через `StoryObserver`
- created, updated, deleted: сбрасывается весь кэш популярных сказок и тегов, к которым относится сказка.
- Теги других сказок не сбрасываются - остаются в кэше до истечения TTL.
- При удалении теги сказки загружаются заранее через `$story->tags()->pluck('slug')`.

## Допущения, ограничения

1. Драйвер кэша database. Для прода заменить на Redis.
2. Инвалидация только при Story::created/updated/deleted. Изменения vote, tags, category, thumbnail не сбрасывают кеш.
3. Архитектура: нет разделения на Domain, Application, Infrastructure слои. Для тестового задания избыточно.

## Дополнительные причины устаревания кэша

При изменении ниже описанных свойств сказки выборки в кэше и БД будут отличаться. В реальном проекте следует обновлять кэш при их изменении
1. vote
2. tags
3. category
4. thumbnail
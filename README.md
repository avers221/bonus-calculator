## Предварительные требования

Проект обернут в докер, для его запуска потребуется установка docker и docker compose

Проверка праздничных дней будет выполняться с помощью библиотеки phptcloud/isdayoff-sdk, никаких ключей не требуется

Перед сборкой контейнеров и разворачиванием проекта необходимо задублировать .env.example и сохранить его как .env 

Там мы укажем настройки, необходимые для работы и разворачивания сервиса. Например, контейнер базы данных будет настроен
исходя из параметров, указанных в .env

## Запуск сервера

1. Внутри папки с проектом необходимо собрать контейрены, выполнив следующую команду. 

    ```shell
    docker compose up -d
    ```

2. После сборки проекта необходимо установить зависимости:

    ```shell
    docker compose exec -T php composer update 
    ```
3. Далее сгенерируем ключ приложения

    ```shell
    docker compose exec -T php php artisan key:generate
    ```
4. Далее выполним миграции и запустим сидеры

    ```shell
    docker compose exec -T php php artisan migrate --seed
    ```
   
5. Далее можно запустить тесты
   
    ```shell
    docker compose exec -T php php artisan test
    ```

## Использование

Для получения 

#### `POST /calculate-bonus`

##### В отправлять следующие хедеры

```
'Host: <calculated when request is sent>'
'Accept: application/json' 
'Content-Type: application/json'
'Content-Length: <calculated when request is sent>'
```

##### В теле запроса необходимо указать:

```
{
  "transaction_amount": сумма покупки,
  "timestamp": дата запроса в UTC-формате, когда была выполнена покупка в формате,
  "customer_status": статус клиента
}
```

Пример тела запроса

```json
{
  "transaction_amount": 150,
  "timestamp": "2025-03-08T14:30:00Z",
  "customer_status": "vip"
}
```

##### Ответ на запрос:

```
{
  "total_bonus": number,
  "applied_rules": [
    { "rule": "rule_slug", "bonus": number },
  ]
}
```
Пример ответа
```json
{
  "total_bonus": 42,
  "applied_rules": [
    { "rule": "base_rate", "bonus": 15 },
    { "rule": "holiday_bonus", "bonus": 15 },
    { "rule": "vip_boost", "bonus": 12 }
  ]
}
```

У каждого правила есть формула и правила, по которым она работает 

У формулы указывается оператор, который используется в формуле и значение, на которое будет изменено значение итогового бонуса

```
    Операторы, которые используются в формуле
    
        case MULTIPLY = 'multiply'
        ADD = 'add'
        DIVIDE = 'divide'
        SUBTRACT = 'subtract'
        DIVISION_WITHOUT_REMAINDER = 'division_without_remainder'
```

У правила указывается поле, которое будет учавствовать в условии правила, оператор условия по которому будет проверено знаение и значение, которое будет проверяться в условии

```
    Все возможные операторы условий
    
        EQUAL = '='
        NOT_EQUAL = '!='
        IN = 'in'
        NOT_IN = 'not_in'
        CHECK_HOLIDAY = 'check_holiday'
        CHECK_STATUS = 'check_status'
     
    Возможно указать проверку на дни рабочий/не рабочий день
     
        WORKDAY = 'workday'
        NON_WORKDAY = 'non_workday'
    
    Возможно указать проверку статус заказчика, обычный или вип
    
        case REGULAR = 'regular'
        VIP = 'vip'
```

Текущая реализация мне не совсем нравится, было сделано на скорую руку в короткие сроки. Планирую в будущем сделать следующее

1. Сделать апи для добавления, изменения и обновления правил
2. Написать swagger документацию для апи
3. Добавить JWT авторизацию 


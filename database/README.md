# tables

```sql
CREATE TABLE IF NOT EXISTS stores (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    street VARCHAR(255) NOT NULL,
    houseNo VARCHAR(10) NOT NULL,
    zip VARCHAR(10) NOT NULL,
    city VARCHAR(255) NOT NULL,
    latitude NUMERIC(8,6) NOT NULL,
    longitude NUMERIC(9,6) NOT NULL
);

CREATE TABLE IF NOT EXISTS stores_weather (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    store_id INTEGER NOT NULL,
    data_type INTEGER NOT NULL,
    json_data TEXT NOT NULL,
    week INTEGER
);
```

CREATE DATABASE IF NOT EXISTS vk_db_1 CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE DATABASE IF NOT EXISTS vk_db_2 CHARACTER SET utf8 COLLATE utf8_general_ci;

USE vk_db_1;

CREATE TABLE IF NOT EXISTS users (
  id                  INT NOT NULL AUTO_INCREMENT,
  email               VARCHAR(255) NOT NULL UNIQUE,
  fio                 VARCHAR(255) NOT NULL,
  balance             DECIMAL(14,2) NOT NULL DEFAULT 0,
  role                INT NOT NULL, -- 1 - customer; 2 - worker
  creation_time       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO users(email, fio, role)
VALUES('customer1@email', 'Петр Заказчиков', 1);

INSERT INTO users(email, fio, role)
VALUES('customer2@email', 'Дмитрий Заказчиков', 1);

INSERT INTO users(email, fio, role)
VALUES('worker1@email', 'Владимир Исполнительный', 2);

INSERT INTO users(email, fio, role)
VALUES('worker2@email', 'Максим Исполнительный', 2);




USE vk_db_2;

CREATE TABLE IF NOT EXISTS orders (
  id                  INT NOT NULL AUTO_INCREMENT,
  title               TEXT NOT NULL,
  description         TEXT,
  customer            INT NOT NULL,
  worker              INT,
  price               DECIMAL(14,2) NOT NULL DEFAULT 0,
  completed           BOOLEAN NOT NULL DEFAULT 0,
  payment_time        TIMESTAMP NULL DEFAULT 0,
  creation_time       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
  PRIMARY KEY (id)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
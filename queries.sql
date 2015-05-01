CREATE DATABASE vk_db_1 CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE DATABASE vk_db_2 CHARACTER SET utf8 COLLATE utf8_general_ci;

USE vk_db_1;

CREATE TABLE users (
  id                  INT NOT NULL AUTO_INCREMENT,
  email               VARCHAR(255) NOT NULL UNIQUE,
  password            VARCHAR(255) NOT NULL,
  fio                 VARCHAR(255) NOT NULL,
  balance             INT NOT NULL DEFAULT 0,
  role                INT NOT NULL, -- 1 - customer; 2 - performer
  creation_time       DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO users(email, password, fio, role)
VALUES('customer1@email', 'pass', 'Петр Заказчиков', 1);

INSERT INTO users(email, password, fio, role)
VALUES('customer2@email', 'pass', 'Дмитрий Заказчиков', 1);

INSERT INTO users(email, password, fio, role)
VALUES('performer1@email', 'pass', 'Владимир Исполнительный', 2);

INSERT INTO users(email, password, fio, role)
VALUES('performer2@email', 'pass', 'Максим Исполнительный', 2);




USE vk_db_2;

CREATE TABLE orders (
  id                  INT NOT NULL AUTO_INCREMENT,
  title               TEXT NOT NULL,
  description         TEXT,
  customer            INT NOT NULL,
  performer           INT,
  price               DECIMAL(14,2) NOT NULL DEFAULT 0,
  completed           BOOLEAN NOT NULL DEFAULT 0,
  payment_time        DATETIME,
  creation_time       DATETIME DEFAULT CURRENT_TIMESTAMP,  
  PRIMARY KEY (id)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
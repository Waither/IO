-- 1. Tabela zdarzeń (Event Store)
CREATE TABLE `events` (
  `id`           BIGINT       NOT NULL AUTO_INCREMENT,
  `aggregate_id` VARCHAR(36)  NOT NULL,
  `type`         VARCHAR(100) NOT NULL,
  `payload`      JSON         NOT NULL,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_aggregate` (`aggregate_id`),
  INDEX `idx_type`      (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Read model: lista zleceń
CREATE TABLE `order_list` (
  `order_id`   VARCHAR(36)  NOT NULL,
  `status`     VARCHAR(50)  NOT NULL,
  `created_at` DATETIME     NOT NULL,
  PRIMARY KEY (`order_id`),
  INDEX `idx_order_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Read model: przydziały kierowców
CREATE TABLE `driver_assignments` (
  `driver_id` VARCHAR(36)  NOT NULL,
  `order_id`  VARCHAR(36)  NOT NULL,
  `status`    VARCHAR(50)  NOT NULL,
  `assigned_at` DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`driver_id`,`order_id`),
  INDEX `idx_assignment_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Przykładowe dane do tabeli order_list
INSERT INTO `order_list` (`order_id`, `status`, `created_at`)
VALUES
  ('order-1', 'NEW', NOW()),
  ('order-2', 'ASSIGNED', NOW());

-- Przykładowe dane do tabeli driver_assignments
INSERT INTO `driver_assignments` (`driver_id`, `order_id`, `status`)
VALUES
  ('driver-1', 'order-1', 'ASSIGNED'),
  ('driver-2', 'order-2', 'PENDING');
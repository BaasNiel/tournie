-- RADIANT
SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';
SET @playerFrom = 5;
SET @playerTo = @playerFrom + 1;
SET @height = 205;

INSERT INTO scoreboard_mapping_slots (
  `scoreboard_mapping_id`,
  `key`,
  `offset_x`,
  `offset_y`,
  `top`,
  `right`,
  `bottom`,
  `left`,
  `width`,
  `height`,
  `text`,
  `created_at`
)
SELECT
SMS.scoreboard_mapping_id,
REPLACE(SMS.key, @playerFrom, @playerTo),
SMS.offset_x,
SMS.offset_y + @height,
SMS.top + @height,
SMS.right,
SMS.bottom,
SMS.left,
SMS.width,
SMS.height,
SMS.text,
SMS.created_at
FROM scoreboard_mapping_slots SMS
WHERE
SMS.key LIKE CONCAT('RADIANT_PLAYER_', @playerFrom, '_%')
ON DUPLICATE KEY UPDATE
offset_y = SMS.offset_y + @height,
top = SMS.top + @height;

-- DIRE
SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';
SET @playerFrom = 1;
SET @playerTo = @playerFrom + 1;
SET @height = 205;

INSERT INTO scoreboard_mapping_slots (
  `scoreboard_mapping_id`,
  `key`,
  `offset_x`,
  `offset_y`,
  `top`,
  `right`,
  `bottom`,
  `left`,
  `width`,
  `height`,
  `text`,
  `created_at`
)
SELECT
SMS.scoreboard_mapping_id,
REPLACE(SMS.key, @playerFrom, @playerTo),
SMS.offset_x,
SMS.offset_y + @height,
SMS.top + @height,
SMS.right,
SMS.bottom,
SMS.left,
SMS.width,
SMS.height,
SMS.text,
SMS.created_at
FROM scoreboard_mapping_slots SMS
WHERE
SMS.key LIKE CONCAT('DIRE_PLAYER_', @playerFrom, '_%')
ON DUPLICATE KEY UPDATE
offset_y = SMS.offset_y + @height,
top = SMS.top + @height;

-- DIRE_WINNER
INSERT INTO `tournie`.`scoreboard_mapping_slots`
(`scoreboard_mapping_id`, `key`, `offset_x`, `offset_y`, `top`, `right`, `bottom`, `left`, `width`, `height`, `text`, `created_at`, `updated_at`)
VALUES ('2', 'DIRE_WINNER', '159', '600', '724', '561', '161', '401', '160', '37', '5', '2021-12-08 20:09:44', '2021-12-08 20:09:44');

INSERT INTO `tournie`.`scoreboard_mapping_slots`
(`scoreboard_mapping_id`, `key`, `offset_x`, `offset_y`, `top`, `right`, `bottom`, `left`, `width`, `height`, `text`, `created_at`, `updated_at`)
VALUES
('2', 'DIRE_SCORE', '0', '636', '760', '294', '197', '242', '52', '37', '5', '2021-12-08 20:11:07', '2021-12-08 20:11:07');

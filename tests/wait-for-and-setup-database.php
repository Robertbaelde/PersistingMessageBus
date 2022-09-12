<?php

use EventSauce\BackOff\LinearBackOffStrategy;
use Illuminate\Database\Capsule\Manager;

/**
 * @codeCoverageIgnore
 */
include __DIR__ . '/../vendor/autoload.php';

$manager = new Manager;
$manager->addConnection(
    [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'message_bus_test',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ]
);

$tries = 0;
$backOff = new LinearBackOffStrategy(200000, 50);

while(true) {
    start:
    try {
        $tries++;
        $connection = $manager->getConnection();
        $connection->select('SHOW TABLES');
        fwrite(STDOUT, "DB connection established!\n");
        break;
    } catch (Throwable $exception) {
        fwrite(STDOUT, "Waiting for a DB connection...\n");
        fwrite(STDOUT, $exception->getMessage(). "\n");
        $backOff->backOff($tries, $exception);
        goto start;
    }
}

$connection->getSchemaBuilder()->dropIfExists('public_messages');
$connection->statement(<<<SQL
CREATE TABLE IF NOT EXISTS `public_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `message_id` varchar (255) NOT NULL,
  `topic` varchar (255) NOT NULL,
  `message_type` varchar (255) NOT NULL,
  `payload` varchar (1200) NOT NULL,
  `headers` varchar (1200) NOT NULL,
  `published_at` timestamp NOT NULL,
  PRIMARY KEY (`id` ASC),
  KEY `topic` (`topic`, `id` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL
);

$connection->statement(<<<SQL
CREATE TABLE IF NOT EXISTS `message_consumer_state` (
  `consumer_name` varchar (255) NOT NULL,
  `cursor` varchar (1200) NOT NULL,
  `last_updated_at` timestamp NOT NULL,
  PRIMARY KEY (`consumer_name`),
  KEY `consumer_name` (`consumer_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL
);

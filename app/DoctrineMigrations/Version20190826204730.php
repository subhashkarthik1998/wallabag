<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Wallabag\CoreBundle\Doctrine\WallabagMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190826204730 extends WallabagMigration
{
    private $rules = [
        'host = "feedproxy.google.com"',
        'host = "feeds.reuters.com"',
        '_all ~ "https?://www\\\.lemonde\\\.fr/tiny.*"',
    ];

    public function up(Schema $schema): void
    {
        $this->skipIf($schema->hasTable($this->getTable('ignore_origin_user_rule')));
        $this->skipIf($schema->hasTable($this->getTable('ignore_origin_instance_rule')));

        $userTable = $schema->createTable($this->getTable('ignore_origin_user_rule'));
        $userTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $userTable->addColumn('config_id', 'integer');
        $userTable->addColumn('rule', 'string', ['length' => 255]);
        $userTable->addIndex(['config_id'], 'idx_config');
        $userTable->setPrimaryKey(['id']);
        $userTable->addForeignKeyConstraint($this->getTable('config'), ['config_id'], ['id'], [], 'fk_config');

        $instanceTable = $schema->createTable($this->getTable('ignore_origin_instance_rule'));
        $instanceTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $instanceTable->addColumn('rule', 'string', ['length' => 255]);
        $instanceTable->setPrimaryKey(['id']);

        if ('postgresql' === $this->connection->getDatabasePlatform()->getName()) {
            $schema->dropSequence('ignore_origin_user_rule_id_seq');
            $schema->createSequence('ignore_origin_user_rule_id_seq');
        }

        foreach ($this->rules as $rule) {
            $this->addSql('INSERT INTO ' . $this->getTable('ignore_origin_instance_rule') . " (rule) VALUES ('" . $rule . "');");
        }
    }

    public function down(Schema $schema): void
    {
        $this->dropTable($this->getTable('ignore_origin_user_rule'));
        $this->dropTable($this->getTable('ignore_origin_instance_rule'));
    }
}

<?php

namespace Claroline\ThemeBundle\Installation\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated migration based on mapping information: modify it with caution.
 *
 * Generation date: 2024/02/03 08:07:44
 */
final class Version20240203080729 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE claro_theme 
            ADD logo VARCHAR(255) DEFAULT NULL, 
            ADD title VARCHAR(255) DEFAULT NULL, 
            ADD subtitle VARCHAR(255) DEFAULT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE claro_theme 
            DROP logo, 
            DROP title, 
            DROP subtitle
        ');
    }
}

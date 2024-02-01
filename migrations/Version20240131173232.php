<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240131173232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_F11D61A2F8B715EC ON invitation');
        $this->addSql('DROP INDEX IDX_F11D61A226F62893 ON invitation');
        $this->addSql('ALTER TABLE invitation ADD sender_id INT NOT NULL, ADD receiver_id INT NOT NULL, DROP sender_email, DROP receiver_email');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2F624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F11D61A2F624B39D ON invitation (sender_id)');
        $this->addSql('CREATE INDEX IDX_F11D61A2CD53EDB6 ON invitation (receiver_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A2F624B39D');
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A2CD53EDB6');
        $this->addSql('DROP INDEX IDX_F11D61A2F624B39D ON invitation');
        $this->addSql('DROP INDEX IDX_F11D61A2CD53EDB6 ON invitation');
        $this->addSql('ALTER TABLE invitation ADD sender_email VARCHAR(200) NOT NULL, ADD receiver_email VARCHAR(200) NOT NULL, DROP sender_id, DROP receiver_id');
        $this->addSql('CREATE INDEX IDX_F11D61A2F8B715EC ON invitation (sender_email)');
        $this->addSql('CREATE INDEX IDX_F11D61A226F62893 ON invitation (receiver_email)');
    }
}

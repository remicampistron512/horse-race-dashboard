<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260306000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial horse race dashboard schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE racecourse (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, region VARCHAR(80) NOT NULL, surface VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE trainer (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(80) NOT NULL, last_name VARCHAR(80) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE jockey_or_driver (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(80) NOT NULL, last_name VARCHAR(80) NOT NULL, discipline VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE horse (id INT AUTO_INCREMENT NOT NULL, trainer_id INT NOT NULL, habitual_jockey_or_driver_id INT NOT NULL, name VARCHAR(120) NOT NULL, age INT NOT NULL, sex VARCHAR(20) NOT NULL, total_earnings DOUBLE PRECISION NOT NULL, recent_form VARCHAR(20) NOT NULL, INDEX IDX_HORSE_TRAINER (trainer_id), INDEX IDX_HORSE_JOCKEY (habitual_jockey_or_driver_id), PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE race (id INT AUTO_INCREMENT NOT NULL, racecourse_id INT NOT NULL, name VARCHAR(140) NOT NULL, date DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)", race_type VARCHAR(50) NOT NULL, discipline VARCHAR(50) NOT NULL, distance INT NOT NULL, ground_condition VARCHAR(30) NOT NULL, prize_money DOUBLE PRECISION NOT NULL, runner_count INT NOT NULL, INDEX IDX_RACE_RACECOURSE (racecourse_id), PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE race_result (id INT AUTO_INCREMENT NOT NULL, race_id INT NOT NULL, horse_id INT NOT NULL, jockey_or_driver_id INT NOT NULL, trainer_id INT NOT NULL, odds DOUBLE PRECISION NOT NULL, finish_position INT NOT NULL, earnings DOUBLE PRECISION NOT NULL, rope_number INT DEFAULT NULL, weight_carried DOUBLE PRECISION DEFAULT NULL, time_recorded VARCHAR(20) DEFAULT NULL, INDEX IDX_RESULT_RACE (race_id), INDEX IDX_RESULT_HORSE (horse_id), INDEX IDX_RESULT_JOCKEY (jockey_or_driver_id), INDEX IDX_RESULT_TRAINER (trainer_id), PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE horse ADD CONSTRAINT FK_HORSE_TRAINER FOREIGN KEY (trainer_id) REFERENCES trainer (id)');
        $this->addSql('ALTER TABLE horse ADD CONSTRAINT FK_HORSE_JOCKEY FOREIGN KEY (habitual_jockey_or_driver_id) REFERENCES jockey_or_driver (id)');
        $this->addSql('ALTER TABLE race ADD CONSTRAINT FK_RACE_RACECOURSE FOREIGN KEY (racecourse_id) REFERENCES racecourse (id)');
        $this->addSql('ALTER TABLE race_result ADD CONSTRAINT FK_RESULT_RACE FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('ALTER TABLE race_result ADD CONSTRAINT FK_RESULT_HORSE FOREIGN KEY (horse_id) REFERENCES horse (id)');
        $this->addSql('ALTER TABLE race_result ADD CONSTRAINT FK_RESULT_JOCKEY FOREIGN KEY (jockey_or_driver_id) REFERENCES jockey_or_driver (id)');
        $this->addSql('ALTER TABLE race_result ADD CONSTRAINT FK_RESULT_TRAINER FOREIGN KEY (trainer_id) REFERENCES trainer (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE race_result');
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE horse');
        $this->addSql('DROP TABLE jockey_or_driver');
        $this->addSql('DROP TABLE trainer');
        $this->addSql('DROP TABLE racecourse');
    }
}

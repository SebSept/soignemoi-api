<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240328103127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Première version de la base de données';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE doctor (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, medical_speciality VARCHAR(255) NOT NULL, employee_id VARCHAR(25) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE hospital_stay (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, checkin TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, checkout TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, reason VARCHAR(255) NOT NULL, medical_speciality VARCHAR(255) NOT NULL, patient_id INT NOT NULL, doctor_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DFE829DD6B899279 ON hospital_stay (patient_id)');
        $this->addSql('CREATE INDEX IDX_DFE829DD87F4FB17 ON hospital_stay (doctor_id)');
        $this->addSql('CREATE TABLE medical_opinion (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, date DATE NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, doctor_id INT NOT NULL, patient_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ED0050E987F4FB17 ON medical_opinion (doctor_id)');
        $this->addSql('CREATE INDEX IDX_ED0050E96B899279 ON medical_opinion (patient_id)');
        $this->addSql('CREATE TABLE patient (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, address1 VARCHAR(255) NOT NULL, address2 VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE prescription (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, date DATE NOT NULL, patient_id INT NOT NULL, doctor_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1FBFB8D96B899279 ON prescription (patient_id)');
        $this->addSql('CREATE INDEX IDX_1FBFB8D987F4FB17 ON prescription (doctor_id)');
        $this->addSql('CREATE TABLE prescription_item (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, drug VARCHAR(255) NOT NULL, dosage VARCHAR(255) NOT NULL, prescription_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1D4599EB93DB413D ON prescription_item (prescription_id)');
        $this->addSql('ALTER TABLE hospital_stay ADD CONSTRAINT FK_DFE829DD6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hospital_stay ADD CONSTRAINT FK_DFE829DD87F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE medical_opinion ADD CONSTRAINT FK_ED0050E987F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE medical_opinion ADD CONSTRAINT FK_ED0050E96B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D96B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D987F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prescription_item ADD CONSTRAINT FK_1D4599EB93DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE hospital_stay DROP CONSTRAINT FK_DFE829DD6B899279');
        $this->addSql('ALTER TABLE hospital_stay DROP CONSTRAINT FK_DFE829DD87F4FB17');
        $this->addSql('ALTER TABLE medical_opinion DROP CONSTRAINT FK_ED0050E987F4FB17');
        $this->addSql('ALTER TABLE medical_opinion DROP CONSTRAINT FK_ED0050E96B899279');
        $this->addSql('ALTER TABLE prescription DROP CONSTRAINT FK_1FBFB8D96B899279');
        $this->addSql('ALTER TABLE prescription DROP CONSTRAINT FK_1FBFB8D987F4FB17');
        $this->addSql('ALTER TABLE prescription_item DROP CONSTRAINT FK_1D4599EB93DB413D');
        $this->addSql('DROP TABLE doctor');
        $this->addSql('DROP TABLE hospital_stay');
        $this->addSql('DROP TABLE medical_opinion');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE prescription');
        $this->addSql('DROP TABLE prescription_item');
    }
}

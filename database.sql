CREATE DATABASE pos_system;
USE pos_system;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL
);

CREATE TABLE pos_masuk (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    tanggal_masuk DATE,
    jenis_surat VARCHAR(100) NOT NULL,
    perihal TEXT NOT NULL,
    asal_surat VARCHAR(255) NOT NULL,
    file_surat VARCHAR(255) NOT NULL,
    keterangan TEXT,
    nomor_surat VARCHAR(100),
    tipe_surat ENUM('umum', 'dana') DEFAULT 'umum'
);

CREATE TABLE pos_keluar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    tanggal_dibuat DATE,
    jenis_surat VARCHAR(100) NOT NULL,
    perihal TEXT NOT NULL,
    tujuan_surat VARCHAR(255) NOT NULL,
    file_surat VARCHAR(255) NOT NULL,
    keterangan TEXT
);

CREATE TABLE disposisi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    surat_masuk_id INT,
    nomor_urut INT NOT NULL,
    nomor_agenda VARCHAR(50),
    dari VARCHAR(255),
    tanggal_masuk DATE,
    perihal TEXT,
    tujuan VARCHAR(255),
    nomor_surat VARCHAR(50),
    FOREIGN KEY (surat_masuk_id) REFERENCES pos_masuk(id) ON DELETE CASCADE
);

ALTER TABLE disposisi ADD COLUMN nomor_urut_umum INT;
ALTER TABLE disposisi ADD COLUMN nomor_urut_dana INT;

ALTER TABLE disposisi ADD COLUMN ttd_pejabat VARCHAR(255);

DROP TRIGGER IF EXISTS before_insert_disposisi;


DELIMITER //
CREATE TRIGGER before_insert_disposisi
BEFORE INSERT ON disposisi
FOR EACH ROW
BEGIN
    DECLARE tipe_surat VARCHAR(10);
    
    SELECT tipe_surat INTO tipe_surat
    FROM pos_masuk
    WHERE id = NEW.surat_masuk_id;
    
    IF tipe_surat = 'dana' THEN
        SET NEW.nomor_urut_dana = (
            SELECT COALESCE(MAX(nomor_urut_dana), 0) + 1
            FROM disposisi
        );
        SET NEW.nomor_urut = NEW.nomor_urut_dana;
    ELSE
        SET NEW.nomor_urut_umum = (
            SELECT COALESCE(MAX(nomor_urut_umum), 0) + 1
            FROM disposisi
        );
        SET NEW.nomor_urut = NEW.nomor_urut_umum;
    END IF;
END//
DELIMITER ;

INSERT INTO users (username, password, role) VALUES
('admin', 'admin123', 'admin'),
('user', 'user123', 'user'); 
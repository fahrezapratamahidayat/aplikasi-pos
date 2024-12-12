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
    jenis_surat VARCHAR(100) NOT NULL,
    perihal TEXT NOT NULL,
    asal_surat VARCHAR(255) NOT NULL,
    file_surat VARCHAR(255) NOT NULL,
    keterangan TEXT
);

CREATE TABLE pos_keluar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    jenis_surat VARCHAR(100) NOT NULL,
    perihal TEXT NOT NULL,
    tujuan_surat VARCHAR(255) NOT NULL,
    file_surat VARCHAR(255) NOT NULL,
    keterangan TEXT
);

CREATE TABLE disposisi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    surat_masuk_id INT,
    nomor_agenda VARCHAR(50),
    dari VARCHAR(255),
    tanggal_masuk DATE,
    perihal TEXT,
    tujuan VARCHAR(255),
    FOREIGN KEY (surat_masuk_id) REFERENCES pos_masuk(id) ON DELETE CASCADE
);

INSERT INTO users (username, password, role) VALUES
('admin', 'admin123', 'admin'),
('user', 'user123', 'user'); 
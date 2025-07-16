-- Active: 1744105609247@@127.0.0.1@3306@garagephp_db
-- Création de la base de données avec la syntaxe correcte
CREATE DATABASE garagephp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE garagephp_db;

-- Table des utilisateurs : gère l'authentification avec nom d'utilisateur/email et l'autorisation avec un accès basé sur les rôles
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des voitures : gère l'inventaire des voitures avec tous les détails essentiels
CREATE TABLE cars (
    car_id INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    annee YEAR NOT NULL,
    couleur VARCHAR(50) NOT NULL,
    immatriculation VARCHAR(20) NOT NULL UNIQUE,
    prix DECIMAL(10, 2) NOT NULL,
    status ENUM('disponible', 'vendu') NOT NULL DEFAULT 'disponible',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
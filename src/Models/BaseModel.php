<?php

namespace App\Models;

use App\Config\Database;
use PDO;

// Rôle: initialise la connexion à la base de données
abstract class BaseModel
{

  /**
   * l'instance de connexion à la base de données
   * @var PDO 
   */
  protected PDO $db;

  /**
   * le nom de la table associée au model
   * @var string
   */
  protected string $table;

  public function __construct(?PDO $db = null)
  {
    $this->db = $db ?? Database::getInstance();
  }
}

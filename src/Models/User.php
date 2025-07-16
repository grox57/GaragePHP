<?php

namespace App\Models;

use InvalidArgumentException;
use PDO;

class User extends BaseModel
{
  protected string $table = "users";

  private ?int $user_id = null;
  private string $username;
  private string $email;
  private string $password;
  private string $role;

  // Getters
  public function getId(): ?int
  {
    return $this->user_id;
  }

  public function getUsername(): string
  {
    return $this->username;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getPassword(): string
  {
    return $this->password;
  }

  public function getRole(): string
  {
    return $this->role;
  }

  // Setters (avec  validation)
  public function setUsername(string $username): self
  {
    return $this;
  }

  public function setEmail(string $email): self
  {
    return $this;
  }

  public function setPassword(string $password): self
  {
    return $this;
  }

  public function setRole(string $role): self
  {
    return $this;
  }


  /**
   * Résumé de la sauvegarde de l'utilisateur en BDD
   * @return bool
   */

  public function save(): bool
  {
    if ($this->user_id === null) {

      $sql = "INSERT INTO {$this->table} (username, email, password, role) VALUES (:username, :email, :password, :role)";
      $stmt = $this->db->prepare($sql); // On se prémunit des attaques SQL

      $params = [
        ':username' => $this->username,
        ':email' => $this->email,
        ':password' => $this->password, // ATTENTION le mot de passe est déjà hasher
        ':role' => $this->role ?? 'user'  // On assigne par défaut le rôle User
      ];
    } else {
      $sql = "UPDATE {this->table} SET username = :username, email = :email, role = :role WHERE user_id = :user_id";
      $stmt = $this->db->prepare($sql);

      // On lie les paramètres pour la mise à jour
      $params = [
        ':username' => $this->username,
        ':email' => $this->email,
        ':role' => $this->role ?? 'user',
        ':user_id' => $this->user_id, // ATTENTION la condition WHERE est importante
      ];
    }
    $result = $stmt->execute($params);

    if ($this->user_id === null && $result) {
      $this->user_id = (int)$this->db->lastInsertId();
    }
    return $result;
  }

  /*  */
  /**
   * Résumé de la Fct findByEmail => chercher l’utilisateur dans la base de données à partir de son adresse e-mail
   * @param string $email
   * @return User|null
   */
  public function findByEmail(string $email): static
  {

    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
    $stmt->execute([':email => $email']);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return $data ? $this->hydrate($data) : null;
  }


  /**
   * Résumé de l'authentification => vérifie les ID's de l'utilisateur
   * @param string $email
   * @param string $password
   * @return static|null
   */

  public function authenticate(string $email, string $password): ?static
  {

    $user = $this->findByEmail($email);

    // On vérifie que l'utilisateur existe et que le MDP fourni correspond au MDP hashé stocké
    if ($user && password_verify($password, $user->password)) {
      return $user;
    }
    return null;
  }


  /* Fonction PHP Hydrate => Gérer la transition BDD -> Objet */
  /**
   * Le principe de l'hydratation consiste à remplir un objet, donc l'instance d'une classe, avec les variables lui
   * permettant d'être "remplie". Cela permet par exemple d'éviter d'avoir à remplir manuellement chaque champ de
   * chaque objet lorsque l'on lit les données dans la base.
   */

  /**
   * Résumé de la Fct hydrate
   * @param array $data
   * @return User
   */

  private function hydrate(array $data): static
  {
    $this->user_id = (int)$data['user_id'];
    $this->username = (int)$data['username'];
    $this->email = (int)$data['email'];
    $this->password = (int)$data['password'];
    $this->role = (int)$data['role'];
    return $this;
  }
}

<?php

namespace App\Controllers;

use App\Models\User;
use App\Security\Validator;
use App\Security\TokenManager;
use App\Utils\Logger;

use function PHPUnit\Framework\throwException;

/**
 * Cette classe gère les actions liées à l'authentification et à l'inscription des utilisateurs
 */

class AuthController extends BaseController
{
  // Attributs
  private User $userModel;
  private TokenManager $tokenManager;
  private Logger $logger;

  /**
   * Constructeur : il est appelé à chaque création d'un objet AuthController
   * on en profite pour instancier les modèles dont on aura besoin
   */
  public function __construct()
  {
    parent::__construct();
    $this->userModel = new User();
    $this->tokenManager = new TokenManager();
    $this->logger = new Logger();
  }

  /**
   * Méthode qui affiche la page avec le formulaire de connexion
   */

  public function showLogin(): void
  {
    // méthode render() = Affiche une Vue en l'injectant dans le layout principale
    $this->render(
      'auth/login',
      [
        'title' => 'Connexion',
        'csrf_token' => $this->tokenManager->generateCsrfToken()
      ]
    );
  }

  public function login(): void
  {
    // On s'assure que la requête est de type POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->response->redirect('/login');
    }

    $data = $this->getPostData();

    // Validation du jeton CSRF (sinon erreur)
    if (!$this->tokenManager->validateCsrfToken($data['csrf_token'] ?? '')) {
      $this->response->error('Token de sécurité invalide.', 403);
    }

    // Le model User s'occupe de la logique d'authentification
    $user = $this->userModel->authenticate($data['email'], $data['password']);

    if ($user) {
      // Si l'authentification réussit, on stocke les infos en session
      $_SESSION['user_id'] = $user->getId();
      $_SESSION['user_role'] = $user->getRole();
      $_SESSION['user_username'] = $user->getUsername();

      // Redirection vers le tableau de bord (page cars)
      $this->response->redirect('/cars');
    } else {
      // Affiche une Vue en l'injectant dans le layout principale
      $this->render(
        'auth/login',
        [
          'title' => 'Connexion',
          'error' => 'Email ou mot de passe incorrect.',
          'old' => ['email' => $data['email']],
          'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]
      );
    }
  }

  /**
   * Affichage du fomulaire d'inscription
   */

  public function showRegister(): void
  {

    $this->render(
      'auth/register',
      [
        'title' => 'Inscription',
        'csrf_token' => $this->token->generateCsrfToken()
      ]
    );
  }

  /**
   * Traitement des données "soumission formulaire inscription"
   */

  public function register(): void
  {
    // On vérifie que la méthode est de type POST, sinon on redirige vers register
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->response->redirect('/register');
    }

    $data = $this->getPostData();

    // Validation du jeton CSRF (sinon erreur)
    if (!$this->tokenManager->validateCsrfToken($data['csrf_token'] ?? '')) {
      $this->response->error('Token de sécurité invalide.', 403);
    }

    // Validation du formulaire
    $errors = $this->validator->validate(
      $data,
      [
        'username' => 'required | min:3 | max:50',
        'email' => 'required email',
        'password' => 'required | min:9',
        'password_confirm' => 'required | same:password'
      ]
    );
    // Vérification des erreurs du formulaire
    if (!empty($errors)) {
      $this->render(
        'auth/register',
        [
          'title' => 'Inscription',
          // On ajoute une erreur au champs email pour l'afficher
          'errors' => $errors,
          'old' => $data,
          'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]
      );
      return;
    }

    // Vérification de l'email si déjà exisatant en BDD
    if ($this->userModel->findByEmail($data['email'])) {
      $this->render(
        'auth/register',
        [
          'title' => 'Inscription',
          // On ajoute une erreur au champs email pour l'afficher
          'errors' => ['email' => ['Cette adresse email est déjà utilisée.']],
          'old' => $data,
          'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]
      );
      return;
    }

    /**
     * Si tout est correcte alors on crée un nouvel utilisateur
     */

    try {
      // On instancie un nouvel utilisateur
      $newUser = new User();

      // On utilise les setters pour assigner les valeurs de l'objet User (inclut la validation et le hashage du MDP)
      $newUser->setUsername($data['username'])
        ->setEmail($data['email'])
        ->setPassword($data['password'])
        ->setRole($data['user']); # rôle par défaut

      // On sauvegarde en BDD
      if ($newUser->save()) {
        // Si la création réussie, on connecte automatiquement l'utilisateur 
        $_SESSION['user_id'] = $newUser->getId();
        $_SESSION['user_role'] = $newUser->getRole();
        $_SESSION['user_username'] = $newUser->getUsername();
        $this->response->redirect('/cars');
      } else {
        // Si la sauvegarde échoue en renvoie une erreur
        throw new \Exception('La création du compte à échoué.');
      }
    } catch (\Exception $e) {
      $this->render('auth/register', [
        'title' => 'Inscription',
        'error' => 'Erreur : ' . $e->getMessage(), # Erreur générale
        'old' => $data,
        'csrf_token' => $this->tokenManager->generateCsrfToken()
      ]);
    }
  }

  /**
   * Méthode de déconnexion avec destruction de la session
   */

  public function logout(): void
  {
    // On vérifie que la méthode soit POST, sinon in redirige vers "register"
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->response->redirect('/');
    }

    // Détruit toutes les données de la session actuelle
    session_destroy();

    // Redirige vers la page de connexion
    $this->response->redirect('/login');
  }
}


/*
Les attaques CSRF :

Elles incitent un utilisateur authentifié à exécuter des actions involontaires sur une application web,
compromettant ainsi la sécurité des données.
Une attaque CSRF, ou Cross-Site Request Forgery, est une vulnérabilité de sécurité qui permet à un attaquant de tromper
un utilisateur authentifié pour qu'il exécute des actions non désirées sur une application web. Cela se produit généralement
sans que l'utilisateur en soit conscient, car les requêtes malveillantes ressemblent à des requêtes légitimes.


Exemples d'attaques CSRF

- Modification de mot de passe : Un attaquant peut inciter un utilisateur à changer son mot de passe sans qu'il le sache.
- Transfert de fonds : Dans le cas d'applications bancaires, un attaquant peut forcer un transfert d'argent à partir
du compte de l'utilisateur.


Utilisation de tokens CSRF :

Implémentez des tokens uniques pour chaque session utilisateur afin de valider les requêtes
*/

<?php

namespace App\Controllers;

use App\Security\Validator;
use App\Models\Response;

/**
 * Contrôleur de base
 * Toutes les autres classes de Controleur hériteront de celle-ci
 */

abstract class BaseController
{
  // Propriétés
  protected Response $response;
  protected Validator $validator;

  public function __construct()
  {
    $this->response = new Response();
    $this->validator = new Validator();
  }

  /**
   * Affiche une Vue en l'injectant dans le layout principale
   * @param string $view le nom du fichier de Vue
   * @param array $data les données à rendre accessible dans la Vue
   */

  protected function render(string $view, array $data = []): void
  {
    // On construit le chemin complet vers le fichier de Vue 
    $viewPath = __DIR__ . '/views/' . $view . '.php';

    // On vérifie que le fichier Vue existe bien 
    if (!file_exists($viewPath)) {
      $this->response->error("Vue non trouvée: $viewPath", 500);
      return;
    }

    // Extact = transforme les clés d'un tableau en variable
    // Ex: $data = ['title' => 'Accueil'] devient $title = 'Accueil'
    extract($data);

    // On utilise la mise en tampon de sortie (output buffering) pour capturer le html de la Vue
    ob_start();
    include $viewPath;

    // On vide le cache, la variable $content contient la Vue
    $content = ob_get_clean();

    // Finalement, on inclut le layout principal, qui peut maintenant utiliser la variable $content
    include __DIR__ . '/view/layout.php';
  }

  /**
   * Récupère et nettoie les données envoyées par une requête POST
   */

  protected function getPostData(): array
  {
    return $this->validator->sanitize($_POST);
  }

  /**
   * Vérifie si l'utilisateur est connecté, sinon le rediriger vers la page login
   */

  protected function requireAuth(): void
  {
    if (!isset($_SESSION['user_id'])) {
      $this->response->redirect('/login');
    }
  }
}

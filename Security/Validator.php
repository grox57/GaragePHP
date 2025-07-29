<?php

namespace App\Security;

/**
 * Fichier qui fournit des méthodes pour valider et nettoyer les données
 */

class Validator
{

  private array $errors = [];
  private array $data;

  public function validate(array $data, array $rules): array
  {
    $this->errors = [];
    $this->data = $data;

    foreach ($rules as $field => $rulesString) {                          #foreach = pour chaque
      $value = $data[$field] ?? null;
      $rulesArray = explode('|', $rulesString);

      foreach ($rulesArray as $rules) {
        $this->applyRule($field, $value, $rules);
      }
    }
    return $this->errors;
  }

  private function applyRule(string $field, $value, string $rule): void
  {
    $param = null;
    if (strpos($rule, ':') !== false) {
      [$rule, $param] = explode(':', $rule, 2);
    }

    switch ($rule) {
      case 'required':
        if (empty($value)) {
          $this->addError($field, "Le champs est requis.");
        }
        break;

      case 'email':
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
          $this->addError($field, "Le champs {$field} doit être une adresse eamil valide.");
        }
        break;

      case 'min':
        if (strlen($value) < (int) $param) {
          $this->addError($field, "Le champs {$field} doit contenir au moins {$param} caractères.");
        }
        break;

      case 'max':
        if (strlen($value) > (int) $param) {
          $this->addError($field, "Le champs {$field} ne peut pas dépasser {$param} caractères.");
        }
        break;

      case 'same':
        if ($value !== ($this->data[$param] ?? null)) {
          $this->addError($field, "Le champs {$field} ne doit pas être identique au champs {$param}.");
        }
        break;
    }
  }

  private function addError(string $field, string $message): void
  {
    $this->errors[$field][] = $message;
  }

  public function sanitize(array $data): array
  {
    $sanitize = [];
    foreach ($data as $key => $value) {
      $sanitize[$key] = is_string($value) ? htmlspecialchars(trim($value), ENT_QUOTES, 'UTF_8') : $value;
    }
    return $sanitize;
  }
}

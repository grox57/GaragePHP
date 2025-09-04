<form action="/register" method="POST" class="auth-form">

  <h2>Créer un compte</h2>

  <?php if (isset($error)): ?>
    <p class="error-message"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

  <div class="form-group">
    <label for="username">Non d'utilissateur</label>
    <input type="text" id="username" value="<?= htmlspecialchars($old["username"] ?? '') ?>" required>
    <?php if (isset($error['username'])): ?>
      <p class="error-validation"><?= htmlspecialchars($error['username'][0]) ?></p>
    <?php endif; ?>
  </div>

  <div class="form-group">
    <label for="email">Adresse email</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($old["email"] ?? '') ?>" required>
    <?php if (isset($error['email'])): ?>
      <p class="error-validation"><?= htmlspecialchars($error['email'][0]) ?></p>
    <?php endif; ?>
  </div>
  <div class="form-group">
    <label for="password">Mot de passe</label>
    <input type="password" name="password" id="password" required>
    <?php if (isset($error['password'])): ?>
      <p class="error-validation"><?= htmlspecialchars($error['password'][0]) ?></p>
    <?php endif; ?>
  </div>

  <div class="form-group">
    <label for="password_confirm">Confirmer le mot de passe</label>
    <input type="password" name="password_confirm" id="password_confirm" required>
    <?php if (isset($error['password_confirm'])): ?>
      <p class="error-validation"><?= htmlspecialchars($error['password_confirm'][0]) ?></p>
    <?php endif; ?>
  </div>

  <button type="submit">S'inscrire</button>
</form>
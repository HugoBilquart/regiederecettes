<nav class="navbar navbar-expand-lg navbar-light bg-light" id="menu">
  <a class="navbar-brand" href="accueil.php" title="<?php echo basename($_SERVER['PHP_SELF']); ?>">
    <img src="img/icons/home.png" alt="home-button" id="home-button">
  </a>
  <div>
    <ul class="navbar-nav mr-auto right-left">
      <li class="nav-item">
        <a class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'transaction.php') echo 'active' ?>" href="transaction.php">
          Nouvelle transaction
        </a>
      </li>
      <?php
        if($_SESSION['a_regie']) {
          ?>
          <li class="nav-item">
            <a class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'regiederecettes.php') echo 'active' ?>" href="regiederecettes.php">
              Régie de recettes
            </a>
          </li>
          <?php
        }
      ?>
    </ul>
    <ul class="navbar-nav mr-auto right-menu" id="login-info">
      <li class="nav-item">
        <a id="role">Poste : <?php echo $_SESSION['fonction']; ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="deconnexion.php">Deconnexion</a>
      </li>
    </ul>
  </div>
</nav>
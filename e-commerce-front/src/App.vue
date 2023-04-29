
import router from './router';

<template>
  <h1>E-Commerce</h1>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">Accueil</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="/products">Liste des produits</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/cart">Panier</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/orders">Commandes</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Votre compte
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/user">Informations</a></li>
            <li><a class="dropdown-item" href="/login">Connexion</a></li>
            <li><a class="dropdown-item" href="/signup">Créer un compte</a></li>
            <li><a class="dropdown-item" href="/user/edit">Modifier votre profil</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
  </nav>
  <router-view/>
</template>

<style>
body{
  background-color: rgb(230, 232, 235);
}
nav{
  background-color: lightgrey
}
h1{
  text-align: center
}

@import'~bootstrap/dist/css/bootstrap.css';
</style>

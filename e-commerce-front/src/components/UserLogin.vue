<template>
  <div>
    <h1>Se connecter à votre compte</h1>
    <div class="container-sm text-center">
      <div class="row">
        <div :class="{'alert alert-primary' : !isError, 'alert alert-danger' : isError}" role="alert" v-show="messageShow">
          {{ messageValue }}
        </div>
        <div class="col">
          <form v-on:submit.prevent="login">
            <div class="mb-3">
              <label for="loginInput" class="form-label">Votre login</label>
              <input type="text" class="form-control" id="loginInput" aria-describedby="emailHelp" v-model="username"/>
            </div>
            <div class="mb-3">
              <label for="passwordInput" class="form-label">Mot de passe</label>
              <input type="password" class="form-control" id="passwordInput" v-model="password"/>
            </div>
            <button type="submit" class="btn btn-primary">Connexion</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data(){
    return {
      username: '',
      password: '',
      messageShow: false,
      messageValue: '',
      isError: false

    }
  },

  methods: {
    login: async function (){
      if(this.username == "" || this.username == null || this.password == "" || this.password == null){
        this.messageValue = "Attention vous devez remplir correctement les champs de texte";
        this.isError = true;
        this.messageShow = true
      }
      else{
        await axios.post('http://192.168.56.108:8080/api/login', {login: this.username ,password: this.password })
        .then(response => {
          this.messageValue = "Vous êtes connecté, vous pouvez maintenant dépenser votre salaire sur notre site";
          this.messageShow = true;
          this.isError = false;
          localStorage.setItem('token', response.data.token);
        })
        .catch(error => {
          this.messageValue = "Votre mot de passe ou votre login est incorrect veuillez retenter l'opération";
          this.messageShow = true;
          this.isError = true;
        });
      }
    }
  },
}

</script>

<style>

</style>

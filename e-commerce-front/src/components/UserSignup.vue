<template>
    <div>
        <h1>Créer un compte</h1>
        <div class="container-sm text-center">
            <div class="col">
                <div :class="{'alert alert-primary' : !isError, 'alert alert-danger' : isError}" role="alert" v-show="messageShow">
                    {{ messageValue }}
                </div>
                <form v-on:submit.prevent="createAccount">
                    <div class="mb-3">
                        <label for="loginInput" class="form-label">Votre login</label>
                        <input type="text" class="form-control" id="loginInput" aria-describedby="emailHelp" v-model="username"/>
                    </div>
                    <div class="mb-3">
                        <label for="passwordInput" class="form-label">Votre mot de passe</label>
                        <input type="password" class="form-control" id="passwordInput" v-model="password"/>
                    </div>
                    <div class="mb-3">
                        <label for="emailInput" class="form-label">Votre adresse mail</label>
                        <input type="email" class="form-control" id="emailInput" v-model="mail" />
                    </div>
                    <div class="mb-3">
                        <label for="firstnameInput" class="form-label">Votre prénom</label>
                        <input type="text" class="form-control" id="firstnameInput" v-model="firstname"/>
                    </div>
                    <div class="mb-3">
                        <label for="lastnameInput" class="form-label">Votre nom</label>
                        <input type="text" class="form-control" id="lastnameInput" v-model="lastname"/>
                    </div>
                    <button type="submit" class="btn btn-primary">Connexion</button>
                 </form>
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
                mail: '',
                firstname: '',
                lastname:'',
                messageShow: false,
                messageValue: '',
                isError: false
            }
        },
        methods:{
            createAccount: async function(){
                if(this.username == "" || this.username == null || this.password == "" || this.password == null || this.mail == "" || this.mail == null ||
                this.firstname == "" || this.firstname == null || this.lastname == "" || this.lastname == null){
                    this.messageValue = "Attention vous devez remplir correctement les champs obligatoires"
                    this.isError = true;
                    this.messageShow = true;
                }
                else{
                    await axios.post('http://192.168.56.108:8080/api/register', {
                        login: this.username,
                        password: this.password,
                        email: this.mail,
                        firstname: this.firstname,
                        lastname: this.lastname
                    })
                    .then(response => {
                        console.log(response);
                        this.messageValue = "Votre compte a bien été créer, veuillez maintenant vous connectez";
                        this.messageShow = true;
                        this.isError = false;
                    })
                    .catch(error =>{
                        console.log(error);
                        this.messageValue = "Une erreur est survenue lors de la création du compte";
                        this.messageShow = true;
                        this.isError = true;
                    })
                }
            }
        }
    }
</script>

<style lang="scss" scoped>

</style>

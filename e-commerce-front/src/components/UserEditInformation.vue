<template>
    <div>
        <h1>Modifier vos informations</h1>
        <div class="container-sm text-center">
            <div class="col">
                <div :class="{'alert alert-primary' : !isError, 'alert alert-danger' : isError}" role="alert" v-show="messageShow">
                    {{ messageValue }}
                </div>
                <form v-on:submit.prevent="update">
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
                        <input type="email" class="form-control" id="emailInput" v-model="mail"/>
                    </div>
                    <div class="mb-3">
                        <label for="firstnameInput" class="form-label">Votre prénom</label>
                        <input type="text" class="form-control" id="firstnameInput" v-model="firstname" />
                    </div>
                    <div class="mb-3">
                        <label for="lastnameInput" class="form-label">Votre nom</label>
                        <input type="text" class="form-control" id="lastnameInput" v-model="lastname" />
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
            return{
                username: '',
                password: '',
                mail:'',
                firstname:'',
                lastname:'',
                messageShow: false,
                messageValue: '',
                isError: false
            }
        },
        methods:{
            update: function() {
                console.log(this.username);
                console.log(this.password);
                console.log(this.mail);
                console.log(this.firstname);
                console.log(this.lastname);
                var token = localStorage.getItem('token');
                if(this.username == "" || this.username == null || this.password == "" || this.password == null || this.mail == "" || this.mail == null ||
                this.firstname == "" || this.firstname == null || this.lastname == "" || this.lastname == null){
                    this.messageShow = true;
                    this.messageValue = "Attention vous devez remplir correctement tous les champs d'entrée";
                    this.isError = true;
                }
                else{
                    console.log(token);
                    axios.put('http://192.168.56.108:8080/api/users',{login: this.username, password: this.password, email: this.mail, firstname: this.firstname
                    , lastname: this.lastname},{
                        headers:{
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log(response);
                        this.messageShow = true;
                        this.messageValue = "Votre compte a bien été modifier";
                        this.isError = false;
                    })
                    .catch(error => {
                        console.log(error);
                        this.messageShow = true;
                        this.messageValue = "Une erreur est survenue lors de la modification de votre compte";
                        this.isError = true;
                    })
                }
                
            }
        }
    }
</script>

<style>

</style>

<template>
    <div>
        <h1>User Information</h1>
        <div class="container-lg text-center">
            <div class="row">
                <div :class="{ 'alert alert-danger' : !log}" role="alert" v-show="!log">
                    {{ messageValue }}
                </div>
                <div class="col">
                    <p v-show="log">Votre login : {{ userLogin }}</p>
                    <p v-show="log">Votre adresse mail : {{ userEmail }}</p>
                    <p v-show="log">Votre nom et prénom : {{ userFirstname }} , {{ userLastname }}</p>
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
                userLogin: '',
                userEmail: '',
                userFirstname: '',
                userLastname: '',
                log: true,
                messageValue: 'Vous devez vous connecter pour voir vos informations'
            }
        },
        async created(){
            await this.loadInformation();
        },
        methods:{
            loadInformation: async function (){
                var token = localStorage.getItem('token');
                console.log(token);
                if(token == "" || token == null){
                    this.log = false;
                }
                else{
                    await axios.get('http://127.0.0.1:8080/api/users', {
                        headers:{
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        this.log = true;
                        this.userLogin = response.data.login;
                        this.userEmail = response.data.email;
                        this.userFirstname = response.data.firstname;
                        this.userLastname = response.data.lastname
                    })
                    .catch(error =>{
                        this.log = false;
                        this.messageValue = "Une erreur est survenue lors de la récupération de vos données, veuillez vous reconnecter";
                    })
                }
            }
        }
    }
</script>

<style>
card{
    display: flex;
    margin: auto;
    text-align: center;
}
</style>
<template>
    <div>
        <h1>Liste des commandes</h1>
        <div class="container-lb text-center">
            <p>Prix total {{ order.totalPrice }} €</p>
            <p>Description : {{ order.creationDate }}</p>
            <div class="card" v-for="product in order.products" style=" height: 35rem;">
                <img class="card-img-top" src="../assets/téléchargement.jpeg">
                <div class="card-body">
                    <p>Nom : {{ product.name }}</p>
                    <p>Description : {{ product.description }}</p>
                    <p>Prix : {{ product.price }} €</p>
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
                order: '',
                id: this.$route.params.id
            }
        },

        methods:{
            setOrder(data){
                this.order = data;
            }
        },
        mounted(){
            var token = localStorage.getItem('token');
            axios.get(`http://192.168.56.108:8080/api/orders/${this.id}`,{
                headers:{
                    'Authorization': "Bearer " + token,
                    'Accept': 'application/json'
                }
            })
            .then((response) => {
                console.log(response);
                this.setOrder(response.data);
            })
            .catch((error) => {
                console.log(error);
            })
        }
    }
</script>

<style>

</style>

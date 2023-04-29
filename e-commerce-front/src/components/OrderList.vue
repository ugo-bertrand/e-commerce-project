<template>
    <div>
        <h1>Liste des commandes</h1>
        <div class="container-lb text-center">
            <div class="alert alert-primary" role="alert" v-show="empty">
                    Votre liste de commande est vide
            </div>
            <div class="card" v-for="order in orders" style=" height: 15rem;">
                <div class="card-body">
                    <p>Prix total {{ order.totalPrice }} €</p>
                    <p>Date de la commande : {{ order.creationDate }}</p>
                    
                    <router-link class="btn btn-primary" :to="{name: 'orderDetail', params: {id: order.id}}">Voir les détails</router-link>
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
                orders: [],
                empty: false
            }
        },

        methods:{
            setOrders(data){
                this.orders = data;
            }
        },
        mounted(){
            var token = localStorage.getItem('token');
            axios.get("http://127.0.0.1:8080/api/orders/",{
                headers:{
                    'Authorization': "Bearer " + token,
                    'Accept': 'application/json'
                }
            })
            .then((response) => {
                console.log(response);
                this.setOrders(response.data);
                if(response.data.length == 0 ){
                    this.empty = true;
                }
            })
            .catch((error) => {
                console.log(error);
            })
        }
    }
</script>

<style>

</style>
<template>
    <div>
        <div class="row">
            <h1>Votre panier</h1>
            <button class="btn btn-success bouton" type="button" v-on:click.prevent="validateCart()">Valider votre commande</button>
            <div class="alert alert-primary" role="alert" v-show="empty">
                    Votre panier est vide
            </div>
        </div>
        <div class="container-lg text-center">
            <div class="card" v-for="product in products" style=" height: 35rem;">
                <img class="card-img-top" src="../assets/téléchargement.jpeg">
                <div class="card-body">
                    <p>Nom : {{ product.name }}</p>
                    <p>Description : {{ product.description }}</p>
                    <p>Prix : {{ product.price }} €</p>
                    
                    <button class="btn btn-danger" type="button" v-on:click.prevent="deleteCart(product.id)">Supprimer du panier</button>
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
                products: [],
                empty: false
            }
        },
        methods: {
            setProducts(data){
                this.products = data;
            },

            deleteCart(id){
                var token = localStorage.getItem('token');
                axios.delete(`http://127.0.0.1:8080/api/carts/${id}`,{
                    headers: {
                        'Authorization': "Bearer " + token,
                        'Accept': 'application/json'
                    }
                })
                .then((response) => {
                    console.log(response);
                    alert("Le produit a bien été supprimer de votre panier");
                })
                .catch((error) => {
                    console.log(error);
                    alert("Une erreur est survenue lors de la suppression du produit dans le panier");
                })
            },

            validateCart(){
                var token = localStorage.getItem('token');
                axios.post("http://127.0.0.1:8080/api/carts/validate",null,{
                    headers:{
                        'Authorization': "Bearer " + token,
                        'Accept': 'application/json'
                    }
                })
                .then((response) => {
                    console.log(response);
                    alert("Votre commande a bien été prise en compte");
                })
                .catch((error) => {
                    console.log(error);
                    alert("Une erreur est survenue lors de la création de la commande");
                })
            }
        },
        async mounted(){
            var token = localStorage.getItem('token');
            await axios
            .get("http://127.0.0.1:8080/api/carts",{
                headers:{
                    'Authorization': "Bearer " + token,
                    'Accept': 'application/json'
                }
            })
            .then((response) => {
                console.log(response);
                this.setProducts(response.data);
                if(response.data.length == 0){
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
.bouton{
    
    margin: auto;
    text-align: center;
}
</style>
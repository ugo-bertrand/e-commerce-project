<template>
    <div>
        <div class="row">
            <h1>Product List</h1>
            <router-link class="btn btn-primary bouton" :to="{name: 'addProduct'}">Ajouter un produit</router-link>
        </div>
        <div class="container-lg text-center">
            <div class="card" v-for="product in products" style=" height: 35rem;">
                <img class="card-img-top" src="../assets/téléchargement.jpeg">
                <div class="card-body">
                    <p>Nom : {{ product.name }}</p>
                    <p>Description : {{ product.description }}</p>
                    <p>Prix : {{ product.price }} €</p>
                    
                    <router-link class="btn btn-primary" :to="{name: 'productDetail', params: {id: product.id}}">Voir les détails</router-link>
                    <button class="btn btn-success" type="button" v-on:click.prevent="addToCart(product.id)">Ajouter au panier</button>
                    <button class="btn btn-danger" type="button" v-on:click.prevent="deleteProduct(product.id)">Supprimer le produit</button>
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
                products: []
            };
        },

        methods: {
            setProducts(data){
                this.products = data;
            },

            async addToCart(id){
                var token = localStorage.getItem('token');
                await axios.post(`http://127.0.0.1:8080/api/carts/${id}`,null,{
                    headers:{
                        'Authorization': "Bearer " + token,
                        'Accept': 'application/json'
                    }
                })
                .then((response) => {
                    console.log(response);
                    alert("Votre produit a bien été ajouter dans votre panier")
                })
                .catch((error) => {
                    console.log(error);
                    alert("Une erreur est survenue lors de l'ajout de l'article dans le panier")
                })
            },

            async deleteProduct(id){
                var token = localStorage.getItem('token');
                await axios.delete(`http://192.168.56.108:8080/api/products/${id}`,{
                    headers:{
                        'Authorization': "Bearer " + token,
                        'Accept': 'application/json'
                    }
                })
                .then((response) => {
                    console.log(response);
                    alert("Le produit a été supprimer");
                    
                })
                .catch((error) => {
                    console.log(error);
                    alert("Une erreur est survenue lors de la suppression du produit")
                })
            },
        },
        mounted(){
            axios
            .get("http://127.0.0.1:8080/api/products")
            .then((response) => this.setProducts(response.data))
            .catch((error) => console.log(error))
        }
    }
</script>

<style>
.card{
    margin-top: 2%;
    margin-bottom: 2%;
}
.card-img-top{
    height: 300px;
    width: 300px;
    display: flex;
    margin: auto;
    text-align: center;
}
.bouton{
    height: 10%;
    width: 50%;
    margin: auto;
    text-align: center;
}
</style>

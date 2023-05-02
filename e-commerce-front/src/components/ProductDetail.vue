<template>
    <div>
        <h1>Product Detail</h1>
        <div class="container-lg text-center">
            <div class="col">
                <p>Nom du produit : {{ this.product.name }}</p>
                <p>Description du produit : {{ this.product.description }}</p>
                <p>Prix du produit : {{ this.product.price }} €</p>
                <img src="../assets/téléchargement.jpeg" alt=""> <br>
                <router-link class="btn btn-primary bouton" :to="{name: 'editProduct', params:{ id: this.id}}">Modifier le produit</router-link>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    
    data(){
        return {
            id: this.$route.params.id,
            product: []
        }
    },
    methods:{
        setProduct(data){
            this.product = data;
        }
    },
    mounted(){
        var token = localStorage.getItem("item");
        axios.get(`http://192.168.56.108:8080/api/products/${this.id}`,{
            headers:{
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        })
        .then((response) => {
            console.log(response);
            this.setProduct(response.data);
        })
        .catch((error) => {
            console.log(error);
        })
    }
}
</script>

<style>
img{
    margin-bottom: 5%;
}
</style>

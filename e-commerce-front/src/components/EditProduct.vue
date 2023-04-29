<template>
    <div>
           <h1>Modifier le produit un produit</h1>
           <div class="container-sm text-center">
               <div class="col">
                   <div :class="{'alert alert-primary' : !isError, 'alert alert-danger' : isError}" role="alert" v-show="messageShow">
                       {{ messageValue }}
                   </div>
                   <form v-on:submit.prevent="createProduct">
                       <div class="mb-3">
                           <label for="nameInput" class="form-label">Nom du produit</label>
                           <input type="text" class="form-control" id="nameInput" aria-describedby="emailHelp" v-model="name"/>
                       </div>
                       <div class="mb-3">
                           <label for="descriptionInput" class="form-label">Description du produit</label>
                           <input type="text" class="form-control" id="descriptionInput" v-model="description"/>
                       </div>
                       <div class="mb-3">
                           <label for="priceInput" class="form-label">Votre nom</label>
                           <input type="number" class="form-control" id="priceInput" v-model="price"/>
                       </div>
                       <button type="submit" class="btn btn-primary">Modifier le produit</button>
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
                   name: '',
                   description: '',
                   price: 0,
                   messageShow: '',
                   isError: false,
                   messageValue: '',
                   id: this.$route.params.id,
               }
           },
           methods:{
               createProduct: function(){
                   var token = localStorage.getItem('token');
                   if(this.name == "" || this.name == null || this.description == "" || this.description == null || this.price == "" || this.price == null){
                       this.messageShow = true;
                       this.messageValue = "Il faut remplir correctement tous les champs"
                       this.isError = true;
                   }
                   else{
                       axios.put(`http://127.0.0.1:8080/api/products/${this.id}`, {
                           name: this.name,
                           description: this.description,
                           photo: 'path/to/the/picture',
                           price: this.price
                       },{
                           headers:{
                               'Authorization': 'Bearer ' + token,
                               'Accept': 'application/json'
                           }
                       })
                       .then((response) => {
                           console.log(response);
                           this.messageValue = 'Le produit a bien été modifier';
                           this.messageShow = true;
                           this.isError = false;
                       })
                       .catch((error) => {
                           console.log(error);
                           this.messageValue = "Une erreur est survenue lors de la modification du produit";
                           this.messageShow = true;
                           this.isError = true;
                       })
                   }   
               }
           }
       }
   </script>
   
   <style>
   
   </style>
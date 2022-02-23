<template> 
    <div class="row m-3">
        <div class="col-12 col-md-12 col-xxl-6 d-flex order-3 order-xxl-2">
            <div class="card flex-fill w-100">
                <div class="card-body">
                    <!--Btn AJouter-->
                    <div class="card-header">
                        <button class="btn btn-primary btn-sm" title="Ajouter un client" @click="displayModal(0)">
                            <i class="far fa-plus-square"></i>
                        </button>
                        <h5 class="card-title mb-0">Liste de tous les clients</h5>
                    </div>
                    <!--FIn Btn AJouter-->
                    <!--  de tableau-->
                    <div class="table-responsive">
                        <table class="table table-xs mb-0">
                            <thead>
                                <tr>
                                    <th>Avatar</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Civil</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                    <th>Pays</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="element in displayedPosts" :key="element.id">
                                    <td>
                                        <img :src="'/upload/image/'+element.avatar" class="avatar img-fluid rounded me-1"/>
                                    </td>
                                    <td>{{element.lname}}</td>
                                    <td>{{element.fname}}</td>
                                    <td>{{element.genre}}</td>
                                    <td>{{element.email}}</td>
                                    <td>
                                        <span class="badge bg-success" v-if="element.status==2">Active</span>
                                        <span class="badge bg-warning" v-else-if="element.status==1">Attente</span>
                                        <span class="badge bg-danger" v-else>Desactive</span>
                                    </td>
                                    <td>{{element.contry}}</td>
                                    <td>
                                        <a  title="Edit" class="align-middle text-dark" @click="displayModal(element.id)">
                                            <i class="far fa-edit"></i>
                                        </a>&nbsp;
                                        <a href="#" title="Delete" class="align-middle text-dark" @click="deleteCustomers(element.id)">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Fin de tableau-->
                <!--Pagination-->
                <div class="row">
                    <div class="col-md-4 ms-auto">
                        <nav aria-label="PdataTables_paginate paging_simple_numbers">
                            <ul class="pagination">
                                <li class="page-item">
                                    <a class="page-link" v-if="page != 1" @click="page--">
                                        &laquo;
                                    </a>
                                </li>
                                <li class="page-item el"
                                v-for="pageNumber in pages.slice(page - 1, page + 5)"
                                :key="pageNumber.id"
                                @click="page = pageNumber">
                                    <a class="page-link text-dark active">
                                        {{ pageNumber }}
                                    </a>
                                </li>
                                <li class="page-item">
                                   <a @click="page++" v-if="page < pages.length" class="page-link">
                                       &raquo;
                                   </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!--Fin de Pagination-->
            </div>
        </div>
        <Clmodal v-show="isVisible" @close="closeModal()"></Clmodal>
    </div>
</template>
<script>
import Swal from "sweetalert2";
import EventBus from "../eventBus";
import Clmodal from"./Clmodal.vue";
export default {
    data(){
        return{
            posts: [],
            page: 1,
            perPage: 8,
            pages: [],
            isVisible:false,
        }
    },
    mounted(){
        EventBus.$on('reloadCustomers',()=>{
            this.loadCustomers();
        });
    },
    beforeMount(){
        this.loadCustomers();
    },
    methods:{
        async loadCustomers(){
            const response = await fetch('/loadCustomers/');
            const data = await response.json();
            this.posts=data;
        },
        deleteCustomers(id){
            if(id > 0){
                Swal.fire({
                    title: "Suppression",
                    text: "Êtes-vous sûre de vouloir supprimer ce compte client ?",
                    icon: "error",
                    showCancelButton: true,
                    confirmButtonColor: "#3b7ddd",
                    cancelButtonColor: "#495057",
                    confirmButtonText: "OUI",
                    cancelButtonText: "NON",
                }).then((result)=>{
                    if(result.isConfirmed){
                        this._deleteCustomers(id);
                    }else{
                        this.closeModal();
                    }
                });
            }
        },
        async _deleteCustomers(id){
            const response = await fetch('/deleteCustomers/'+id);
            const data = await response.json();
            this.closeModal();
            this.loadCustomers();
            Swal.fire({
                icon: data.statut,
                text: data.msg,
                confirmButtonColor:'#3b7ddd'
            });
        },
        displayModal(id){
            EventBus.$emit('idCustomers',id);
            this.isVisible=true;
        },
        closeModal(){
            this.isVisible=false;
        },
        setPages(){
            let filter=Array();
            let numberOfPages = Math.ceil(this.posts.length / this.perPage);
            for (let index = 1; index <= numberOfPages; index++){
                filter.push(index);
            }
            this.pages=Array.from(new Set(filter));
        },
        paginate(posts){
            let page = this.page;
            let perPage = this.perPage;
            let from = page * perPage - perPage;
            let to = page * perPage;
            return posts.slice(from, to);
        }
    },
    components:{
        Clmodal,EventBus,Swal
    },
     computed:{
         displayedPosts(){
             return this.paginate(this.posts);
         }
     },
    watch: {
        posts() {
        this.setPages();
        }
    }
}
</script>

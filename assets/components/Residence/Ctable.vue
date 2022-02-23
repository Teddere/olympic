<template>
    <div class="row m-3">
            <div class="col-12 col-md-12 col-xxl-6 d-flex order-3 order-xxl-2">
                <div class="card flex-fill w-100">
                    <div class="card-body">
                        <div class="card-header d-flex titre">
                            <div>
                                <button class="btn btn-primary btn-sm" @click="displayVisibleModal(0)">
                                    <i class="far fa-plus-square"></i>
                                </button>
                                <h5 class="card-title mb-0">Ajouter</h5>
                            </div>
                            <div>
                                <button class="btn btn-primary btn-sm" @click="displayVisibleOccupy(2)">
                                <i class="fas fa-align-left"></i>
                                </button>
                                <h5 class="card-title mb-0">Occupées</h5>
                            </div>
                            <div>
                                <button class="btn btn-primary btn-sm" @click="displayVisibleOccupy(0)">
                                    <i class="fas fa-align-right"></i>
                                </button>
                                <h5 class="card-title mb-0">Libres</h5>
                            </div>
                            <div>
                                <button class="btn btn-primary btn-sm" @click="displayVisibleOccupy(1)">
                                    <i class="fas fa-align-right"></i>
                                </button>
                                <h5 class="card-title mb-0">En cours</h5>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-xs mb-0">
                                <thead>
                                    <tr>
                                        <th>Numéro</th>
                                        <th>Nom</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="element in displayedPosts" :key="element.id">
                                        <td>App-0{{element.id}}</td>
                                        <td>{{element.name}}</td>
                                        <td >   
                                            <span class="badge bg-success" v-if="element.statut==0">Libre</span>
                                            <span class="badge bg-warning" v-else-if="element.statut==1">En cours</span>
                                            <span class="badge bg-danger" v-else>Occuper</span>
                                        </td>
                                        <td>
                                            <a href="#" title="Edit" class="align-middle text-dark" @click="displayVisibleModal(element.id)">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <a href="#" title="Delete" class="align-middle text-dark" @click="deleteRoom(element.id)">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination -->
                    <div class="row">
                        <div class="col-md-4 ms-auto">
                            <nav aria-label="PdataTables_paginate paging_simple_numbers">
                                <ul class="pagination">
                                    <li class="page-item">
                                        <a class="page-link"
                                            v-if="page != 1"
                                            @click="page--"
                                        >
                                            &laquo;
                                        </a>
                                    </li>
                                    <li
                                        class="page-item el"
                                        v-for="pageNumber in pages.slice(page - 1, page + 5)"
                                        :key="pageNumber.id"
                                        @click="page = pageNumber"
                                    >
                                        <a class="page-link text-dark active">
                                            {{ pageNumber }}
                                        </a>
                                    </li>
                                    <li>
                                        <a 
                                            class="page-link"
                                            @click="page++"
                                            v-if="page < pages.length"
                                        >
                                            &raquo;
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <Mmodal v-show="isVisibleOccupy" @close="closeModalOccupy"></Mmodal>
            <Romodal v-show="isVisibleModal" @close="closeModal"></Romodal>
        </div>   
</template>
<script>
import Mmodal from "./Mmodal.vue";
import Romodal from "./Romodal.vue";
import EventBus from "../eventBus";
import Swal from "sweetalert2";
export default {
    data(){
        return{
            posts: [],
            page: 1,
            perPage: 5,
            pages: [],
            isVisibleOccupy:false,
            isVisibleModal:false,
        }
    },
    mounted(){
        EventBus.$on('loadRoom',()=>{
            this.loadListReserver();
        });
    },
    beforeMount(){
        this.loadListReserver();
    },
    methods:{
        async loadListReserver(){
            const response = await fetch('/getLoadListReserver');
            const data = await response.json();
            this.posts=data;
        },
        setPages(){
            let filter=Array();
            let numberOfPages = Math.ceil(this.posts.length / this.perPage);
            for (let index = 1; index <= numberOfPages; index++) {
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
        },
        displayVisibleOccupy(status){
            this.isVisibleOccupy=true;
            EventBus.$emit('loadStatus',status);
        },
        closeModalOccupy(){
           this.isVisibleOccupy=false; 
        },
        displayVisibleModal(id){
            EventBus.$emit('id_loadRoom',id);
            this.isVisibleModal=true;
        },
        closeModal(){
            this.isVisibleModal=false;
        },
        deleteRoom(id){
            Swal.fire({
                title: "Suppression",
                text: "Êtes-vous sûre de vouloir supprimer ce chambre ?",
                icon: "error",
                showCancelButton: true,
                confirmButtonColor: "#3b7ddd",
                cancelButtonColor: "#495057",
                confirmButtonText: "OUI",
                cancelButtonText: "NON",
            }).then((result) => {
                if (result.isConfirmed) {
                  this._deleteRoom(id);  
                }else{
                    this.closeModal();
                }
            });
        },
       async _deleteRoom(id){
           const response = await fetch('/setDeleteRoom/'+id);
           const data = await response.json();
           this.loadListReserver();
            Swal.fire({
                icon:data.statut,
                text:data.msg,
                confirmButtonColor:'#3b7ddd'
                });
       } 

    },
    components:{
        Mmodal,EventBus,Romodal,Swal
    },
    computed:{
        displayedPosts() {
            return this.paginate(this.posts);;
        }
    },
    watch: {
        posts() {
            this.setPages();
        }
    },
}
</script>
<style scoped>
    .titre{
        justify-content: space-between;
    }
</style>

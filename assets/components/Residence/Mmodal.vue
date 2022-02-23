<template>
    <div class="modall-backdrop">
        <div class="card" role="dialog" aria-labelledby="modallTitle" aria-describedby="modallDescription">
            <div class="modall">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <header class="modal-header" id="modallTitle">
                            <slot name="header" class="modal-title" v-if="status==1">Chambres occupées</slot>
                            <slot name="header" class="modal-title" v-else>Chambres libres</slot>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeModal()"></button>
                        </header>
                        <section class="modal-body m-2" v-if="displayedPosts.length > 0">
                            <div class="table-responsive">
                                <table class="table table-xs mb-0">
                                    <thead>
                                        <tr>
                                            <th>Numéro</th>
                                            <th>Nom</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="element in displayedPosts" :key="element.id">
                                            <td>App-0{{element.id}}</td>
                                            <td>{{element.name}}</td>
                                            <td>
                                                <span class="badge bg-warning" v-if="element.status==1">En cours</span>
                                                <span class="badge bg-success" v-else-if="element.status==0">Libre</span>
                                                <span class="badge bg-danger" v-else>Occuper</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        <section class="modal-body m-2" v-else>
                            <div v-if="status==1"><h2>Accune chambre est en cours de réservation</h2></div>
                            <div v-else-if="status==2"><h2>Accune chambre est occupée</h2></div>
                            <div v-else><h2>Accune chambre est Libre</h2></div>
                        </section>
                        <div class="row">
                            <div>
                                <div class="col-md-8 ms-auto">
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
                        <div class="text-center m-2">
                            <button class="btn btn-lg  btn-secondary" @click="closeModal()">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import EventBus from "../eventBus";
export default {
    data(){
        return{
            posts: [],
            page: 1,
            perPage: 5,
            pages: [],
            status:null
        }
    },
    mounted(){
        EventBus.$on('loadStatus',(status)=>{
            this.status=status;
            this.loadListOccupation();
        });
    },
    methods:{
        async loadListOccupation(){
            const response = await fetch('/getloadListOccupation/'+this.status);
            const data = await response.json();
            this.posts=data;
        },
        closeModal(){
            this.$emit('close');
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

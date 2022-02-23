<template>
    <div class="row m-3">
        <div class="col-12 col-md-12 col-xxl-6 d-flex order-3 order-xxl-2">
            <div class="card flex-fill w-100">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-xs mb-0">
                            <thead>
                                <tr>
                                    <th>Réservation</th>
                                    <th>Nom du client</th>
                                    <th>Nom de chambre</th>
                                    <th>Statut</th>
                                    <th>Date de départ</th>
                                    <th>Date d'arrivage</th>
                                </tr>
                                <tr v-for="valide in displayedPosts" :key="valide.id">
                                    <td>RE-{{valide.idReser}}</td>
                                    <td>{{valide.idCustomer.lname}} {{valide.idCustomer.fname}}</td>
                                    <td>{{valide.idRoom.name}}</td>
                                    <td>
                                        <span class="badge bg-success" v-if="valide.statut==1">Fin</span>
                                        <span class="badge bg-danger" v-else>Refusé</span>
                                    </td>
                                    <td>{{valide.date_d}}</td>
                                    <td>{{valide.date_r}}</td>
                                </tr>
                            </thead>
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
                                <li class="page-item el"
                                    v-for="pageNumber in pages.slice(page - 1, page + 5)"
                                    :key="pageNumber.id"
                                    @click="page = pageNumber"
                                >
                                    <a class="page-link text-dark active">
                                        {{ pageNumber }}
                                    </a>
                                </li>
                                <li>
                                    <a class="page-link"
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
                <!-- FIn Pagination -->
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return{
            posts: [],
            page: 1,
            perPage: 5,
            pages: [],
        }
    },
    beforeMount(){
        this.validationReservation();
    },
    methods:{
        async validationReservation(){
            const response = await fetch('/validationReservation/');
            const data = await response.json();
            this.posts=data
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
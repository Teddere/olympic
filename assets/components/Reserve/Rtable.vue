<template>
    <div class="row m-3">
        <div class="col-12 col-md-12 col-xxl-6 d-flex order-3 order-xxl-2">
            <div class="card flex-fill w-100">
                <div class="card-body">
                    <div class="card-header">
                        <button class="btn btn-primary btn-sm" title="Ajouter une réservation" @click="displayModalNew()">
                            <i class="far fa-plus-square"></i>
                        </button>
                        <h5 class="card-title mb-0">Liste de toutes les réservations</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-xs mb-0">
                            <thead>
                                <tr>
                                    <th>Numéro</th>
                                    <th>Client</th>
                                    <th>Chambre</th>
                                    <th>Date d'arrive</th>
                                    <th>Date de réservation</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="reserve in displayedPosts" :key="reserve.id">
                                    <td>RE-{{reserve.id}}</td>
                                    <td>{{reserve.name_cl.fname}} {{reserve.name_cl.lname}}</td>
                                    <td>{{reserve.name_room.name}}</td>
                                    <td>{{reserve.date_ar}}</td>
                                    <td>{{reserve.date_re}}</td>
                                    <td>
                                        <span class="badge bg-warning" v-if="reserve.statut==1">Attente</span>
                                        <span class="badge bg-success" v-else-if="reserve.statut==2">Validé</span>
                                    </td>
                                    <td>
                                        <!-- Statut sur l'état de modification des données -->
                                        <a class="align-middle text-dark" title="Validation" @click="validationReserve(reserve.id,reserve.name_cl.id,reserve.name_room.id)" v-if="reserve.statut==1">
                                            <i class="far fa-question-square"></i> 
                                        </a>
                                        <a class="align-middle text-dark" title="Validation" v-else @click="confirmReserve(reserve.id,reserve.name_cl.id,reserve.name_room.id)">
                                            <i class="far fa-check-square"></i>
                                        </a>
                                        <!-- Fin Statut sur l'état de modification des données -->
                                        <a class="align-middle text-dark" title="Modifier la date d'arriver" @click="displayModalUp(reserve.id)">
                                            <i class="far fa-edit"></i>
                                        </a>
                                        <a class="align-middle text-dark" title="Suppression" @click="deleteReservation(reserve.id)">
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
        <Rmodal v-show="isVisibleNew" @close="closeModalNew()"></Rmodal>
        <Emodal v-show="isVisibleUp" @close="closeModalUp()"></Emodal>
    </div>    
</template>
<script>
import Rmodal from"./Rmodal.vue";
import Emodal from"./Emodal.vue";
import EventBus from "../eventBus";
import Swal from "sweetalert2";
export default {
    data(){
        return{
            posts: [],
            page: 1,
            perPage: 5,
            pages: [],
            isVisibleNew:false,
            isVisibleUp:false
        }
    },
    mounted(){
        EventBus.$on("reloadReserver",()=>{
            this.loadReservation();
        })
    },
    beforeMount(){
        this.loadReservation();
    },
    methods:{
        async loadReservation(){
            const response = await fetch('/loadReservation/');
            const data = await response.json();
            this.posts=data;
        },
        confirmReserve(id,idClient,idChambre){
            Swal.fire({
                title: "Confirmation de fin de séjours",
                text: 'Vouliez-vous valider la fin de ce séjurs',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#3b7ddd",
                cancelButtonColor: "#495057",
                confirmButtonText: "OUI",
                cancelButtonText: "NON",
            }).then((result) => {
                if (result.isConfirmed) {
                    this._confirmReserve(id,idClient,idChambre);
                }
            });
        },
        async _confirmReserve(id,id_c,id_r){
            const dataForm={
                id:id,
                idCustomer:id_c,
                idRoom:id_r
            }
            const dataHeaders={
                method:"POST",
                Headers:{'content-Type':'application/json'},
                body:JSON.stringify(dataForm)
            }
            const response = await fetch('/confirmReserve/',dataHeaders);
            const data = await response.json();
            this.loadReservation();
            Swal.fire({
                title:data.msg,
                icon:data.statut,
                confirmButtonColor: '#3b7ddd',
            });
            
        },
        validationReserve(idReserve,idCustomer,idRoom){
            Swal.fire({
                title: 'Vouliez-vous valider cette réservation ?',
                icon: 'question',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Valider',
                denyButtonText: 'Réfuser',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#3b7ddd',
                denyButtonColor:'#d33',
                cancelButtonColor: '#495057'
            }).then((result)=>{
                if (result.isConfirmed){
                    this._validationReserve(idReserve,idCustomer,idRoom,2)
                }else if(result.isDenied){
                   this._validationReserve(idReserve,idCustomer,idRoom,4) 
                }
            });
        },
        async _validationReserve(id,cl,ch,statut){
            const dataFrom={
                "id":id,
                "idCustomer":cl,
                "idRoom":ch,
                "status":statut
            }
            const dataHeaders={
                method:'POST',
                Headers:{'content-Type':'application/json'},
                body:JSON.stringify(dataFrom)
            }
            const response = await fetch('/validatimSimple/',dataHeaders);
            const data = await response.json();
            EventBus.$emit('updateReservation');
            this.loadReservation();
            if(data.detail){
                Swal.fire({
                icon:data.statut,
                title:data.msg,
                text:data.detail,
                confirmButtonColor: '#3b7ddd',
                });   
            }else{

                Swal.fire({
                    title:data.msg,
                    icon:data.statut,
                    confirmButtonColor: '#3b7ddd',
                });
            }
        },
        deleteReservation(id){
            Swal.fire({
                title: "Suppression",
                text: 'Vouliez-vous supprimer cette réservation ?',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: "#3b7ddd",
                cancelButtonColor: "#495057",
                confirmButtonText: "OUI",
                cancelButtonText: "NON",
            }).then((result) => {
                if (result.isConfirmed) {
                    this._deleteReservation(id);
                }
            });
        },
        async _deleteReservation(id){
            const response = await fetch('/deleteReservation/'+id);
            const data = await response.json();
            this.loadReservation();
            Swal.fire({
                title:data.msg,
                icon:data.statut,
                confirmButtonColor: "#3b7ddd",
            });
        },
        displayModalUp(id){
            this.isVisibleUp=true;
            EventBus.$emit("idLoadReserve",id);
        },
        displayModalNew(){
            this.isVisibleNew=true;
        },
        closeModalUp(){
            this.isVisibleUp=false;
        },
        closeModalNew(){
            this.isVisibleNew=false;
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
    components:{
        Rmodal,EventBus,Swal,Emodal
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
